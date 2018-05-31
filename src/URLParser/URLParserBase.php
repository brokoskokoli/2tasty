<?php

namespace App\URLParser;


use App\Entity\Recipe;

class URLParserBase
{
    /**
     * @param Recipe $recipe
     * @param $url
     * @return Recipe
     */
    public function readSingleRecipeFromUrl(Recipe $recipe, $url)
    {
        return $recipe;
    }

    /**
     * @param $url
     * @return bool
     */
    public function canHandleUrl($url)
    {
        return false;
    }
}
