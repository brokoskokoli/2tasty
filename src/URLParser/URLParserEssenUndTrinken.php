<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;
use Symfony\Component\DomCrawler\Crawler;

class URLParserEssenUndTrinken extends URLParserBase
{

    protected $hosts = [
        'www.essen-und-trinken.de',
        'm.essen-und-trinken.de',
        'essen-und-trinken.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    protected $language = Recipe::LANGUAGE_GERMAN;

    public function readRecipeFromDom(Recipe $recipe, Crawler $dom)
    {
        $title = html_entity_decode(trim(strip_tags($dom->find('h1', 0)->innerHtml)));
        if ($title != '') {
            $recipe->setTitle($title);
        }

        $recipe->setPortions($this->guessPortions($dom));

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

        $this->addGuessedImages($recipe, $dom);
    }
}
