<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\State\Processor\UserPasswordHasher;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(normalizationContext: ['groups' => ['user:read']]),
        new Post(
            denormalizationContext: ['groups' => ['user:create']],
            processor: UserPasswordHasher::class),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';

    #[Groups(['user:read'])]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    public Uuid $id {
        get {
            return $this->id;
        }
    }

    #[Groups(['user:read', 'user:create', 'user:update'])]
    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    private string $email = '';

    /**
     * @var list<string> The user roles
     */
    #[Groups(['user:read'])]
    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank, Assert\Type('array')]
    private array $roles = [self::ROLE_USER];

    #[ORM\Column]
    // #[Assert\NotBlank(message: 'Please enter a password')]
    // #[Assert\Length(min: 6, minMessage: 'Your password should be at least {{ limit }} characters')]
    private string $password = '';

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * @psalm-api
     *
     * @param list<string> $roles
     */
    public static function create(string $email, string $password, array $roles = [self::ROLE_USER]): self
    {
        $user = new self();
        $user->email = $email;
        $user->password = $password;
        $user->roles = $roles;

        return $user;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /** @psalm-api */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /** @psalm-suppress UnusedMethod */
    public function eraseCredentials(): void
    {
    }
}
