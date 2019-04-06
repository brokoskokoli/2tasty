<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeIngredientListRepository")
 */
class RecipeIngredientList
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
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTime")
     */
    private $createdAt;

    /**
     * @var RecipeIngredient[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeIngredient",
     *      mappedBy="recipeIngredientList",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeIngredients;


    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeIngredientLists")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recipe;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->recipeIngredients = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getId() . '-' . $this->getTitle();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
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
     * @return RecipeIngredientList
     */
    public function setCreatedAt(\DateTime $createdAt): RecipeIngredientList
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return RecipeIngredient[]|ArrayCollection
     */
    public function getRecipeIngredients() : Collection
    {
        return $this->recipeIngredients;
    }

    public function addRecipeIngredient(RecipeIngredient $recipeIngredient) : RecipeIngredientList
    {
        $this->recipeIngredients->add($recipeIngredient);
        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient) : RecipeIngredientList
    {
        $this->recipeIngredients->remove($recipeIngredient);
        return $this;
    }

    /**
     * @return Recipe
     */
    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    /**
     * @param Recipe $recipe
     * @return RecipeIngredientList
     */
    public function setRecipe(Recipe $recipe): RecipeIngredientList
    {
        $this->recipe = $recipe;
        return $this;
    }
}
