<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefIngredientDisplayPreferenceRepository")
 */
class IngredientDisplayPreferenceOverride
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Ingredient
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Ingredient",
     *     fetch="EAGER")
     * @Assert\NotNull()
     */
    private $ingredient;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="App\Entity\RefUnit", fetch="EAGER")
     * @Assert\NotNull()
     */
    private $unit;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return IngredientDisplayPreferenceOverride
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Ingredient
     */
    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    /**
     * @param Ingredient $ingredient
     * @return IngredientDisplayPreferenceOverride
     */
    public function setIngredient(?Ingredient $ingredient): IngredientDisplayPreferenceOverride
    {
        $this->ingredient = $ingredient;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): ?RefUnit
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return IngredientDisplayPreferenceOverride
     */
    public function setUnit(?RefUnit $unit): IngredientDisplayPreferenceOverride
    {
        $this->unit = $unit;
        return $this;
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
     * @return IngredientDisplayPreferenceOverride
     */
    public function setAuthor(?User $author): IngredientDisplayPreferenceOverride
    {
        $this->author = $author;
        return $this;
    }



}
