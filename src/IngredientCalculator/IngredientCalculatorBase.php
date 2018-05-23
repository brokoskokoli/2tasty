<?php

namespace App\IngredientCalculator;

use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
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

    public function calculate(RecipeIngredient $recipeIngredient)
    {
        $result = '';

        if ($recipeIngredient->getAmount()) {
            $result .= $recipeIngredient->getAmount() . ' ';
        }
        if ($recipeIngredient->getUnit()) {
            $result .= $this->translator->trans($recipeIngredient->getUnit()->getName()) . ' ';
        }

        return $result;
    }
}
