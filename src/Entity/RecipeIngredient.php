<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RecipeIngredient
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $text;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="App\Entity\RefUnit", fetch="EAGER")
     */
    private $unit;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @var Ingredient
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Ingredient",
     *     fetch="EAGER",
     *     cascade={"persist"})
     */
    private $ingredient;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeIngredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    public function __construct()
    {
        $this->text = '';
    }


    public function __toString()
    {
        return $this->getIngredient()->getName();
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
     * @return RecipeIngredient
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount() : ?string
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return RecipeIngredient
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Ingredient|null
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * @param Ingredient $ingredient
     * @return RecipeIngredient
     */
    public function setIngredient(?Ingredient $ingredient): RecipeIngredient
    {
        $this->ingredient = $ingredient;
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
     * @return Recipe
     */
    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
        return $recipe;
    }

    /**
     * @return string
     */
    public function getUnit() : ?RefUnit
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return RecipeIngredient
     */
    public function setUnit(?RefUnit $unit): RecipeIngredient
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return RecipeIngredient
     */
    public function setText(?string $text): RecipeIngredient
    {
        $this->text = $text;
        return $this;
    }




}
