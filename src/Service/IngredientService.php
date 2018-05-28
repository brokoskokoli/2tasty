<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\IngredientDisplayPreferenceOverride;
use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
use App\Entity\RefUnit;
use App\Entity\User;
use App\IngredientCalculator\IngredientCalculator;
use App\IngredientCalculator\IngredientCalculatorBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IngredientService
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
        return $this->em->getRepository(Ingredient::class)->findAll();
    }

    public function getAllNames()
    {
        $locale = $this->translator->getLocale();
        $entries = $this->em->getRepository(Ingredient::class)->getAllWithTranslationIn($locale);
        $function = 'get' . $locale;
        return array_map(function ($element) use ($function) {
            return $element->$function();
        }, $entries);
    }

    public function getReadableIngredientText(RecipeIngredient $recipeIngredient)
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

    public function getUserPreferenceIngredientDisplayDefault(Ingredient $ingredient, RefIngredientDisplayPreference $preference) : RefUnit
    {
        $calculator = IngredientCalculator::getCalculator($preference, $this->em, $this->translator);
        return $calculator->getDefault($ingredient);
    }

    public function getUserIngredientPreferenceUnit(Ingredient $ingredient, ?User $user) : ?RefUnit
    {
        if (!$ingredient) {
            return null;
        }

        foreach ($user->getIngredientDisplayPreferenceOverrides() as $override) {
            if ($override->getIngredient() === $ingredient && $override->getDisplayPreference() === null) {
                return $override->getUnit();
            }
        }

        foreach ($user->getIngredientDisplayPreferenceOverrides() as $override) {
            if ($override->getIngredient() === $ingredient && $override->getDisplayPreference() === $user->getIngredientDisplayPreference()) {
                return $override->getUnit();
            }
        }

        dump($this->em->getRepository(IngredientDisplayPreferenceOverride::class)->getAllCommon());
        foreach ($this->em->getRepository(IngredientDisplayPreferenceOverride::class)->getAllCommon() as $override) {
            if ($override->getIngredient() === $ingredient && $override->getDisplayPreference() === $user->getIngredientDisplayPreference()) {
                return $override->getUnit();
            }
        }

        return $this->getUserPreferenceIngredientDisplayDefault($ingredient, $user->getIngredientDisplayPreference());
    }

    public function getCalculatedIngredientAmountText(RecipeIngredient $recipeIngredient, ?User $user = null)
    {
        if ($recipeIngredient->getIngredient()) {
            $preferenceUnit = $this->getUserIngredientPreferenceUnit($recipeIngredient->getIngredient(), $user);

            if ($preferenceUnit !== $recipeIngredient->getUnit()) {
                IngredientCalculator::calculateToUnit($recipeIngredient, $preferenceUnit);
            }
        }

        return $this->getReadableIngredientText($recipeIngredient);
    }

    public function getTranslatedCalculatedIngredientText(RecipeIngredient $recipeIngredient, ?User $user = null)
    {
        $result = $this->getCalculatedIngredientAmountText($recipeIngredient, $user);

        if ($recipeIngredient->getIngredient()) {
            $result .= $this->translator->trans($recipeIngredient->getIngredient()->getName());
        }

        return $result;
    }
}
