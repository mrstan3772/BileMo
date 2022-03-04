<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\UniqueEmail;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    public const ROLES = [
        'ROLE_USER',
        'ROLE_ADMIN'
    ];

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(
        groups: ['create'],
        message: 'The email {{ value }} cannot be blank',
    )]
    #[Assert\Email(
        groups: ['create'],
        message: 'The email {{ value }} is not a valid email address',
    )]
    #[UniqueEmail(
        groups: ['create'],
    )] 
    private string $email;

    #[ORM\Column(type: 'json')]
    #[Assert\Type(
        type: ['array'],
        message: 'The roles sould be a valid array',
    )]
    #[Assert\All(
        new Assert\Choice(
            groups: ['create'],
            choices: User::ROLES,
            message: 'You must provid a valid user role. Roles available: {{ choices }}'
        )
    )]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(
        groups: ['create'],
        message: 'The email {{ password }} cannot be blank',
    )]
    #[Assert\Regex(
        groups: ['create'],
        pattern: '/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}/',
        message: 'For security reasons, your password must contain at least 12 characters, including 1 lowercase letter, 1 uppercase letter, 1 number and a special character (random order)',
    )]
    private string $password;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Type(
        groups: ['create'],
        type: ['int'],
        message: 'The phone number should be a valid number',
    )]
    #[Assert\Length(
        groups: ['create'],
        max: 15,
        message: 'The phone number cannot exceed {{ limit }} characters',
    )]
    private ?int $phoneNumber;

    #[ORM\Column(type: 'string', length: 90)]
    #[Assert\NotBlank(
        groups: ['create'],
        message: 'The user fullname cannot be blank',
    )]
    #[Assert\Length(
        groups: ['create'],
        max: 90,
        message: 'The fullname cannot exceed {{ limit }} characters',
    )]
    private $fullname;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'users', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private int|Client $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}