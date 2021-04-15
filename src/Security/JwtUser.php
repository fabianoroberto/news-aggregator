<?php

declare(strict_types=1);

namespace App\Security;

use Assert\Assertion;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUser implements UserInterface, JWTUserInterface
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles = [];

    public static function createFromPayload($username, array $payload)
    {
        Assertion::notEmpty($payload['roles'], 'Missing roles field in Token');

        $user = new self();
        $user->email = $username;
        $user->roles = $payload['roles'];

        return $user;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
