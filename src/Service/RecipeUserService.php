<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipeCooking;
use App\Entity\RecipeLink;
use App\Entity\RecipeTag;
use App\Entity\RecipeUserFlags;
use App\Entity\User;
use App\Form\RecipeDisplaySettingsType;
use App\Helper\SecurityHelper;
use App\URLParser\URLParser;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use FAPI\Localise\Api\Import;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecipeUserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RecipeService
     */
    private $recipeService;


    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RecipeService $recipeService
    ) {
        $this->em = $entityManager;
        $this->recipeService = $recipeService;
    }

    public function getRecipeProposal(User $user)
    {
        $recipes = $this->em->getRepository(Recipe::class)->getMyProposedRecipes($user);
        if (empty($recipes)) {
            return null;
        }

        $index = array_rand($recipes);
        $recipe = $recipes[$index];

        if ($recipe) {
            $recipeTags = $this->em->getRepository(RecipeUserFlags::class)->getRecipeUserFlags($recipe, $user);
            $recipeTags->setProposedNow();
            $this->em->flush();
        }

        return $recipe;
    }

    public function userCooksNow(Recipe $recipe, User $user)
    {
        $cooking = new RecipeCooking();
        $cooking->setRecipe($recipe);
        $cooking->setAuthor($user);
        $this->em->persist($cooking);

        $recipeTags = $this->em->getRepository(RecipeUserFlags::class)->getRecipeUserFlags($recipe, $user);
        $recipeTags->setProposed(null);
        $recipeTags->setWantToCook(null);
        $this->em->flush();
    }

    public function userCooksSoon(Recipe $recipe, User $user)
    {
        $recipeTags = $this->em->getRepository(RecipeUserFlags::class)->getRecipeUserFlags($recipe, $user);
        $recipeTags->setWantToCookNow();
        $this->em->flush();
    }
}
