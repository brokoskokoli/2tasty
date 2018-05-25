<?php

namespace App\IngredientCalculator;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
use App\Entity\RefUnit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IngredientCalculatorBase
{
    const TABLE_PREFIX = 'ingredient_preferences.';

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /** @var TranslatorInterface $translator */
    protected $translator;

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
     * @param Ingredient $ingredient
     * @return RefUnit
     * @throws \Doctrine\ORM\ORMException
     */
    public function getDefault(Ingredient $ingredient)
    {
        return $this->em->getReference(RefUnit::class, RefUnit::REF_UNIT_KG);
    }
}
