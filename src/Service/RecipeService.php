<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\User;
use App\Helper\SecurityHelper;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecipeService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->em = $entityManager;
    }



    public function saveRecipe(Recipe $recipe)
    {
        $recipe->setSlug(Slugger::slugify($recipe->getAuthor()->getUsername() . '_' . $recipe->getTitle()));

        foreach ($recipe->getRecipeHints() as &$recipeHint) {
            $recipeHint->setRecipe($recipe);
        }
        foreach ($recipe->getRecipeSteps() as &$recipeStep) {
            $recipeStep->setRecipe($recipe);
        }
        foreach ($recipe->getRecipeIngredients() as &$recipeIngredient) {
            $recipeIngredient->setRecipe($recipe);
        }
        foreach ($recipe->getRecipeAlternatives() as &$recipeAlternative) {
            $recipeAlternative->setRecipe($recipe);
        }
        foreach ($recipe->getRecipeLinks() as &$recipeLink) {
            $recipeLink->setRecipe($recipe);
        }
        foreach ($recipe->getImages() as &$recipeImage) {
            if ($recipeImage->getImageName() === null && $recipeImage->getImageFile() === null) {
                $recipe->removeImage($recipeImage);
            } else {
                $recipeImage->setRecipe($recipe);
            }
        }

        $this->em->persist($recipe);
        $this->em->flush();
    }

    public function deleteRecipe(Recipe $recipe)
    {

        $this->em->remove($recipe);
        $this->em->flush();
    }

    public function getLatest($page)
    {
        return $this->em->getRepository(Recipe::class)->findLatest($page);
    }

    /**
     * @param $filter
     * @return Recipe
     */
    public function getRandom($filter)
    {
        $recipes = $this->em->getRepository(Recipe::class)->findAll();
        $index = array_rand($recipes);
        return $recipes[$index];
    }

    public function filterRecipes($page, $filter, User $user)
    {
        if ($filter['private'] === true) {
            $filter['private'] = $user;
        } else {
            $filter['private'] = null;
        }

        return $this->em->getRepository(Recipe::class)->filterRecipes($page, $filter, $user);
    }
}
