<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert ;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'contacts')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    use Timestampable ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Please enter your first name")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please enter your last name")]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Assert\NotBlank()]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:20 )]
    private ?string $message = null;

    #[ORM\Column(nullable: true, options:["default" => false])]
    private ?bool $seen = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): static
    {
        $this->seen = $seen;

        return $this;
    }

    // #[ORM\PostLoad]
    // public function postLoad()
    // {
    //     $this->setSeen(true);
    // }
}
