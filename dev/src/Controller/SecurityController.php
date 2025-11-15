<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
    
        if ($user) {
            if (!$user->isAccess()) {
                // Déconnexion automatique si l'accès est interdit
                dd($container);
                $this->container->get('security.token_storage')->setToken(null);
                $this->container->getSession()->invalidate();

                return $this->redirectToRoute('app_login', [
                    'error' => 'Votre compte n’a pas l’autorisation d’accéder au site.'
                ]);
            }

            return $this->redirectToRoute('page_strains');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
