<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use App\State\ProfileProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute as Serializer;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/me.{_format}',
            provider: ProfileProvider::class,
        ),
        new Get(
            // Explicit URI Template is needed to allow API Platform to use
            // this path for IRI generation instead of `/api/users/me`. This
            // also applies to the following GetCollection configuration
            uriTemplate: '/users/{id}.{_format}',
        ),
        new GetCollection(
            itemUriTemplate: '/users/{id}.{_format}',
        ),
    ],
    security: 'is_granted("ROLE_USER")',
    routePrefix: '/api'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[ApiProperty(security: 'object == user')]
    private ?string $password = null;

    #[Serializer\Ignore]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Vacancy>
     */
    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Vacancy::class)]
    private Collection $curatedVacancies;

    /**
     * @var Collection<int, Vacancy>
     */
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Vacancy::class)]
    private Collection $createdVacancies;

    public function __construct()
    {
        $this->curatedVacancies = new ArrayCollection();
        $this->createdVacancies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[Serializer\Ignore]
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     *
     * @psalm-return non-empty-array<int, string>
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $baseRole = 'ROLE_USER';

        return array_unique(array_merge($this->roles, [$baseRole]));
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
    public function getPassword(): ?string
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

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getCuratedVacancies(): Collection
    {
        return $this->curatedVacancies;
    }

    public function addCuratedVacancy(Vacancy $curatedVacancy): static
    {
        if (!$this->curatedVacancies->contains($curatedVacancy)) {
            $this->curatedVacancies->add($curatedVacancy);
            $curatedVacancy->setManager($this);
        }

        return $this;
    }

    public function removeCuratedVacancy(Vacancy $curatedVacancy): static
    {
        if ($this->curatedVacancies->removeElement($curatedVacancy)) {
            // set the owning side to null (unless already changed)
            if ($curatedVacancy->getManager() === $this) {
                $curatedVacancy->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getCreatedVacancies(): Collection
    {
        return $this->createdVacancies;
    }

    public function addCreatedVacancy(Vacancy $createdVacancy): static
    {
        if (!$this->createdVacancies->contains($createdVacancy)) {
            $this->createdVacancies->add($createdVacancy);
            $createdVacancy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedVacancy(Vacancy $createdVacancy): static
    {
        if ($this->createdVacancies->removeElement($createdVacancy)) {
            // set the owning side to null (unless already changed)
            if ($createdVacancy->getCreatedBy() === $this) {
                $createdVacancy->setCreatedBy(null);
            }
        }

        return $this;
    }
}
