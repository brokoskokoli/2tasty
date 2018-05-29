<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeTagRepository")
 *
 * Defines the properties of the Tag entity to represent the recipe recipeTags.
 *
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RecipeTag implements \JsonSerializable
{
    const TAG_TOCOOK = 1;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var RecipeTag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Recipe", mappedBy="recipeTags")
     */
    private $recipes;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        // This entity implements JsonSerializable (http://php.net/manual/en/class.jsonserializable.php)
        // so this method is used to customize its JSON representation when json_encode()
        // is called, for example in recipeTags|json_encode (app/Resources/views/form/fields.html.twig)

        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return RecipeTag[]|ArrayCollection
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


}
