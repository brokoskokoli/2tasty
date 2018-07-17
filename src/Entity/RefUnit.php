<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RefUnit
{

    const REF_UNIT_KG = 2;
    const REF_UNIT_G = 3;
    const REF_UNIT_ML = 4;
    const REF_UNIT_L = 5;

    const REF_UNIT_US_CUP = 7;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $factorToLiter;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $factorToKg;

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
     * @return RefUnit
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RefUnit
     */
    public function setName(?string $name): RefUnit
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getFactorToLiter(): ?float
    {
        return $this->factorToLiter;
    }

    /**
     * @param float $factorToLiter
     * @return RefUnit
     */
    public function setFactorToLiter(?float $factorToLiter): RefUnit
    {
        $this->factorToLiter = $factorToLiter;
        return $this;
    }

    /**
     * @return float
     */
    public function getFactorToKg(): ?float
    {
        return $this->factorToKg;
    }

    /**
     * @param float $factorToKg
     * @return RefUnit
     */
    public function setFactorToKg(?float $factorToKg): RefUnit
    {
        $this->factorToKg = $factorToKg;
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
    public function setDe(string $de): RefUnit
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
    public function setEn(string $en): RefUnit
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
    public function setFr(string $fr): RefUnit
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
    public function setEs(string $es): RefUnit
    {
        $this->es = $es;
        return $this;
    }

}
