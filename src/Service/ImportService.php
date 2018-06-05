<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ImportService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * @var RefUnitService
     */
    private $refUnitService;

    /**
     * @var IngredientService
     */
    private $ingredientService;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param RefUnitService $unitService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        RefUnitService $unitService,
        IngredientService $ingredientService
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->refUnitService = $unitService;
        $this->ingredientService = $ingredientService;
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $unitString
     * @return Ingredient|null
     */
    public function importAmoutAndUnitToRecipeIngredientFromString(RecipeIngredient $recipeIngredient, $unitString)
    {
        $this->refUnitService->parseUnitToRecipeIngredientFromString($recipeIngredient, $unitString);
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $unitString
     * @return Ingredient|null
     */
    public function importIngredientToRecipeIngredientFromString(RecipeIngredient $recipeIngredient, $ingredientString)
    {
        $parts = explode(', ', trim($ingredientString));
        if (count($parts) > 0) {
            $preparedPart = trim(array_shift($parts));
            $ingredient = $this->ingredientService->getIngredientFromStringInCurrentLocale($preparedPart);
            $recipeIngredient->setIngredient($ingredient);

            $rest = implode(', ', $parts);
            if (!empty($rest)) {
                $recipeIngredient->addToText($rest);
            }
        }
    }
}
