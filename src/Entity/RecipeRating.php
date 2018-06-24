<?php

namespace App\Entity;


use App\Utils\Slugger;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Arr;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRatingRepository")
 */
class RecipeRating
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTime")
     */
    private $createdAt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default": 1})
     *
     */
    private $enabled;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->enabled = true;
    }


    public function __toString(): string
    {
        return intval($this->rating) . '-' . $this->author->getFullName() . '-' . ($this->enabled? 'active':'disabled');
    }


    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return RecipeRating
     */
    public function setAuthor(?User $author): RecipeRating
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return RecipeRating
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return RecipeRating
     */
    public function setRating(?int $rating): RecipeRating
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return Recipe
     */
    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    /**
     * @param Recipe $recipe
     * @return RecipeRating
     */
    public function setRecipe(?Recipe $recipe): RecipeRating
    {
        $this->recipe = $recipe;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return RecipeRating
     */
    public function setCreatedAt(\DateTime $createdAt): RecipeRating
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return RecipeRating
     */
    public function setEnabled(bool $enabled): RecipeRating
    {
        $this->enabled = $enabled;
        return $this;
    }




}
