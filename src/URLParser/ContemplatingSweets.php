<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredientList;
use Symfony\Component\DomCrawler\Crawler;

class ContemplatingSweets extends URLParserAdvanced
{
    protected $hosts = [
        'www.contemplatingsweets.com',
    ];

    protected $language = Recipe::LANGUAGE_ENGLISH;

    protected $portionsFilter = 'span.wprm-recipe-servings';

    protected $informationsFilter = 'div.wprm-recipe-details-container';

    protected $summaryFilter = 'div.wprm-recipe-container div.wprm-recipe-summary';

    protected $stepsFilter = [
        self::KEY => 'ol.wprm-recipe-instructions',
        self::SUBKEY => [
            'li',
        ],
    ];

    protected $ingredientsFilter = 'ul.wprm-recipe-ingredients li, h4.wprm-recipe-ingredient-group-name';

    protected $baseURL = 'https://www.contemplatingsweets.com';

    protected $imagesFilter = [
        self::KEY => 'div.entry-content p',
        self::SUBKEY => 'img.size-full',
        self::ATTRIBUTE => 'data-srcset',
    ];

    protected function getRecipeIngredientListFromNode(Crawler $crawler)
    {
        $class = $crawler->attr('class');
        if ($class != 'wprm-recipe-ingredient') {
            $title = $this->cleanString($crawler->html());
            $recipeIngredientsList = new RecipeIngredientList();
            $recipeIngredientsList->setTitle($title);
            return $recipeIngredientsList;
        }

        return null;
    }
}
