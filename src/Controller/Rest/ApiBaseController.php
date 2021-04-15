<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Entity\User;
use App\Security\JwtUser;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ApiBaseController extends AbstractFOSRestController
{
    public function getJwtUser()
    {
        $user = $this->getUser();

        if ($user !== null && !($user instanceof JwtUser)) {
            throw new \Exception('User is not authenticated with a valid Jwt Token');
        }

        return $user;
    }

    /**
     * @throws \Exception
     */
    protected function getLoggedUser(): ?User
    {
        $jwtUser = $this->getJwtUser();

        if ($jwtUser === null) {
            return null;
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $jwtUser->getEmail()]);

        if ($user === null) {
            throw new \Exception("User not found with username: {$jwtUser->getEmail()}");
        }

        return $user;
    }
}
