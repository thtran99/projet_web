<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields = {"email"},
 * message = "L'email est déjà utilisé")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min = 8,
     * minMessage = "Votre mot de passe doit contenir au minimum {{ limit }} caractères")
     *
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Cours", inversedBy="users")
     */
    private $lessons;

    /**
     * @Assert\EqualTo(propertyPath="password",
     * message = "Veuillez entrer le même mot de passe")
     */
    private $confirm_password;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Cours $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
        }

        return $this;
    }

    public function removeLesson(Cours $lesson): self
    {
        if ($this->lessons->contains($lesson)) {
            $this->lessons->removeElement($lesson);
        }

        return $this;
    }

    public function getConfirmPassword()
    {
        return $this -> confirm_password;
    }

    public function setConfirmPassword($confirm_password)
    {
        $this->confirm_password = $confirm_password;
        
        return $this;
    }

    public function getRoles() 
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        
    }

    public function getSalt()
    {
        
    }
}
