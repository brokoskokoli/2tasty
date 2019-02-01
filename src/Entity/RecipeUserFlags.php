<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeUserFlagsRepository")
 */
class RecipeUserFlags
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $wantToCook;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $proposed;

    public function __toString()
    {
        return strval($this->getId());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    /**
     * @return \DateTime
     */
    public function getWantToCook(): \DateTime
    {
        return $this->wantToCook;
    }

    /**
     * @param \DateTime $wantToCook
     * @return RecipeUserFlags
     */
    public function setWantToCook(\DateTime $wantToCook): RecipeUserFlags
    {
        $this->wantToCook = $wantToCook;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getProposed(): \DateTime
    {
        return $this->proposed;
    }

    /**
     * @param \DateTime $proposed
     * @return RecipeUserFlags
     */
    public function setProposed(\DateTime $proposed): RecipeUserFlags
    {
        $this->proposed = $proposed;
        return $this;
    }

}
