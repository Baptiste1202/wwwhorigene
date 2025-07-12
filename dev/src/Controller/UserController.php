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
    #[IsGranted('ROLE_ADMIN')]
    public function showPage(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $users = $this->userRepository->findAll(10000); 

        return $this->render('user/main.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('user/edit/{id}', name: 'edit_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        $userForm = $this->createForm(UserFormType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user ' . $user->getFirstname() . $user->getLastname(). ' modified with succes !');

            return $this->redirectToRoute('page_users');
        }
        return $this->render('user/edit.html.twig', compact('userForm'));
    }

    #[Route('strains/user/delete/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(user $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'user ' . $user->getFirstname() . $user->getLastname() . ' delete with success !');

        return $this->redirectToRoute('page_users');
    }
}
