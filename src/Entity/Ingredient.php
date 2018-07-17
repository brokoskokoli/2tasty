<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IngredientRepository")
 */
class Ingredient
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
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $de;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $en;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fr;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $es;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $density;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $liquid;

    public function __construct()
    {
        $this->liquid = false;
    }


    public function __toString()
    {
        return $this->getName();
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
     * @return Ingredient
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Ingredient
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDe(): ?string
    {
        return $this->de;
    }

    /**
     * @param string $de
     * @return Ingredient
     */
    public function setDe(string $de): Ingredient
    {
        $this->de = $de;
        return $this;
    }

    /**
     * @return string
     */
    public function getEn(): ?string
    {
        return $this->en;
    }

    /**
     * @param string $en
     * @return Ingredient
     */
    public function setEn(string $en): Ingredient
    {
        $this->en = $en;
        return $this;
    }

    /**
     * @return string
     */
    public function getFr(): ?string
    {
        return $this->fr;
    }

    /**
     * @param string $fr
     * @return Ingredient
     */
    public function setFr(string $fr): Ingredient
    {
        $this->fr = $fr;
        return $this;
    }

    /**
     * @return string
     */
    public function getEs(): ?string
    {
        return $this->es;
    }

    /**
     * @param string $fr
     * @return Ingredient
     */
    public function setEs(string $es): Ingredient
    {
        $this->es = $es;
        return $this;
    }

    /**
     * @return float
     */
    public function getDensity(): ?float
    {
        return $this->density;
    }

    /**
     * @param float $density
     * @return Ingredient
     */
    public function setDensity(?float $density): Ingredient
    {
        $this->density = $density;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLiquid(): bool
    {
        return $this->liquid;
    }

    /**
     * @param bool $liquid
     * @return Ingredient
     */
    public function setLiquid(bool $liquid): Ingredient
    {
        $this->liquid = $liquid;
        return $this;
    }


}
