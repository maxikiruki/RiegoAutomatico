<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectorRepository")
 */
class Sector
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valve;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $humedity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $flowmeter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sectors")
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\State", mappedBy="sector", cascade={"persist", "remove"})
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="sector")
     */
    private $histories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Schedule", inversedBy="sector")
     */
    private $schedule;

    public function __construct()
    {
        $this->histories = new ArrayCollection();
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

    public function getValve(): ?string
    {
        return $this->valve;
    }

    public function setValve(?string $valve): self
    {
        $this->valve = $valve;

        return $this;
    }

    public function getHumedity(): ?string
    {
        return $this->humedity;
    }

    public function setHumedity(?string $humedity): self
    {
        $this->humedity = $humedity;

        return $this;
    }

    public function getFlowmeter(): ?string
    {
        return $this->flowmeter;
    }

    public function setFlowmeter(?string $flowmeter): self
    {
        $this->flowmeter = $flowmeter;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(State $state): self
    {
        $this->state = $state;

        // set the owning side of the relation if necessary
        if ($state->getSector() !== $this) {
            $state->setSector($this);
        }

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setSector($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getSector() === $this) {
                $history->setSector(null);
            }
        }

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
