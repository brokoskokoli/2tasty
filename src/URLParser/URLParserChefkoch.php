<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeIngredientList;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;
use Symfony\Component\DomCrawler\Crawler;

class URLParserChefkoch extends URLParserBase
{
    protected $hosts = [
        'www.chefkoch.de',
        'chefkoch.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    protected $language = Recipe::LANGUAGE_GERMAN;

    public function readRecipeFromDom(Recipe $recipe, Crawler $dom)
    {
        $recipe->setTitle($this->getNodeFindText($dom, 'h1.page-title'));
        $recipe->setSummary($this->getNodeFindText($dom, 'div.summary'));
        $ingredientRows = $dom->filter('table.incredients tr');
        $recipeIngredientList = null;
        $ingredientRows->each(function (Crawler $ingredientRow, $i) use (&$recipeIngredientList, &$recipe) {
            if (strpos($this->getNodeFindText($ingredientRow, 'td', 1), 'Für') !== false) {
                if ($recipeIngredientList) {
                    $recipe->addRecipeIngredientList($recipeIngredientList);
                }

                $recipeIngredientList = new RecipeIngredientList();
                $recipeIngredientList->setTitle(trim($this->getNodeFindText($ingredientRow, 'td', 1)));
                return false;
            }

            if (!$recipeIngredientList) {
                $recipeIngredientList = new RecipeIngredientList();
            }

            $recipeIngredient = new RecipeIngredient();
            if ($text = $this->getNodeFindText($ingredientRow, '.amount')) {
                $this->importService->importAmoutAndUnitToRecipeIngredientFromString($recipeIngredient, $text);
            }
            if ($text = $this->getNodeFindText($ingredientRow, 'td', 1)) {
                $this->importService->importIngredientToRecipeIngredientFromString($recipeIngredient, $text);
            }

            $recipeIngredientList->addRecipeIngredient($recipeIngredient);
        });

        if ($recipeIngredientList) {
            $recipe->addRecipeIngredientList($recipeIngredientList);
        }


        $preparation = $dom->filter('div #rezept-zubereitung')->html();
        $preparationStepTexts = StringHelper::splitString($preparation);
        $this->addListAsRecipeSteps($recipe, $preparationStepTexts);

        $portionsInput = $dom->filter('#divisor')->first();
        $recipe->setPortions(intval($portionsInput->attr('value')));
        $infoNodeText = $dom->filter('#preparation-info')->html();
        $recipe->setInformations(html_entity_decode(trim($infoNodeText)));

        $imgs = $dom->filter('#slideshow img.slideshow-image');

        $images = $imgs->each(function (Crawler $img, $i) {
            return $img->attr('src');
        });
        $this->addListAsImages($recipe, $images);
    }
}
