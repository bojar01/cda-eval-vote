<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $election_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $election_end = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $application_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $application_end = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session_id = null;

    #[ORM\OneToMany(mappedBy: 'vote', targetEntity: Candidate::class)]
    private Collection $candidates;

    #[ORM\OneToMany(mappedBy: 'vote', targetEntity: UserVote::class)]
    private Collection $userVotes;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
        $this->userVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getElectionStart(): ?\DateTimeInterface
    {
        return $this->election_start;
    }

    public function setElectionStart(\DateTimeInterface $election_start): static
    {
        $this->election_start = $election_start;

        return $this;
    }

    public function getElectionEnd(): ?\DateTimeInterface
    {
        return $this->election_end;
    }

    public function setElectionEnd(\DateTimeInterface $election_end): static
    {
        $this->election_end = $election_end;

        return $this;
    }

    public function getApplicationStart(): ?\DateTimeInterface
    {
        return $this->application_start;
    }

    public function setApplicationStart(\DateTimeInterface $application_start): static
    {
        $this->application_start = $application_start;

        return $this;
    }

    public function getApplicationEnd(): ?\DateTimeInterface
    {
        return $this->application_end;
    }

    public function setApplicationEnd(\DateTimeInterface $application_end): static
    {
        $this->application_end = $application_end;

        return $this;
    }

    public function getSessionId(): ?Session
    {
        return $this->session_id;
    }

    public function setSessionId(?Session $session_id): static
    {
        $this->session_id = $session_id;

        return $this;
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): static
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates->add($candidate);
            $candidate->setVote($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            // set the owning side to null (unless already changed)
            if ($candidate->getVote() === $this) {
                $candidate->setVote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserVote>
     */
    public function getUserVotes(): Collection
    {
        return $this->userVotes;
    }

    public function addUserVote(UserVote $userVote): static
    {
        if (!$this->userVotes->contains($userVote)) {
            $this->userVotes->add($userVote);
            $userVote->setVote($this);
        }

        return $this;
    }

    public function removeUserVote(UserVote $userVote): static
    {
        if ($this->userVotes->removeElement($userVote)) {
            // set the owning side to null (unless already changed)
            if ($userVote->getVote() === $this) {
                $userVote->setVote(null);
            }
        }

        return $this;
    }
}
