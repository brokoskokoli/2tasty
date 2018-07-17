<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class RefUnitName
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
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $language;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="App\Entity\RefUnit", fetch="EAGER")
     */
    private $unit;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return RefUnitName
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RefUnitName
     */
    public function setName(string $name): RefUnitName
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return RefUnitName
     */
    public function setLanguage(string $language): RefUnitName
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return RefUnitName
     */
    public function setUnit(string $unit): RefUnitName
    {
        $this->unit = $unit;
        return $this;
    }
}
