<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredientList;
use Symfony\Component\DomCrawler\Crawler;

class URLParserEssenUndTrinken extends URLParserAdvanced
{

    protected $hosts = [
        'www.essen-und-trinken.de',
        'm.essen-und-trinken.de',
        'essen-und-trinken.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    protected $language = Recipe::LANGUAGE_GERMAN;

    protected $titleFilter = 'h1';

    protected $portionsText = ['Portionen'];


    protected $stepsFilter = [
        self::KEY => 'ul.preparation',
        self::SUBKEY => [
            'li.preparation-step > div > p',
        ],
    ];

    protected $ingredientsFilter = 'ul.ingredients-list li';

    protected $informationsFilter = 'div.right-col';
    protected $baseURL = 'https://www.essen-und-trinken.de';

    protected $summaryFilter = 'div.intro';

    protected $portionsFilter = 'div.servings';

    protected $imagesFilter = [
        self::KEY => 'figure.recipe-img',
        self::SUBKEY => 'img',
        self::ATTRIBUTE => 'src',
    ];

/*
    public function readRecipeFromDom(Recipe $recipe, Crawler $dom)
    {
        $finalIngredientList = $this->guessIngredientList($dom, false,'section.ingredients ul', ['ingredients-list']);
        $finalIngredientList = $this->generateRecipeIngredientList($finalIngredientList);
        $this->addListStringRecursiveAsRecipeIngredients($recipe, $finalIngredientList);
    }

    protected function generateRecipeIngredientList(?Crawler $crawler)
    {
        if (!$crawler instanceof Crawler) {
            return [];
        }

        $lastIndex = '';
        $crawler->filter('li')->each(function (Crawler $finalIngredient, $i) use (&$result, &$lastIndex) {
            $class = $finalIngredient->attr('class');
            if ($class == 'ingredients-zwiti') {
                $lastIndex = $this->cleanString($finalIngredient->html());
                return null;
            }
            $ingredient = $finalIngredient->html();
            $ingredient = $this->cleanString($ingredient, false);
            if ($ingredient) {
                $result[$lastIndex][] = preg_replace('!\s+!', ' ', $ingredient);
            }
        });

        return $result;
    }
*/


    protected function getRecipeIngredientListFromNode(Crawler $crawler)
    {

        $class = $crawler->attr('class');
        if ($class == 'ingredients-zwiti') {
            $title = $this->cleanString($crawler->html());
            $recipeIngredientsList = new RecipeIngredientList();
            $recipeIngredientsList->setTitle($title);
            return $recipeIngredientsList;
        }

        return null;
    }
}

