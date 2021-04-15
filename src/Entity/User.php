<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\UserDto;
use App\Dto\UserExtendedDto;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class User implements UserInterface
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $firstName = '';

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $lastName = '';

    /**
     * Class Invariant:
     * Every user has a valid email and at least one valid role
     */
    public function __construct(string $email, array $roles = ['ROLE_USER'])
    {
        $this->email = $email;
        $this->setRoles($roles);
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->id->__toString();
    }

    public function getEmail(): ?string
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
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return \sprintf('%s %s', \trim($this->getFirstName()), \trim($this->getLastName()));
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public static function createFromRequest(UserDto $dto): self
    {
        $user = new self($dto->getEmail());
        $user->update($dto);

        return $user;
    }

    public function update(UserDto $dto)
    {
        $this->email = $dto->getEmail();
        $this->firstName = $dto->getFirstName();
        $this->lastName = $dto->getLastName();

        if ($dto instanceof UserExtendedDto) {
            $this->setDeletedAt($dto->isEnabled() ? null : new DateTime());
            $this->roles = [$dto->getRole()];
        }
    }

    private function setRoles(array $roles): self
    {
        $this->roles = \array_unique($roles);

        return $this;
    }
}
