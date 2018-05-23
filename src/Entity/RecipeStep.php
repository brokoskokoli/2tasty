<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 */
class RecipeStep
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\Range(min="1", max="2")
     */
    private $type;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeSteps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    public function __toString()
    {
        return $this->getText();
    }


    public function __construct()
    {
        $this->type = 1;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return RecipeStep
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDuration() : ?float
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     * @return RecipeStep
     */
    public function setDuration(string $duration): RecipeStep
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return RecipeStep
     */
    public function setType(int $type): RecipeStep
    {
        $this->type = $type;
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
     * @return Recipe
     */
    public function setRecipe(Recipe $recipe): RecipeStep
    {
        $this->recipe = $recipe;
        return $this;
    }
}
