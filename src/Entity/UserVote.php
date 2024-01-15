<?php

namespace App\Entity;

use App\Repository\UserVoteRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserVoteRepository::class)]
class UserVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vote_time = null;

    #[ORM\ManyToOne(inversedBy: 'userVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vote $vote = null;

    #[ORM\ManyToOne(inversedBy: 'userVotes')]
    private ?User $user = null;

    function __construct(){
        $this->vote_time = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getVoteTime(): ?\DateTimeInterface
    {
        return $this->vote_time;
    }

    public function setVoteTime(\DateTimeInterface $vote_time): static
    {
        $this->vote_time = $vote_time;

        return $this;
    }

    public function getVote(): ?Vote
    {
        return $this->vote;
    }

    public function setVote(?Vote $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
