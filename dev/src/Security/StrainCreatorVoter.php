<?php 
namespace App\Security;

use App\Entity\Strain;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StrainCreatorVoter extends Voter{

    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'strain.is_creator' == $attribute && $subject instanceof Strain; 
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User){
            return false; 
        }

        return $user == $subject->getUserCreator();
    }

}