<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class ImageFile
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $uniqueId;


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
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var Recipe
     *
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recipe;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime());
        $this->altText = '';
        $this->uniqueId = uniqid();
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
        return $this->imageName;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return ImageFile
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return ImageFile
     */
    public function setUpdatedAt(\DateTime $updatedAt): ImageFile
    {
        $this->updatedAt = $updatedAt;
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
     * @param Recipe $recipe
     * @return ImageFile
     */
    public function setRecipe(Recipe $recipe): ImageFile
    {
        $this->recipe = $recipe;
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
     * @return ImageFile
     */
    public function setAltText(?string $altText): ImageFile
    {
        $this->altText = $altText;
        if (!$this->altText) {
            $this->altText = '';
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueId(): ?string
    {
        return $this->uniqueId;
    }

    /**
     * @param string $uniqueId
     * @return ImageFile
     */
    public function setUniqueId(?string $uniqueId): ImageFile
    {
        if ($uniqueId) {
            $this->uniqueId = $uniqueId;
        }

        return $this;
    }



}
