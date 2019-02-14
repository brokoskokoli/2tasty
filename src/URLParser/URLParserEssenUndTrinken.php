<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;

class URLParserEssenUndTrinken extends URLParserBase
{

    protected $hosts = [
        'www.essen-und-trinken.de',
        'm.essen-und-trinken.de',
        'essen-und-trinken.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    /**
     * @inheritDoc
     */
    public function readSingleRecipeFromUrl(Recipe $recipe, $url)
    {
        try {
            $dom = new Dom;
            $dom->loadFromUrl($url);

            $recipe->setLanguage(Recipe::LANGUAGE_GERMAN);
            $this->importService->initForRecipe($recipe);
            $title = html_entity_decode(trim(strip_tags($dom->find('h1', 0)->innerHtml)));
            if ($title != '') {
                $recipe->setTitle($title);
            }

            $recipe->setPortions($this->guessPortions($dom));

            $element = $dom->find('.summary', 0);
            if ($element) {
                $recipe->setSummary(html_entity_decode($element->text));
            }

            $finalIngredientList = $this->guessIngredientList($dom, true,'ul');
            $this->addStringListAsRecipeIngredients($recipe, $finalIngredientList);

            $finalStepsList = $this->guessStepsList($dom, true,'ol');
            if (!is_iterable($finalStepsList)) {
                $finalStepsList = $this->guessStepsList($dom, true,'ul');
            }
            if (is_iterable($finalStepsList)) {
                array_shift($finalStepsList);
                $this->addListAsRecipeSteps($recipe, $finalStepsList);
            }

            $images = $this->guessImageList($dom);
            $this->addListAsImages($recipe, $images);

            return $recipe;
        } catch (\Exception $e) {
            return null;
        }

    }
}
