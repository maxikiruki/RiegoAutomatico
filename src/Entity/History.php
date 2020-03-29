<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoryRepository")
 */
class History
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $start_time;

    /**
     * @ORM\Column(type="time")
     */
    private $end_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $start_humidity;

    /**
     * @ORM\Column(type="integer")
     */
    private $final_humidity;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_liters;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sector", inversedBy="histories")
     */
    private $sector;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        // return $this->start_time;
        return $this->start_time->format('H:i');
    }

    public function setStartTime(\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getStartHumidity(): ?int
    {
        return $this->start_humidity;
    }

    public function setStartHumidity(int $start_humidity): self
    {
        $this->start_humidity = $start_humidity;

        return $this;
    }

    public function getFinalHumidity(): ?int
    {
        return $this->final_humidity;
    }

    public function setFinalHumidity(int $final_humidity): self
    {
        $this->final_humidity = $final_humidity;

        return $this;
    }

    public function getTotalLiters(): ?int
    {
        return $this->total_liters;
    }

    public function setTotalLiters(int $total_liters): self
    {
        $this->total_liters = $total_liters;

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function __toString()
    {
        return $this->sector->getName();
    }
}
