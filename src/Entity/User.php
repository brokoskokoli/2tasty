<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"})
 * @UniqueEntity(fields={"username"})
 *
 * @Vich\Uploadable
 *
 * Defines the properties of the User entity to represent the application users.
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class User implements UserInterface, \Serializable
{
    const DISPLAY_PREFERENCE_NATIVE = 1;

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
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $forgotPasswordToken;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $altText;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="recipe_images", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * @var RefIngredientDisplayPreference
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\RefIngredientDisplayPreference",
     *     fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $ingredientDisplayPreference;


    /**
     * @var IngredientDisplayPreferenceOverride[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\IngredientDisplayPreferenceOverride",
     *      mappedBy="author",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"id": "ASC"})
     * @Assert\Valid()
     */
    private $ingredientDisplayPreferenceOverrides;

    /**
     * @var Recipe[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\Recipe",
     *      mappedBy="author",
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $recipes;

    /**
     * @var Recipe[]|ArrayCollection
     *
     * @ORM\ManyToMany(
     *      targetEntity="App\Entity\Recipe",
     *      mappedBy="collectors",
     *      cascade={"persist"}
     * )
     */
    private $collected_recipes;

    /**
     * @var RecipeList
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RecipeList")
     * @ORM\JoinColumn(nullable=true)
     */
    private $activeRecipeList;

    /**
     * @var RecipeCooking[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\RecipeCooking",
     *      mappedBy="author",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"cookedAt": "DESC"})
     * @Assert\Valid()
     *
     */
    private $recipeCookings;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime());
        $this->altText = '';
        $this->ingredientDisplayPreference = self::DISPLAY_PREFERENCE_NATIVE;
        $this->ingredientDisplayPreferenceOverrides = new ArrayCollection();
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function __toString()
    {
        return $this->getUsername();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    public function clearForgotPasswordToken(): User
    {
        $this->forgotPasswordToken = null;
        return $this;
    }

    /**
     * @param string $forgotPasswordToken
     * @return User
     */
    public function setForgotPasswordToken(string $forgotPasswordToken): User
    {
        $this->forgotPasswordToken = $forgotPasswordToken;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }


    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }



    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getAltText(): ?string
    {
        return $this->altText ?? '';
    }

    /**
     * @param string $altText
     * @return User
     */
    public function setAltText(?string $altText): User
    {
        $this->altText = $altText;
        if (!$this->altText) {
            $this->altText = '';
        }
        return $this;
    }

    /**
     * @return RefIngredientDisplayPreference|null
     */
    public function getIngredientDisplayPreference(): ?RefIngredientDisplayPreference
    {
        return $this->ingredientDisplayPreference;
    }

    /**
     * @param RefIngredientDisplayPreference $ingredientDisplayPreference
     * @return User
     */
    public function setIngredientDisplayPreference(RefIngredientDisplayPreference $ingredientDisplayPreference): User
    {
        $this->ingredientDisplayPreference = $ingredientDisplayPreference;
        return $this;
    }


    /**
     * @return IngredientDisplayPreferenceOverride[]|ArrayCollection
     */
    public function getIngredientDisplayPreferenceOverrides() : Collection
    {
        return $this->ingredientDisplayPreferenceOverrides;
    }

    public function addIngredientDisplayPreferenceOverride(IngredientDisplayPreferenceOverride $override) : User
    {
        $this->ingredientDisplayPreferenceOverrides->add($override);
        $override->setAuthor($this);
        return $this;
    }

    public function removeIngredientDisplayPreferenceOverride(IngredientDisplayPreferenceOverride $override) : User
    {
        $this->ingredientDisplayPreferenceOverrides->remove($override);
        $override->setAuthor(null);
        return $this;
    }

    /**
     * @return Recipe[]|ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param Recipe $recipe
     * @return $this
     */
    public function removeRecipe(Recipe $recipe)
    {
        $this->recipes->remove($recipe);
        $recipe->setAuthor(null);
        return $this;
    }

    public function addRecipe(Recipe $recipe)
    {
        $this->recipes->add($recipe);
        $recipe->setAuthor($this);
        return $this;
    }

    /**
     * @return Recipe[]|ArrayCollection
     */
    public function getCollectedRecipes()
    {
        return $this->collected_recipes;
    }

    /**
     * @param Recipe $recipe
     * @return $this
     */
    public function removeCollectedRecipe(Recipe $recipe)
    {
        $this->collected_recipes->remove($recipe);
        $recipe->removeCollector($this);
        return $this;
    }

    public function addCollectedRecipe(Recipe $recipe)
    {
        $this->collected_recipes->add($recipe);
        $recipe->addCollector($this);
        return $this;
    }

    /**
     * @return RecipeList
     */
    public function getActiveRecipeList(): ?RecipeList
    {
        return $this->activeRecipeList;
    }

    /**
     * @param RecipeList $recipeList
     * @return User
     */
    public function setActiveRecipeList(?RecipeList $recipeList): User
    {
        $this->activeRecipeList = $recipeList;
        return $this;
    }

    public function getRecipeCookings(): Collection
    {
        return $this->recipeCookings;
    }

    public function addRecipeCooking(RecipeCooking $recipeCooking): self
    {
        $recipeCooking->setRecipe($this);
        if (!$this->recipeCookings->contains($recipeCooking)) {
            $this->recipeCookings->add($recipeCooking);
        }

        return $this;
    }

    public function removeRecipeCooking(RecipeCooking $recipeCooking): self
    {
        $recipeCooking->setRecipe(null);
        $this->recipeCookings->removeElement($recipeCooking);

        return $this;
    }

}
