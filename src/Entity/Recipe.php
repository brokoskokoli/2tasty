<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 *
 * Defines the properties of the Recipe entity to represent the blog recipes.
 *
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class Recipe
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    const NUM_ITEMS = 10;

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
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $title;

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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $informations;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTime")
     */
    private $createdAt;

    /**
     * @var ImageFile[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\ImageFile",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @Assert\Valid()
     */
    private $images;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $portions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $author;

    /**
     * @var int
     * in minutes
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $workingTime;

    /**
     * @var int
     * in minutes
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $waitingTime;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"publishedAt": "DESC"})
     * @Assert\Valid()
     *
     */
    private $comments;

    /**
     * @var RecipeStep[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeStep",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeSteps;

    /**
     * @var RecipeHint[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeHint",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeHints;

    /**
     * @var RecipeAlternative[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeAlternative",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeAlternatives;

    /**
     * @var RecipeIngredient[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeIngredient",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeIngredients;

    /**
     * @var RecipeLink[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\RecipeLink",
     *      mappedBy="recipe",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $recipeLinks;

    /**
     * @var RecipeTag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\RecipeTag", cascade={"persist"}, inversedBy="recipes")
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $recipeTags;

    /**
     * @var ArrayCollection|RecipeList[]
     * @ORM\ManyToMany(targetEntity="App\Entity\RecipeList", inversedBy="recipes")
     */
    private $lists;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->recipeTags = new ArrayCollection();
        $this->recipeHints = new ArrayCollection();
        $this->recipeAlternatives = new ArrayCollection();
        $this->recipeIngredients = new ArrayCollection();
        $this->recipeSteps = new ArrayCollection();
        $this->recipeLinks = new ArrayCollection();
        $this->private = false;
    }

    public function __toString()
    {
        return $this->getSlug();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        $comment->setRecipe($this);
if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    public function removeComment(Comment $comment): void
    {
        $comment->setRecipe(null);
        $this->comments->removeElement($comment);
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return string
     */
    public function getInformations(): ?string
    {
        return $this->informations;
    }

    /**
     * @param string $informations
     * @return Recipe
     */
    public function setInformations(?string $informations): Recipe
    {
        $this->informations = $informations;
        return $this;
    }



    /**
     * @return ImageFile[]|ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function addImage(ImageFile $image)
    {
        $this->images->add($image);
        return $this;
    }

    public function removeImage(ImageFile $image)
    {
        $this->images->removeElement($image);
        return $this;
    }


    public function addTag(RecipeTag ...$recipeTags): void
    {
        foreach ($recipeTags as $tag) {
            if (!$this->recipeTags->contains($tag)) {
                $this->recipeTags->add($tag);
            }
        }
    }

    public function removeTag(RecipeTag $tag): void
    {
        $this->recipeTags->removeElement($tag);
    }

    public function getTags(): Collection
    {
        return $this->recipeTags;
    }

    /**
     * @return RecipeTag[]|ArrayCollection
     */
    public function getRecipeTags()
    {
        return $this->recipeTags;
    }

    /**
     * @param RecipeTag[]|ArrayCollection $recipeTags
     * @return Recipe
     */
    public function setRecipeTags($recipeTags)
    {
        $this->recipeTags = $recipeTags;
        return $this;
    }

    /**
     * @return RecipeList[]|ArrayCollection
     */
    public function getLists()
    {
        return $this->lists;
    }

    public function addList(RecipeList ...$lists): void
    {
        foreach ($lists as $list) {
            if (!$this->lists->contains($list)) {
                $this->lists->add($list);
            }
        }
    }

    public function removeList(RecipeList $list): void
    {
        $this->lists->removeElement($list);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Recipe
     */
    public function setCreatedAt(\DateTime $createdAt): Recipe
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getPortions(): ?int
    {
        return $this->portions;
    }

    /**
     * @param int $portions
     * @return Recipe
     */
    public function setPortions(int $portions): Recipe
    {
        $this->portions = $portions;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     * @param bool $private
     * @return Recipe
     */
    public function setPrivate(bool $private): Recipe
    {
        $this->private = $private;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkingTime(): ?int
    {
        return $this->workingTime;
    }

    /**
     * @param int $workingTime
     * @return Recipe
     */
    public function setWorkingTime(?int $workingTime): Recipe
    {
        $this->workingTime = $workingTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getWaitingTime(): ?int
    {
        return $this->waitingTime;
    }

    /**
     * @param int $waitingTime
     * @return Recipe
     */
    public function setWaitingTime(?int $waitingTime): Recipe
    {
        $this->waitingTime = $waitingTime;
        return $this;
    }

    /**
     * @return RecipeStep[]|ArrayCollection
     */
    public function getRecipeSteps() : Collection
    {
        return $this->recipeSteps;
    }

    public function addRecipeStep(RecipeStep $recipeStep) : Recipe
    {
        $this->recipeSteps->add($recipeStep);
        $recipeStep->setRecipe($this);
        return $this;
    }

    public function removeRecipeStep(RecipeStep $recipeStep) : Recipe
    {
        $this->recipeSteps->remove($recipeStep);
        $recipeStep->setRecipe(null);
        return $this;
    }

    /**
     * @return RecipeHint[]|ArrayCollection
     */
    public function getRecipeHints() : Collection
    {
        return $this->recipeHints;
    }

    public function addRecipeHint(RecipeHint $recipeHint) : Recipe
    {
        $this->recipeHints->add($recipeHint);
        $recipeHint->setRecipe($this);
        return $this;
    }

    public function removeRecipeHint(RecipeHint $recipeHint) : Recipe
    {
        $this->recipeHints->remove($recipeHint);
        $recipeHint->setRecipe(null);
        return $this;
    }

    /**
     * @return RecipeAlternative[]|ArrayCollection
     */
    public function getRecipeAlternatives() : Collection
    {
        return $this->recipeAlternatives;
    }

    public function addRecipeAlternative(RecipeAlternative $recipeAlternative) : Recipe
    {
        $this->recipeAlternatives->add($recipeAlternative);
        $recipeAlternative->setRecipe($this);
        return $this;
    }

    public function removeRecipeAlternative(RecipeAlternative $recipeAlternative) : Recipe
    {
        $this->recipeAlternatives->remove($recipeAlternative);
        $recipeAlternative->setRecipe(null);
        return $this;
    }

    /**
     * @return RecipeIngredient[]|ArrayCollection
     */
    public function getRecipeIngredients() : Collection
    {
        return $this->recipeIngredients;
    }

    public function addRecipeIngredient(RecipeIngredient $recipeIngredient) : Recipe
    {
        $this->recipeIngredients->add($recipeIngredient);
        $recipeIngredient->setRecipe($this);
        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient) : Recipe
    {
        $this->recipeIngredients->remove($recipeIngredient);
        $recipeIngredient->setRecipe(null);
        return $this;
    }


    /**
     * @return RecipeLink[]|ArrayCollection
     */
    public function getRecipeLinks() : Collection
    {
        return $this->recipeLinks;
    }

    public function addRecipeLink(RecipeLink $recipeLink) : Recipe
    {
        $this->recipeLinks->add($recipeLink);
        $recipeLink->setRecipe($this);
        return $this;
    }

    public function removeRecipeLink(RecipeLink $recipeLink) : Recipe
    {
        $this->recipeLinks->remove($recipeLink);
        $recipeLink->setRecipe(null);
        return $this;
    }



}
