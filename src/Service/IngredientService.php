<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
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

    public function getCalculatedIngredient(RecipeIngredient $recipeIngredient, ?RefIngredientDisplayPreference $preference = null)
    {
        $calculator =  IngredientCalculator::getCalculator($preference, $this->em, $this->translator);
        return $calculator->calculate($recipeIngredient);
    }

    public function getTranslatedCalculatedIngredient(RecipeIngredient $recipeIngredient, ?RefIngredientDisplayPreference $preference = null)
    {
        $result = $this->getCalculatedIngredient($recipeIngredient, $preference);

        if ($recipeIngredient->getIngredient()) {
            $result .= $this->translator->trans($recipeIngredient->getIngredient()->getName()) . ' ';
        }

        return $result;
    }
}
