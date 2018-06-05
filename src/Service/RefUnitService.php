<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webit\Util\EvalMath\EvalMath;

class RefUnitService
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
        return $this->em->getRepository(RefUnit::class)->findAll();
    }

    public function getAllNames()
    {
        return array_map(function ($element) {
            return $element->getName();
        }, $this->getAll());
    }

    /**
     * @param RecipeIngredient $recipeIngredient
     * @param $unitString
     * @return Ingredient|null
     */
    public function parseUnitToRecipeIngredientFromString(RecipeIngredient $recipeIngredient, $unitString)
    {
        $unitString = str_replace('&nbsp;', ' ', $unitString);
        $unitString = html_entity_decode($unitString);
        $parts = preg_split('/\s+/', $unitString);
        $units = $this->getAll();

        foreach ($parts as $part) {
            $preparedPart = trim($part);
            if (empty($preparedPart)) {
                continue;
            }
            if (is_numeric($preparedPart)) {
                $recipeIngredient->setAmount(floatval($preparedPart));
                continue;
            }
            if ($recipeIngredient->getUnit() === null) {
                $found = false;
                foreach ($units as $unit) {
                    if (strtolower($unit->getName()) === strtolower($preparedPart)) {
                        $recipeIngredient->setUnit($unit);
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    continue;
                }
            }
            try {
                $m = new EvalMath;
                $m->suppress_errors = true;
                $result = $m->evaluate($preparedPart);
                if (!$m->last_error) {
                    $recipeIngredient->setAmount(floatval($result));
                    continue;
                }
            } catch (\Exception $e) {
                // error transforming number -> do nothing with this part
            }

            $recipeIngredient->addToText($preparedPart);
        }
    }
}
