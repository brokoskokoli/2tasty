<?php

namespace App\IngredientCalculator;

use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
use App\Entity\RefUnit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IngredientCalculator
{
    public static function getCalculator(?RefIngredientDisplayPreference $preference,
                                         EntityManagerInterface $entityManager,
                                            TranslatorInterface $translator) : IngredientCalculatorBase
    {
        $preferenceIndicator = 'Base';
        if ($preference) {
            $preferenceIndicatorParts = explode('.', $preference->getName());
            if (count($preferenceIndicatorParts) >= 2) {
                $preferenceIndicator = $preferenceIndicatorParts[1];
            }
        }
        $className = __NAMESPACE__.'\\IngredientCalculator'.$preferenceIndicator;
        return new $className($entityManager, $translator);
    }

    public static function calculateToUnit(RecipeIngredient $recipeIngredient, RefUnit $unit)
    {
        if (empty($recipeIngredient->getUnit()) || empty($recipeIngredient->getIngredient())) {
            return false;
        }

        $fromFactorKg = $recipeIngredient->getUnit()->getFactorToKg() ?? null;
        $fromFactorLiter = $recipeIngredient->getUnit()->getFactorToLiter() ?? null;
        $toFactorKg = $unit->getFactorToKg() ?? null;
        $toFactorLiter = $unit->getFactorToLiter() ?? null;
        $density = $recipeIngredient->getIngredient()->getDensity() ?? null;

        if (!(($fromFactorKg && $toFactorKg)
            || ($fromFactorLiter && $toFactorLiter)
            || (($fromFactorKg || $fromFactorLiter) && ($toFactorKg || $toFactorLiter) && $density))
        ) {
            return false;
        }

        $withDensity = true;
        if (($fromFactorKg && $toFactorKg) || ($fromFactorLiter && $toFactorLiter)) {
            $withDensity = false;
        }

        $recipeIngredient->setUnit($unit);
        $amount = $recipeIngredient->getAmount();

        if ($fromFactorKg) {
            $amount = $amount * $fromFactorKg;
        } else {
            $amount = $amount * $fromFactorLiter;
        }

        if ($withDensity) {
            $fromKgToLiter = isset($fromFactorKg);

            if ($fromKgToLiter) {
                $amount = $amount / $density;
            } else {
                $amount = $amount * $density;
            }
        }

        if ($toFactorKg) {
            $amount = $amount / $toFactorKg;
        } else {
            $amount = $amount / $toFactorLiter;
        }

        $recipeIngredient->setUnit($unit);
        $roundedAmount = round($amount, - (ceil(log10($amount))-3));
        $recipeIngredient->setAmount($roundedAmount);
        return false;
    }
}
