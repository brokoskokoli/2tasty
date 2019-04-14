<?php

namespace App\URLParser;

use App\Entity\Recipe;
use App\Service\ImportService;
use Symfony\Component\DomCrawler\Crawler;

class URLParserBase
{
    const MAX_NUMBER_OF_IMGAES = 10;

    const PORTIONS_TEXT_AREA = 50;

    const KEY = 'key';

    const SUBKEY = 'subkey';

    const ATTRIBUTE = 'attribute';

    protected $importService;

    protected $hosts = ['*'];

    protected $pathMatch = '';

    protected $language = Recipe::LANGUAGE_ENGLISH;

    /**
     * @inheritDoc
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function getText(Crawler $dom)
    {
        return trim(strip_tags($dom->html()));
    }

    /**
     * @param Recipe $recipe
     * @param $url
     * @return Recipe
     */
    public function readSingleRecipeFromUrl(Recipe $recipe, $url)
    {
        try {
            $html = @file_get_contents($url);
            if (!$html) {
                return null;
            }
            $dom = new Crawler($html);
            $this->prepareRecipe($recipe, $dom);
            $this->readRecipeFromDom($recipe, $dom);
            return $recipe;
        } catch (\Exception $e) {

            dump($e);
            return null;
        }
    }

    public function readRecipeFromDom(Recipe $recipe, Crawler $dom)
    {
    }

    public function canHandleUrl($url)
    {
        $parts = parse_url($url);

        $domainMatch = false;
        foreach ($this->hosts as $host) {
            if ('*' === $host ||$parts['host'] === $host) {
                $domainMatch = true;
            }
        }
        if (!$domainMatch) {
            return false;
        }

        if (preg_match($this->pathMatch, $parts['path']) !== 1) {
            return false;
        }

        return true;
    }

    protected function cleanString($string, $stripTags = true)
    {
        $text = trim($string);
        $text = str_replace('&nbsp;', ' ', $text);
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = preg_replace('!\s+!', ' ', $text);
        if ($stripTags) {
            $text = strip_tags($text);
        }

        return $text;
    }

    /**
     * @param Recipe $recipe
     * @param Crawler $dom
     */
    public function prepareRecipe(Recipe $recipe, Crawler $dom)
    {
        $recipe->setLanguage($this->language);
        $this->importService->initForRecipe($recipe);
    }

    protected function getNodeFindText(Crawler $dom, $selector, $index = 0)
    {
        $result = $dom->filter($selector);

        if ($result->count() > $index) {
            return $result->eq($index)->text();
        }

        return '';
    }
}
