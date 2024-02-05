<?php

namespace App\Security\Voter;

use App\Entity\Advertisement;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAdvertisementVoter extends Voter
{
    public const EDIT = 'EDIT_ADVERTISEMENT';
    public const DELETE = 'DELETE_ADVERTISEMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Advertisement;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // If the user is the owner of the advertisement, grant access
        if ($subject->getOwner() === $user) {
            return true;
        }

        // By default, deny access
        return false;
    }
}
