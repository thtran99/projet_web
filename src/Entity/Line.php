<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LineRepository")
 */
class Line
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $ranking;

    /**
     * @ORM\Column(type="integer")
     */
    private $indentation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Exercise", inversedBy="lignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exercise_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(int $ranking): self
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function getIndentation(): ?int
    {
        return $this->indentation;
    }

    public function setIndentation(int $indentation): self
    {
        $this->indentation = $indentation;

        return $this;
    }

    public function getExerciseId(): ?Exercise
    {
        return $this->exercise_id;
    }

    public function setExerciseId(?Exercise $exercise_id): self
    {
        $this->exercise_id = $exercise_id;

        return $this;
    }
}
