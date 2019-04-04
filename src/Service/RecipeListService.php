<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\RecipeTag;
use App\Entity\User;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RecipeListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @return Ingredient[]|array
     */
    public function getAll()
    {
        return $this->em->getRepository(RecipeList::class)->findAll();
    }

    /**
     * @return RecipeList[]|array
     */
    public function getAllForUser(?User $user = null, $onlyNotArchived = false)
    {
        return $this->em->getRepository(RecipeList::class)->getAllForUser($user, $onlyNotArchived);
    }

    public function getAllNamesForUser(?User $user = null)
    {
        return array_map(function ($element) {
            return $element->getName();
        }, $this->getAllForUser($user, true));
    }


    public function saveRecipeList(RecipeList $recipeList, ?User $user = null)
    {
        $recipeList->setAuthor($user);
        $recipeList->createSlug();

        $this->em->persist($recipeList);
        $this->em->flush();
    }

    public function makeActive(RecipeList $recipeList, User $user)
    {
        if (!$recipeList->isArchived()) {
            $user->setActiveRecipeList($recipeList);
            $this->em->persist($user);
            $this->em->flush();
            return true;
        }

        return false;
    }

    public function addRecipeToList(RecipeList $recipeList, Recipe $recipe)
    {
        if (!$recipeList->isArchived() && !$recipeList->getRecipes()->contains($recipe)) {
            $recipeList->addRecipe($recipe);
            $this->em->persist($recipeList);
            $this->em->flush();
            return true;
        }

        return false;
    }

    public function removeActive(User $user)
    {
        $user->setActiveRecipeList(null);
        $this->em->persist($user);
        $this->em->flush();
        return true;
    }

    public function deleteRecipeList(RecipeList $recipeList)
    {
        $this->em->remove($recipeList);
        $this->em->flush();
    }

}
