<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipeLink;
use App\Entity\RecipeTag;
use App\Entity\User;
use App\Helper\SecurityHelper;
use App\URLParser\URLParser;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use FAPI\Localise\Api\Import;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecipeService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportService
     */
    private $importService;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ImportService $importService
    ) {
        $this->em = $entityManager;
        $this->importService = $importService;
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

    private function prepareRecipeFilter(&$filter, ?User $user = null)
    {
        if ($filter['private'] === true) {
            $filter['private'] = $user;
        } else {
            $filter['private'] = null;
        }
    }

    public function filterRecipes($page, $filter, User $user)
    {
        $this->prepareRecipeFilter($filter, $user);

        return $this->em->getRepository(Recipe::class)->filterRecipes($page, $filter, $user);
    }

    public function randomRecipe($filter, User $user)
    {
        $this->prepareRecipeFilter($filter, $user);

        $recipes = $this->em->getRepository(Recipe::class)->getAllForFilter($filter, $user);

        $index = array_rand($recipes);
        return [$recipes[$index]];
    }
    /**
     * @param $link
     * @param User $user
     * @return Recipe|null
     */
    public function createRecipeFromLink($link, User $user)
    {
        $recipe = new Recipe();
        $recipe->setAuthor($user);
        $tempname = parse_url($link, PHP_URL_HOST) . parse_url($link, PHP_URL_PATH);
        $tempname = preg_replace('/[^A-Za-z0-9\-]/', ' ', $tempname);
        $recipeLink = new RecipeLink();
        $recipeLink->setUrl($link);
        $recipe->addRecipeLink($recipeLink);
        $recipe->setTitle($tempname);
        $recipe->setPrivate(true);
        $tag = $this->em->getReference(RecipeTag::class, RecipeTag::TAG_TOCOOK);
        $recipe->addTag($tag);

        $parser = URLParser::getParser($link, $this->importService);
        if ($parser) {
            $parser->readSingleRecipeFromUrl($recipe, $link);
        }

        return $recipe;
    }
}
