<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cours", inversedBy="exercises")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Line", mappedBy="exercise_id", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $lignes;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notation", mappedBy="exercise")
     */
    private $notations;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $attempts;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->notations = new ArrayCollection();
        $this->attempts = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * @return Collection|Line[]
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(Line $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes[] = $ligne;
            $ligne->setExerciseId($this);
        }

        return $this;
    }

    public function removeLigne(Line $ligne): self
    {
        if ($this->lignes->contains($ligne)) {
            $this->lignes->removeElement($ligne);
            // set the owning side to null (unless already changed)
            if ($ligne->getExerciseId() === $this) {
                $ligne->setExerciseId(null);
            }
        }

        return $this;
    }

    public function getnbLines(): ?int
    {
        return $this->nbLines;
    }

    public function setnbLines(int $nbLines): self
    {
        $this->nbLines = $nbLines;

        return $this;
    }

    /**
     * @return Collection|Notation[]
     */
    public function getNotations(): Collection
    {
        return $this->notations;
    }

    public function addNotation(Notation $notation): self
    {
        if (!$this->notations->contains($notation)) {
            $this->notations[] = $notation;
            $notation->setExercise($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): self
    {
        if ($this->notations->contains($notation)) {
            $this->notations->removeElement($notation);
            // set the owning side to null (unless already changed)
            if ($notation->getExercise() === $this) {
                $notation->setExercise(null);
            }
        }

        return $this;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(?int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function addAttemps()
    {
        $this->attempts++;

        return $this;
    }

    public function average_rate()
    {
        $total = $this->notations->count();
        if ($total == 0) {
            return '/';
        }

        $somme = 0;
        foreach ($this->notations as $notation) {
            $somme += $notation->getNote();
        }

        return $somme / $total;
    }
}
