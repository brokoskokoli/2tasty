<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;

class URLParserChefkoch extends URLParserBase
{
    protected $hosts = [
        'www.chefkoch.de',
        'chefkoch.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    protected $language = Recipe::LANGUAGE_GERMAN;

    public function readRecipeFromDom(Recipe $recipe, Dom $dom)
    {
        $title = html_entity_decode($dom->find('h1.page-title', 0)->text);
        if ($title != '') {
            $recipe->setTitle($title);
        }
        $recipe->setSummary(html_entity_decode($dom->find('div.summary', 0)->text));
        $ingredientsTable = $dom->find('table.incredients');
        $ingredientRows = $ingredientsTable->find('tr');
        foreach ($ingredientRows as $ingredientRow) {
            $recipeIngredient = new RecipeIngredient();
            if ($text = $this->getNodeFindText($ingredientRow, '.amount')) {
                $this->importService->importAmoutAndUnitToRecipeIngredientFromString($recipeIngredient, $text);
            }
            if ($text = $this->getNodeFindText($ingredientRow, 'td', 1)) {
                $this->importService->importIngredientToRecipeIngredientFromString($recipeIngredient, $text);
            }
            $recipe->addRecipeIngredient($recipeIngredient);
        }

        $preparation = $dom->find('div #rezept-zubereitung', 0)->innerHtml;
        $preparationStepTexts = StringHelper::splitString($preparation);
        $this->addListAsRecipeSteps($recipe, $preparationStepTexts);
        $portionsInput = $dom->find('#divisor', 0);
        $recipe->setPortions(intval($portionsInput->tag->getAttribute('value')['value']));
        $infoNodeText = $dom->find('#preparation-info', 0)->innerHtml;
        $recipe->setInformations(html_entity_decode(trim($infoNodeText)));

        $images = [];
        $slideshow = $dom->find('#slideshow');
        $imgs = $slideshow->find('img.slideshow-image');
        foreach ($imgs as $img) {
            $images[] = $img->tag->getAttribute('src')['value'];
        }
        $this->addListAsImages($recipe, $images);
    }
}
