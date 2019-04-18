<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Helper\StringHelper;
use PHPHtmlParser\Dom;

class URLParserJournalDesFemmes extends URLParserAdvanced
{
    protected $hosts = [
        'cuisine.journaldesfemmes.fr',
    ];

    protected $pathMatch = '/^\/recette\/*/';

    protected $language = Recipe::LANGUAGE_FRENCH;

}
