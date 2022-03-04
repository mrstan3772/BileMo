<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\PhoneRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
class Phone
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 45)]
    #[Assert\NotBlank(
        message: 'The name cannot be blank',
    )]
    #[Assert\Length(
        max: 45,
        message: 'The name cannot exceed {{ limit }} characters',
    )]
    private string $name;

    #[ORM\Column(type: 'string', length: 40)]
    #[Assert\NotBlank(
        message: 'The reference cannot be blank',
    )]
    #[Assert\Length(
        max: 40,
        message: 'The reference cannot exceed {{ limit }} characters',
    )]
    private $reference;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\NotBlank(
        message: 'The brand cannot be blank',
    )]
    #[Assert\Length(
        max: 60,
        message: 'The brand cannot exceed {{ limit }} characters',
    )]
    private string $brand;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Type(
        type: ['float', 'int'],
        message: 'The price should be a valid number',
    )]
    #[Assert\Positive(
        message: 'The price should be positive and greater than 0',
    )]
    private ?float $price;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 2500,
        message: 'The description cannot exceed {{ limit }} characters',
    )]
    private ?string $description;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
