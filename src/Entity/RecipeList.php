<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Arr;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeListRepository")
 */
class RecipeList implements \JsonSerializable
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
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $summary;

    /**
     * @var ArrayCollection|Recipe[]
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="recipeLists", cascade={"persist"})
     */
    private $recipes;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }


    public function __toString(): string
    {
        $result = '';
        if ($this->author !== null) {
            $result .= $this->author->getUsername() . ' - ';
        }
        return $result . $this->name;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        // This entity implements JsonSerializable (http://php.net/manual/en/class.jsonserializable.php)
        // so this method is used to customize its JSON representation when json_encode()
        // is called, for example in recipeTags|json_encode (app/Resources/views/form/fields.html.twig)

        return $this->__toString();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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
     * @return RecipeList
     */
    public function setName(?string $name): RecipeList
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
                $recipe->addRecipeList($this);
            }
        }
    }

    public function removeRecipe(Recipe $recipe): void
    {
        $this->recipes->removeElement($recipe);
        $recipe->removeRecipeList($this);
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
     * @return RecipeList
     */
    public function setAuthor(?User $author): RecipeList
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @param string $summary
     * @return RecipeList
     */
    public function setSummary(?string $summary): RecipeList
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }


}
