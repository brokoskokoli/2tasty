<?php

namespace App\Service;

use App\Entity\Ingredient;
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

        foreach ($recipeList->getRecipes() as $recipe) {
            $recipe->addRecipeList($recipeList);
        }

        $this->em->persist($recipeList);
        $this->em->flush();
    }

}
