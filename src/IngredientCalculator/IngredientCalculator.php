<?php

namespace App\IngredientCalculator;

use App\Entity\RefIngredientDisplayPreference;
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
}
