<?php

namespace App\Entity;

use App\Repository\GenresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GenresRepository::class)
 */
class Genres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Books::class, mappedBy="genres")
     */
    private $properties;

    public function __construct()
    {
        $this->properties = new ArrayCollection();
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

    /**
     * @return Collection|Books[]
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(Books $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties[] = $property;
        }

        return $this;
    }

    public function removeProperty(Books $property): self
    {
        $this->properties->removeElement($property);

        return $this;
    }
}
