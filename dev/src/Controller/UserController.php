<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Strain; 
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function __construct(
        #[Autowire(service: UserRepository::class)]
        private UserRepositoryInterface $userRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {}

    #[Route(path: 'page_users', name: 'page_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function showPage(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $allUsers = $this->userRepository->findAll(10000);

        return $this->render('user/main.html.twig', [
            'users'    => $allUsers,
        ]);
    }

    #[Route('user/edit/{id}', name: 'edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // À l’édition, le mot de passe est optionnel
        $form = $this->createForm(UserFormType::class, $user, [
            'password_required' => false,
            'is_update'         => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ne hasher que si un nouveau password est saisi
            $plain = $form->has('plainPassword') ? (string) $form->get('plainPassword')->getData() : '';
            if ($plain !== '') {
                $hashed = $passwordHasher->hashPassword($user, $plain);
                $user->setPassword($hashed);
            }

            $em->flush();

            $this->addFlash('success', sprintf(
                'User %s %s modified successfully!',
                (string) $user->getFirstname(),
                (string) $user->getLastname()
            ));

            return $this->redirectToRoute('page_users');
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $form->createView()
        ]);
    }

    #[Route('strains/user/delete/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        // Récupère les souches liées à cet utilisateur (FK createdBy)
        $strains   = $em->getRepository(Strain::class)->findBy(['createdBy' => $user]);
        $strainIds = array_map(fn(Strain $s) => $s->getId(), $strains);

        if (count($strainIds) > 0) {
            $this->addFlash(
                'error',
                'Cannot delete this user because it is associated with the following strain IDs: ' . implode(', ', $strainIds) . '.'
            );
        } else {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User "' . $user->getFirstname() . ' ' . $user->getLastname() . '" has been successfully deleted!');
        }

        return $this->redirectToRoute('page_users');
    }

    #[Route('user/access/{id}', name: 'access_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function access(User $user, EntityManagerInterface $em): Response
    {
        if ($user->isAccess()) {
            $user->setAccess(false);
            $em->flush();
            $this->addFlash('success', 'User "' . $user->getFirstname() . ' ' . $user->getLastname() . '" dont have access anymore!');
            return $this->redirectToRoute('page_users');
        }

        $user->setAccess(true);
        $em->flush();   
        $this->addFlash('success', 'User "' . $user->getFirstname() . ' ' . $user->getLastname() . '" have access!');
        return $this->redirectToRoute('page_users');
    }

    #[Route('/users/delete-multiple', name: 'delete_multiple_users', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteMultiple(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $ids = $request->request->all('selected_users');

        if (!is_array($ids) || empty($ids)) {
            $this->addFlash('error', 'No user selected.');
            return $this->redirectToRoute('page_users');
        }

        $users = $em->getRepository(User::class)->findBy(['id' => $ids]);

        if (!$users) {
            $this->addFlash('error', 'No user found for deletion.');
            return $this->redirectToRoute('page_users');
        }

        $detailsDeleted = [];
        $detailsBlocked = [];

        foreach ($users as $u) {
            // Option : bloquer la suppression de soi-même
            if ($security->getUser() && $security->getUser()->getId() === $u->getId()) {
                $detailsBlocked[] = sprintf('[ID: %d - %s %s] (current account)', $u->getId(), (string) $u->getFirstname(), (string) $u->getLastname());
                continue;
            }

            // 🔒 Bloque si des Strain référencent cet utilisateur (FK createdBy)
            $strains   = $em->getRepository(Strain::class)->findBy(['createdBy' => $u]);
            $strainIds = array_map(fn(Strain $s) => $s->getId(), $strains);

            if (!empty($strainIds)) {
                $detailsBlocked[] = sprintf(
                    '[ID: %d - %s %s → Linked Strains: %s]',
                    $u->getId(),
                    (string) $u->getFirstname(),
                    (string) $u->getLastname(),
                    implode(', ', $strainIds)
                );
                continue;
            }

            // ✅ OK pour suppression
            $detailsDeleted[] = sprintf('[ID: %d - %s %s]', $u->getId(), (string) $u->getFirstname(), (string) $u->getLastname());
            $em->remove($u);
        }

        if (!empty($detailsDeleted)) {
            $em->flush();
            $this->addFlash('success', sprintf(
                '%d user(s) successfully deleted: %s',
                count($detailsDeleted),
                implode(', ', $detailsDeleted)
            ));
        }

        if (!empty($detailsBlocked)) {
            $this->addFlash('error',
                'Unable to delete some user(s) because they are linked to strains or are the current account: ' . implode(', ', $detailsBlocked)
            );
        }

        return $this->redirectToRoute('page_users');
    }
}
