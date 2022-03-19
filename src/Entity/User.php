<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete'],
    attributes: [
        'normalization_context' => ['groups' => ['user:read']],
        'denormalization_context' => ['groups' => ['user:write']],
    ],
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'firstName' => 'word_start',
    'lastName' => 'word_start',
    'email' => 'exact',
    'phone' => 'exact',
    'type' => 'start',
    'userGroup' => 'exact',
])]
#[UniqueEntity('email')]
#[UniqueEntity('phone')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(["user:write", "user:read"])]
    #[Assert\NotBlank]
    #[Assert\Type("alpha")]
    private $firstName;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Type("alpha")]
    #[Groups(["user:write", "user:read"])]
    private $lastName;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(["user:write", "user:read"])]
    private $email;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank]
    #[Assert\Regex("/^(0)([0-9]){9}$/i")]
    #[Groups(["user:write", "user:read"])]
    private $phone;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Type("integer")]
    #[Groups(["user:write", "user:read"])]
    private $age;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    #[Groups(["user:write", "user:read"])]
    private $type;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[Groups(["user:read"])]
    private $userGroup;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUserGroup(): ?Group
    {
        return $this->userGroup;
    }

    public function setUserGroup(?Group $userGroup): self
    {
        $this->userGroup = $userGroup;

        return $this;
    }
}
