<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
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
    private $description;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $start_time_morning;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $end_time_morning;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $start_time_afternoon;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $end_time_afternoon;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $monday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tuesday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $wednesday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $thursday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $friday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $saturday;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sunday;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sector", mappedBy="schedule")
     */
    private $sector;

    public function __construct()
    {
        $this->sector = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartTimeMorning(): ?\DateTimeInterface
    {
        return $this->start_time_morning;
    }

    public function setStartTimeMorning(?\DateTimeInterface $start_time_morning): self
    {
        $this->start_time_morning = $start_time_morning;

        return $this;
    }

    public function getEndTimeMorning(): ?\DateTimeInterface
    {
        return $this->end_time_morning;
    }

    public function setEndTimeMorning(?\DateTimeInterface $end_time_morning): self
    {
        $this->end_time_morning = $end_time_morning;

        return $this;
    }

    public function getStartTimeAfternoon(): ?\DateTimeInterface
    {
        return $this->start_time_afternoon;
    }

    public function setStartTimeAfternoon(?\DateTimeInterface $start_time_afternoon): self
    {
        $this->start_time_afternoon = $start_time_afternoon;

        return $this;
    }

    public function getEndTimeAfternoon(): ?\DateTimeInterface
    {
        return $this->end_time_afternoon;
    }

    public function setEndTimeAfternoon(?\DateTimeInterface $end_time_afternoon): self
    {
        $this->end_time_afternoon = $end_time_afternoon;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getMonday(): ?bool
    {
        return $this->monday;
    }

    public function setMonday(bool $monday): self
    {
        $this->monday = $monday;

        return $this;
    }

    public function getTuesday(): ?bool
    {
        return $this->tuesday;
    }

    public function setTuesday(bool $tuesday): self
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    public function getWednesday(): ?bool
    {
        return $this->wednesday;
    }

    public function setWednesday(bool $wednesday): self
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    public function getThursday(): ?bool
    {
        return $this->thursday;
    }

    public function setThursday(bool $thursday): self
    {
        $this->thursday = $thursday;

        return $this;
    }

    public function getFriday(): ?bool
    {
        return $this->friday;
    }

    public function setFriday(bool $friday): self
    {
        $this->friday = $friday;

        return $this;
    }

    public function getSaturday(): ?bool
    {
        return $this->saturday;
    }

    public function setSaturday(bool $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function getSunday(): ?bool
    {
        return $this->sunday;
    }

    public function setSunday(bool $sunday): self
    {
        $this->sunday = $sunday;

        return $this;
    }

    /**
     * @return Collection|Sector[]
     */
    public function getSector(): Collection
    {
        return $this->sector;
    }

    public function addSector(Sector $sector): self
    {
        if (!$this->sector->contains($sector)) {
            $this->sector[] = $sector;
            $sector->setSchedule($this);
        }

        return $this;
    }

    public function removeSector(Sector $sector): self
    {
        if ($this->sector->contains($sector)) {
            $this->sector->removeElement($sector);
            // set the owning side to null (unless already changed)
            if ($sector->getSchedule() === $this) {
                $sector->setSchedule(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
