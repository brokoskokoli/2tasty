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
     * @return Ingredient[]|array
     */
    public function getAllForUser(?User $user = null)
    {
        return $this->em->getRepository(RecipeList::class)->findAll();
    }

    public function getAllNamesForUser(?User $user = null)
    {
        return array_map(function ($element) {
            return $element->getName();
        }, $this->getAllForUser($user));
    }


    public function saveRecipeList(RecipeList $recipeList)
    {
        $recipeList->setSlug(Slugger::slugify($recipeList->getAuthor()->getUsername() . '_' . $recipeList->getName()));

        $this->em->persist($recipeList);
        $this->em->flush();
    }

}
