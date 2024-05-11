<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\VacancyStatus;
use App\Repository\VacancyRepository;
use App\Validator\IsList;
use App\Validator\ScalarType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VacancyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    routePrefix: '/api',
    security: 'is_granted("ROLE_USER")',
    normalizationContext: ['groups' => ['read']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            denormalizationContext: ['groups' => ['create']],
        ),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    // The following is very important: it removes ugly bad request errors
    // when type checks fail in Serializer and instead passes them to Validator
    // which constructs proper violations with 422 response
    collectDenormalizationErrors: true,
)]
class Vacancy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Groups(['read', 'create'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['read', 'create'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Serializer\Groups(['read', 'create'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\Groups(['read', 'create'])]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255)]
    #[Serializer\Groups(['read', 'create'])]
    private VacancyStatus $status = VacancyStatus::Draft;

    #[ORM\ManyToOne(inversedBy: 'curatedVacancies')]
    #[Serializer\Groups(['read', 'create'])]
    private ?User $manager = null;

    #[ORM\Column]
    #[Assert\NotBlank, Assert\Positive, Assert\Type('integer')]
    #[Serializer\Groups(['read', 'create'])]
    private ?int $minBudget = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(
        propertyPath: 'minBudget',
        message: 'This value should be greater than or equal {{ compared_value_path }} ({{ compared_value }}).'
    )]
    #[Assert\Type('integer')]
    #[Serializer\Groups(['read', 'create'])]
    private ?int $maxBudget = null;

    /**
     * @var list<string> $requirements
     */
    #[ORM\Column(nullable: true, type: Types::JSON)]
    #[IsList(type: ScalarType::String)]
    #[Serializer\Groups(['read', 'create'])]
    private ?array $requirements = null;

    #[ORM\ManyToOne(inversedBy: 'createdVacancies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['read'])]
    private ?User $createdBy = null;

    #[ORM\Column]
    #[Serializer\Groups(['read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Serializer\Groups(['read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getStatus(): ?VacancyStatus
    {
        return $this->status;
    }

    public function setStatus(VacancyStatus|string $status): static
    {
        if ($status instanceof VacancyStatus) {
            $this->status = $status;

            return $this;
        }

        $transformedStatus = VacancyStatus::tryFrom($status);
        if (null === $transformedStatus) {
            throw new \RuntimeException('Unknown vacancy status: '.$status);
        }

        $this->status = $transformedStatus;

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getMinBudget(): ?int
    {
        return $this->minBudget;
    }

    public function setMinBudget(int $minBudget): static
    {
        $this->minBudget = $minBudget;

        return $this;
    }

    public function getMaxBudget(): ?int
    {
        return $this->maxBudget;
    }

    public function setMaxBudget(?int $maxBudget): static
    {
        $this->maxBudget = $maxBudget;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRequirements(): ?array
    {
        return $this->requirements;
    }

    /**
     * @param list<string> $requirements
     */
    public function setRequirements(?array $requirements): static
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function addRequirement(string $requirement): static
    {
        $existingIndex = array_search($requirement, $this->requirements ?? []);
        if (false !== $existingIndex) {
            $this->requirements[] = $requirement;
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

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
}
