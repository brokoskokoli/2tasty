<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;
use Symfony\Component\DomCrawler\Crawler;

class URLParserNYTimesCooking extends URLParserBase
{
    protected $hosts = [
        'cooking.nytimes.com',
    ];

    protected $pathMatch = '/^\/recipes\/*/';

    protected $language = Recipe::LANGUAGE_ENGLISH;

    public function readRecipeFromDom(Recipe $recipe, Crawler $dom)
    {
        $title = html_entity_decode($dom->find('h1.recipe-title', 0)->text);
        if ($title != '') {
            $recipe->setTitle($title);
        }

        $recipe->setSummary(html_entity_decode($dom->find('div.recipe-topnote-metadata div.topnote p', 0)->text));

        $finalIngredientList = $this->guessIngredientList($dom, true,'ul', ['recipe-ingredients']);
        $this->addStringListAsRecipeIngredients($recipe, $finalIngredientList);
        $finalStepsList = $this->guessStepsList($dom, true,'ol', ['recipe-steps']);
        $this->addListAsRecipeSteps($recipe, $finalStepsList);

        $portionsText = $dom->find('span.recipe-yield-value', 0)->text;
        $portionsTextParts = explode(' ', $portionsText);
        $recipe->setPortions(intval(array_shift($portionsTextParts)));

        $recipe->setInformations($dom->find('span.recipe-yield-value', 1)->text);

        $images = [];
        $slideshow = $dom->find('div.recipe-intro div.media-container');
        $imgs = $slideshow->find('img');
        foreach ($imgs as $img) {
            $images[] = $img->tag->getAttribute('src')['value'];
        }
        $this->addListAsImages($recipe, $images);
    }
}
