<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeIngredientList;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;
use Symfony\Component\DomCrawler\Crawler;

class URLParserNYTimesCooking extends URLParserAdvanced
{
    protected $hosts = [
        'cooking.nytimes.com',
    ];

    protected $pathMatch = '/^\/recipes\/*/';

    protected $language = Recipe::LANGUAGE_ENGLISH;


    protected $titleFilter = 'h1.recipe-title';

    protected $portionsFilter = 'span.recipe-yield-value';

    protected $informationsFilter = 'ul.recipe-time-yield';

    protected $summaryFilter = 'div.recipe-topnote-metadata div.topnote p';

    protected $stepsFilter = [
        self::KEY => 'ol.recipe-steps',
        self::SUBKEY => [
            'li',
        ],
    ];

    protected $ingredientsFilter = 'ul.recipe-ingredients li, section.recipe-ingredients-wrap h4';

    protected $baseURL = 'https://www.kuechengoetter.de';

    protected $imagesFilter = [
        self::KEY => 'div.media-container',
        self::SUBKEY => 'img',
        self::ATTRIBUTE => 'src',
    ];

    protected function getRecipeIngredientListFromNode(Crawler $crawler)
    {
        $class = $crawler->attr('itemprop');
        if ($class != 'recipeIngredient') {
            $title = $this->cleanString($crawler->html());
            $recipeIngredientsList = new RecipeIngredientList();
            $recipeIngredientsList->setTitle($title);
            return $recipeIngredientsList;
        }

        return null;
    }
}
