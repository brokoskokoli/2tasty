<?php

namespace App\IngredientCalculator;

use App\Entity\RecipeIngredient;

class IngredientCalculatorNative extends IngredientCalculatorBase
{
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
