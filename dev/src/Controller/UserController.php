<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class UserController extends AbstractController
{
    public function __construct(
        #[Autowire(service: UserRepository::class)]
        private UserRepositoryInterface $userRepository,
        private PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder
    ) {
    }

    #[Route(path: 'page_users', name: 'page_users')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $userForm = $this->createForm(UserFormType::class);

        if ($security->isGranted('ROLE_ADMIN')) {
            $userForm = $this->addForm($request, $em);
        }

        $users = $this->userRepository->findAll(10000); // Meme logique que Plasmyd

        return $this->render('user/main.html.twig', [
            'userForm' => $userForm,
            'users' => $users,
        ]);
    }

    #[Route(path: 'strains/user/ajout', name: 'add_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function addForm(Request $request, EntityManagerInterface $em): Form
    {
        $user = new User();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User ' . $user->getFirstname() . ' added successfully!');
        }

        return $form;
    }

    #[Route(path: 'strains/user/edit/{id}', name: 'edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User ' . $user->getFirstname() . ' updated successfully!');
            return $this->redirectToRoute('page_users');
        }

        return $this->render('user/edit.html.twig', compact('form'));
    }

    #[Route(path: 'strains/user/delete/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        // Suppression directe sans dépendance ici (ajuster si besoin)
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('page_users');
    }
}
