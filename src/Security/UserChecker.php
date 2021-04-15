<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Exception\UserNotEnabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isDeleted()) {
            throw new UserNotEnabledException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
