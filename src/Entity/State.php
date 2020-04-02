<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StateRepository")
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $on_off;

    /**
     * @ORM\Column(type="boolean")
     */
    public $programmed;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sector", inversedBy="state", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sector;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOnOff(): ?bool
    {
        return $this->on_off;
    }

    public function setOnOff(bool $on_off): self
    {
        $this->on_off = $on_off;

        return $this;
    }

    public function getProgrammed(): ?bool
    {
        return $this->programmed;
    }

    public function setProgrammed(bool $programmed): self
    {
        $this->programmed = $programmed;

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function __toString()
    {
        return $this->sector->getName();
    }
}
