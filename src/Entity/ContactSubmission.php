<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ContactSubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactSubmissionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            normalizationContext: ['groups' => ['contact_submission:created']]
        ),
    ]
)]


class ContactSubmission
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['contact_submission:list', 'contact_submission:created'])]
    private ?Uuid $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/<[^>]+>/',
        message: 'HTML tags are not allowed.',
        match: false,
)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(mode: 'html5')]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/<[^>]+>/',
        message: 'HTML tags are not allowed.',
        match: false,
    )]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\IsTrue]
    private ?bool $consent = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isConsent(): ?bool
    {
        return $this->consent;
    }

    public function setConsent(bool $consent): static
    {
        $this->consent = $consent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
