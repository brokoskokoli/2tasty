<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;
use Symfony\Component\DomCrawler\Crawler;

class URLParserKuechengoetter extends URLParserAdvanced
{
    protected $hosts = [
        'www.kuechengoetter.de',
    ];

    protected $pathMatch = '/^\/rezepte\/*/';

    protected $language = Recipe::LANGUAGE_GERMAN;

    protected $titleFilter = 'h1 span.headline__title';

    protected $portionsFilter = 'div.recipe-information div.recipe-information__item--servings p.recipe-information__item-text';

    protected $informationsFilter = 'div.recipe-information';

    protected $summaryFilter = 'p.recipe-teaser';

    protected $stepsFilter = [
        self::KEY => 'ol.recipe-preparation__list',
        self::SUBKEY => [
            'li span.recipe-preparation__text',
        ],
    ];

    protected $ingredientsFilter = 'ul.recipe-ingredients__list li';

    protected $baseURL = 'https://www.kuechengoetter.de';

    protected $imagesFilter = [
        self::KEY => 'div.recipe-gallery',
        self::SUBKEY => 'section > div > meta',
        self::ATTRIBUTE => 'content',
    ];

}