<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeListRepository")
 */
class RecipeList
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
     * @var ArrayCollection|Recipe[]
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="lists")
     */
    private $recipes;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RecipeList
     */
    public function setName(string $name): RecipeList
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Recipe[]|ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe ...$recipes): void
    {
        foreach ($recipes as $recipe) {
            if (!$this->recipes->contains($recipe)) {
                $this->recipes->add($recipe);
            }
        }
    }

    public function removeRecipe(Recipe $recipe): void
    {
        $this->recipes->removeElement($recipe);
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return RecipeList
     */
    public function setAuthor(User $author): RecipeList
    {
        $this->author = $author;
        return $this;
    }


}
