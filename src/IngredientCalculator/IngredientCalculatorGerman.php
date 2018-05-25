<?php

namespace App\IngredientCalculator;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;

class IngredientCalculatorGerman extends IngredientCalculatorBase
{
    public function getDefault(Ingredient $ingredient)
    {
        if ($ingredient->isLiquid()) {
            return $this->em->getReference(RefUnit::class, RefUnit::REF_UNIT_ML);
        } else {
            return $this->em->getReference(RefUnit::class, RefUnit::REF_UNIT_G);
        }
    }


}
