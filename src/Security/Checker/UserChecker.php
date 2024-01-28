<?php

namespace App\Security\Checker;

use App\Entity\User as AppUser;
use App\Enum\StatusEnum;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getStatus() === StatusEnum::BLOCKED) {
            throw new CustomUserMessageAccountStatusException('Your user is blocked, contact your admin to unblock.');
        }
    }
}
