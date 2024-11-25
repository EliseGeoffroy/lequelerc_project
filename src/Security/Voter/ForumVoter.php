<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ForumVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const RATE = 'RATE';


    public function __construct(private Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::RATE])
            && $subject instanceof \App\Entity\Question;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();


        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if ($user == $subject->getAuthor()) {
                    return true;
                }
                break;

            case self::DELETE:
                if (($user == $subject->getAuthor()) || ($this->security->isGranted('ROLE_ADMIN'))) {
                    return true;
                }
                break;

            case self::RATE:
                if (($user != $subject->getAuthor()) && (!$subject->getVoters()->contains($user))) {
                    return true;
                }
                break;
        }

        return false;
    }
}
