<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RecipeLink
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;


    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return RecipeLink
     */
    public function setUrl(?string $url): RecipeLink
    {
        $this->url = $url;
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
    public function setRecipe(?Recipe $recipe): RecipeLink
    {
        $this->recipe = $recipe;
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
     * @return RecipeLink
     */
    public function setText(?string $text): RecipeLink
    {
        $this->text = $text;
        return $this;
    }


    public function getLinkText()
    {
        if ($this->text == '') {
            return $this->getUrl();
        }
        return $this->text;
    }

}
