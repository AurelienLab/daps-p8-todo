<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table('user')]
#[ORM\Entity]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'global')]
#[UniqueEntity('email')]
#[UniqueEntity('username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'string', length: 25, unique: true)]
    private string $username;

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[ORM\Column(type: 'string', length: 64)]
    private ?string $password;

    #[ORM\Column(type: 'string', length: 60, unique: true)]
    #[Assert\NotBlank(message: "Vous devez saisir une adresse email.")]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    private string $email;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'author')]
    private Collection $tasks;


    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getUsername(): string
    {
        return $this->username;
    }


    public function setUsername($username): self
    {
        $this->username = $username;
        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function eraseCredentials(): void
    {
    }


    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }


    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }


    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAuthor($this);
        }

        return $this;
    }


    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAuthor() === $this) {
                $task->setAuthor(null);
            }
        }

        return $this;
    }


}
