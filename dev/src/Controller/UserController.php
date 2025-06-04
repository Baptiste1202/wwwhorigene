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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class UserController extends AbstractController
{
    public function __construct(
        #[Autowire(service: UserRepository::class)]
        private UserRepositoryInterface $userRepository,
    ) {
    }

    #[Route(path: 'page_users', name: 'page_users')]
    public function showPage(Request $request, EntityManagerInterface $em): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('user/main.html.twig', ['users' => $users]);
    }

    #[Route('user/edit/{id}', name: 'edit_user')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        //Create the form
        $userForm = $this->createForm(UserFormType::class, $user);

        //treat the request
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            //generate the slug

            //stock data
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user ' . $user->getFirstname() . $user->getLastname(). ' modified with succes !');

            return $this->redirectToRoute('page_users');
        }
        return $this->render('user/edit.html.twig', compact('userForm'));
    }

    #[Route('strains/user/delete/{id}', name: 'delete_user')]
    public function delete(user $user, EntityManagerInterface $em): Response
    {
        /*
        if ($vehicule) {
            $this->denyAccessUnlessGranted('vehicule.is_creator', $vehicule);
        }
        */

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'user ' . $user->getFirstname() . $user->getLastname() . ' delete with success !');

        return $this->redirectToRoute('page_users');
    }

}