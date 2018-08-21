<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;

class URLParserNYTimesCooking extends URLParserBase
{
    /**
     * @inheritDoc
     */
    public function readSingleRecipeFromUrl(Recipe $recipe, $url)
    {
        try {
            $dom = new Dom;
            $dom->loadFromUrl($url);

            $recipe->setLanguage(Recipe::LANGUAGE_ENGLISH);
            $this->importService->initForRecipe($recipe);
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

            return $recipe;
        } catch (\Exception $e) {
            dump($e);
            die;
            return null;
        }

    }

    /**
     * @inheritDoc
     */
    public function canHandleUrl($url)
    {
        $parts = parse_url($url);

        if ($parts['host'] != 'cooking.nytimes.com') {
            return false;
        }

        if (preg_match('/^\/recipes\/*/',$parts['path']) !== 1) {
            return false;
        }

        return true;
    }

}
