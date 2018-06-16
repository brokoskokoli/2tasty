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

    private $translationService;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param TranslationService $translationService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        TranslationService $translationService
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->translationService = $translationService;
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

    public function getUserPreferenceIngredientDisplayDefault(Ingredient $ingredient, RefIngredientDisplayPreference $preference) : ?RefUnit
    {
        $calculator = IngredientCalculator::getCalculator($preference, $this->em, $this->translator);
        return $calculator->getDefault($ingredient);
    }

    public function getUserIngredientPreferenceUnit(Ingredient $ingredient, ?User $user = null) : ?RefUnit
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
            if ($user) {
                $preferenceUnit = $this->getUserIngredientPreferenceUnit($recipeIngredient->getIngredient(), $user);

                if ($preferenceUnit && $preferenceUnit !== $recipeIngredient->getUnit()) {
                    IngredientCalculator::calculateToUnit($recipeIngredient, $preferenceUnit);
                }
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

    public function getIngredientFromStringInCurrentLocale($ingredientString)
    {
        $locale = $this->translator->getLocale();

        $ingredient = $this->em
            ->getRepository(Ingredient::class)
            // query for the issue with this id
            ->findOneBy([$locale => $ingredientString])
        ;

        if (!$ingredient) {
            $function = 'set' . $locale;
            $ingredient = new Ingredient();
            $ingredient->setName(uniqid('ingredient.'));
            $ingredient->$function($ingredientString);
            $this->translationService->clearTranslationCache();
        }

        return $ingredient;
    }
}
