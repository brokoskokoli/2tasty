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

