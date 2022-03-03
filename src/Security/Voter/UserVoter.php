<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['MANAGE'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'MANAGE':
                return $this->canManage($user, $subject);
        }

        return false;
    }

    /**
     * Checks that the current user is part of the same client as the subject
     */
    private function canManage($user, $subject): bool
    {
        return $user->getClient() === $subject->getClient();
    }
}
