<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

class AppUserProvider implements UserProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        // BLOCAGE : si pas d'accès, lever exception AVANT authentification
        if (!$user->isAccess()) {
            throw new AuthenticationException(
                'Votre compte n\'a pas l\'autorisation d\'accéder au site.'
            );
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new AuthenticationException('Invalid user class.');
        }

        // Revérifier à chaque refresh (importante pour les sessions)
        if (!$user->isAccess()) {
            throw new AuthenticationException(
                'Votre compte n\'a pas l\'autorisation d\'accéder au site.'
            );
        }

        return $this->loadUserByIdentifier($user->getEmail());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}