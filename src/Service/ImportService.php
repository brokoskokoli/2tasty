<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;
use App\Entity\RefUnitName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webit\Util\EvalMath\EvalMath;

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


    protected $unitList;
    protected $locale;


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

    public function initForRecipe(Recipe $recipe)
    {
        if ($recipe->getLanguage()) {
            $this->locale = $recipe->getLanguage();
            $this->unitList = $this->getUnitTextsForLanguage($recipe->getLanguage());
        }
    }


    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $unitString
     * @return Ingredient|null
     */
    public function importAmoutAndUnitToRecipeIngredientFromString(RecipeIngredient $recipeIngredient, $unitString)
    {
        $text = $unitString;
        $this->parseUnit($recipeIngredient, $text);
        $this->parseAmount($recipeIngredient, $text);
        $recipeIngredient->setText($text);
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $unitString
     * @return Ingredient|null
     */
    public function importIngredientToRecipeIngredientFromString(RecipeIngredient $recipeIngredient, $ingredientString)
    {
        $text = $ingredientString;
        $this->parseIngredient($recipeIngredient, $text);
        if (!empty($text)) {
            $recipeIngredient->addToText($text);
        }
    }

    public function getUnitTextsForLanguage($languageString = 'en')
    {
        return $this->refUnitService->getUnitTextsForLanguage($languageString);
    }


    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $string
     */
    public function parseUnit(RecipeIngredient $recipeIngredient, &$string)
    {
        $string = str_replace('&nbsp;', ' ', $string);
        $string = html_entity_decode($string);
        $parts = preg_split('/\s+/', $string);

        foreach ($parts as $index => &$part) {
            $preparedPart = trim($part);
            if (empty($preparedPart)) {
                continue;
            }

            if ($recipeIngredient->getUnit() === null) {
                $found = false;
                foreach ($this->unitList as $unitName => $unit) {
                    if (strtolower($part) != strtolower($unitName)) {
                        continue;
                    }

                    if ($unit instanceof RefUnit) {
                        $recipeIngredient->setUnit($unit);
                    }
                    if ($unit instanceof RefUnitName) {
                        $recipeIngredient->setUnit($unit->getUnit());
                    }
                    unset($parts[$index]);
                    $found = true;
                    break;
                }
                if ($found) {
                    break;
                }
            } else {
                break;
            }
        }
        $string = implode(' ', $parts);
    }


    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $string
     */
    public function parseAmount(RecipeIngredient $recipeIngredient, &$string)
    {
        $string = str_replace('&nbsp;', ' ', $string);
        $string = html_entity_decode($string);
        $parts = preg_split('/\s+/', $string);

        foreach ($parts as $index => &$part) {
            $preparedPart = trim($part);
            if (empty($preparedPart)) {
                continue;
            }
            if (is_numeric($preparedPart)) {
                $recipeIngredient->setAmount(floatval($preparedPart));
                unset($parts[$index]);
                break;
            }
            try {
                $m = new EvalMath;
                $m->suppress_errors = true;
                $result = $m->evaluate($preparedPart);
                if (!$m->last_error) {
                    $recipeIngredient->setAmount(floatval($result));
                    unset($parts[$index]);
                    continue;
                }
            } catch (\Exception $e) {
                // error transforming number -> do nothing with this part
            }
        }
        $string = implode(' ', $parts);
    }


    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $string
     */
    public function parseIngredient(RecipeIngredient $recipeIngredient, &$string)
    {
        $parts = explode(', ', trim($string));
        if (count($parts) > 0) {
            $preparedPart = trim(array_shift($parts));
            $ingredient = $this->ingredientService->getIngredientFromString($preparedPart, $this->locale);
            $recipeIngredient->setIngredient($ingredient);

            $rest = implode(', ', $parts);
            $string = $rest;
        }
    }

    public function storeImages(Recipe $recipe)
    {
        foreach ($recipe->getImages() as $image) {
            $this->em->persist($image);
        }

        $this->em->flush();

        // man kann die bilder ohne besitze einfach löschen und dann alle bilder, die keinen eintrag in der db haben
        // bild wird nicht gespeichert
    }

}
