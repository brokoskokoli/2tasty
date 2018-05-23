<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RecipeAlternative
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
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeAlternatives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    public function __toString()
    {
        return $this->getText();
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
    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return Recipe
     */
    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe) : RecipeAlternative
    {
        $this->recipe = $recipe;
        return $this;
    }

}
