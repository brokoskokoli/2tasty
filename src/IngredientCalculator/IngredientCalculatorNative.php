<?php

namespace App\IngredientCalculator;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;

class IngredientCalculatorNative extends IngredientCalculatorBase
{
    /**
     * @param Ingredient $ingredient
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     */
    public function getDefault(Ingredient $ingredient)
    {
        return $this->em->getReference(RefUnit::class, RefUnit::REF_UNIT_US_CUP);
    }


}
