<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'global')]
#[ORM\Table('task')]
class Task
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Vous devez saisir un titre.')]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $isDone;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $author = null;


    public function __construct()
    {
        $this->createdAt = new Datetime();
        $this->isDone = false;
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }


    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    public function getTitle(): string
    {
        return $this->title;
    }


    public function setTitle($title): void
    {
        $this->title = $title;
    }


    public function getContent(): string
    {
        return $this->content;
    }


    public function setContent($content): void
    {
        $this->content = $content;
    }


    public function setIsDone(bool $isDone): void
    {
        $this->isDone = $isDone;
    }


    public function isDone(): bool
    {
        return $this->isDone;
    }


    public function toggle($flag): void
    {
        $this->isDone = $flag;
    }


    public function getAuthor(): ?User
    {
        return $this->author;
    }


    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }


}
