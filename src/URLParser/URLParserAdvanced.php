<?php

namespace App\URLParser;


use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeIngredientList;
use App\Entity\RecipeStep;
use App\Helper\FileHelper;
use HTMLPurifier;
use HTMLPurifier_Config;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class URLParserAdvanced extends URLParserBase
{
    const TEXT = 'text';
    const TIME = 'time';

    protected $titleFilter = 'h1';

    protected $portionsFilter = '';

    protected $portionsText = [];

    protected $stepsFilter = [
        self::KEY => 'ul.preparation',
        self::SUBKEY => [
            'li',
        ],
    ];

    protected $ingredientsFilter = 'ul.ingredients li';

    protected $informationsFilter = '';
    protected $baseURL = '';

    protected $summaryFilter = '';

    protected $imagesFilter = [
        self::KEY => 'div.images',
        self::SUBKEY => 'img',
        self::ATTRIBUTE => 'src',
    ];


    /**
     * @param Recipe $recipe
     * @param Crawler $dom
     */
    public function prepareRecipe(Recipe $recipe, Crawler $dom)
    {
        parent::prepareRecipe($recipe, $dom);
        if ($this->titleFilter) {
            $recipe->setTitle($this->getNodeFindText($dom, $this->titleFilter));
        }
        if (empty($this->portionsText)) {
            $recipe->setPortions($this->guessPortions($dom));
        }
        if (!empty($this->portionsFilter)) {
            $portions = $this->guessPortionsFromFilter($dom, $this->portionsFilter);
            if ($portions) {
                $recipe->setPortions($portions);
            }
        }
        if (isset($this->stepsFilter[self::KEY]) && isset($this->stepsFilter[self::SUBKEY])) {
            $this->guessRecipeStepsAndAddThem($recipe, $dom);
        }

        if (!empty($this->ingredientsFilter)) {
            $this->guessIngredientListAndAddThem($recipe, $dom);
        }
        if (!empty($this->informationsFilter)) {
            $this->guessRecipeInformationsAndAddThem($recipe, $dom);
        }
        if (!empty($this->summaryFilter)) {
            $this->guessRecipeSummaryAndAddThem($recipe, $dom);
        }

        if (isset($this->imagesFilter[self::KEY]) && isset($this->imagesFilter[self::SUBKEY]) && isset($this->imagesFilter[self::ATTRIBUTE])) {
            $this->guessImagesAndAddThem($recipe, $dom);
        }
    }

    protected function guessPortions(Crawler $dom, $keys = ['portion'])
    {
        $fulltext = $this->getText($dom);
        foreach ($keys as $keyWord) {
            if ($pos = stripos($fulltext, $keyWord)) {
                $area = substr($fulltext, $pos - self::PORTIONS_TEXT_AREA/2,self::PORTIONS_TEXT_AREA);
                $parts = explode(' ', $area);
                foreach ($parts as $part) {
                    if (is_numeric($part)) {
                        return $part;
                        break;
                    }
                }
            }
        }

        return null;
    }

    protected function guessPortionsFromFilter(Crawler $dom, $filter) : int
    {
        $portionsNode = $dom->filter($filter);

        $portions = $this->cleanString($portionsNode->html());

        $parts = explode(' ', $portions);

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                return intval($part);
            }
        }

        return 0;
    }


    protected function getRecipeIngredientListFromNode(Crawler $crawler)
    {
        return null;
    }

    protected function getRecipeIngredientFromNode(Crawler $crawler)
    {
        return $this->importService->parseStringToRecipeIngredient($this->cleanString($crawler->html()));
    }


    protected function guessIngredientListAndAddThem(Recipe $recipe, Crawler $dom, string $key = '')
    {
        if (!$key && !empty($this->ingredientsFilter)) {
            $key = $this->ingredientsFilter;
        }

        if (!$key) {
            return false;
        }

        $ingredientsNodes = $dom->filter($key);
        $currentRecipeIngredientList = null;

        $ingredientsNodes->each(function (Crawler $crawler, $i) use (&$recipe, &$currentRecipeIngredientList) {
            $recipeIngredientList = $this->getRecipeIngredientListFromNode($crawler);
            if ($recipeIngredientList) {
                $recipe->addRecipeIngredientList($recipeIngredientList);
                $currentRecipeIngredientList = $recipeIngredientList;
                return null;
            }

            if (!$currentRecipeIngredientList) {
                $currentRecipeIngredientList = new RecipeIngredientList();
                $recipe->addRecipeIngredientList($currentRecipeIngredientList);
            }

            $recipeIngredient = $this->getRecipeIngredientFromNode($crawler);
            if ($recipeIngredient) {
                $currentRecipeIngredientList->addRecipeIngredient($recipeIngredient);
            }
        });

        return true;
    }

    /**
     * @param Recipe $recipe
     * @param Dom $dom
     */
    protected function guessImagesAndAddThem(Recipe $recipe, Crawler $dom, $key = '', $subkey = '', $attribute = '')
    {

        if (!$key && !empty($this->imagesFilter[self::KEY])) {
            $key = $this->imagesFilter[self::KEY];
        }
        if (!$subkey && !empty($this->imagesFilter[self::SUBKEY])) {
            $subkey = $this->imagesFilter[self::SUBKEY];
        }
        if (!$attribute && !empty($this->imagesFilter[self::ATTRIBUTE])) {
            $attribute = $this->imagesFilter[self::ATTRIBUTE];
        }

        if (!$key || !$subkey || !$attribute) {
            return false;
        }

        $images = $this->guessImageList($dom, $key, $subkey, $attribute);

        if (!$images) {
            return false;
        }

        $this->addListAsImages($recipe, $images);

        return true;
    }


    protected function guessImageList(Crawler $dom, $key, $subkey, $attribute)
    {
        $images = [];

        /** @var Collection $slideshow */
        $slideshow = $dom->filter($key);

        if ($slideshow->count()) {
            $imgs = $slideshow->filter($subkey);
            $images = $imgs->each(function (Crawler $img, $i) use ($attribute) {
                return $img->attr($attribute);
            });
        }

        return $images;
    }

    protected function addListAsImages(Recipe $recipe, iterable $images)
    {
        $number = 0;
        foreach ($images as $index => $image) {
            if ($number >= self::MAX_NUMBER_OF_IMGAES) {
                continue;
            }

            $filename = FileHelper::getTempFileName() . '.temp';

            if (stripos($image, 'http') !== 0) {
                $image = 'http:' . $image;
            }

            file_put_contents($filename, fopen($image, 'r'));
            $mimeType = mime_content_type($filename);

            if ($mimeType != 'image/jpeg') {
                continue;
            }

            $size = filesize($filename);

            $uploadedFile = new UploadedFile($filename, 'image' . $index . '.jpg', $mimeType, $size, null, true);

            $imageFile = new ImageFile();
            $imageFile->setImageFile($uploadedFile);
            $recipe->addImage($imageFile);
            $number++;
        }

        $this->importService->storeImages($recipe);
    }

    protected function guessRecipeInformationsAndAddThem(Recipe $recipe, Crawler $dom, $filter = '') : bool
    {
        if (!$filter && !empty($this->informationsFilter)) {
            $filter = $this->informationsFilter;
        }

        $informations = '';
        if (is_string($filter) && !empty($filter)) {
            $informations = $this->guessRecipeInformations($dom, $filter);
        }

        if (!is_string($informations)) {
            return false;
        }

        return $this->addStringAsRecipeInformations($recipe, $informations);
    }


    protected function guessRecipeInformations(Crawler $dom, string $filter) : string
    {
        $informations = $dom->filter($filter);

        if ($informations->count() == 0) {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();
        if (!empty($this->baseURL)) {
            $config->set('URI.Base', $this->baseURL);
            $config->set('URI.MakeAbsolute', true);
        }
        $purifier = new HTMLPurifier($config);
        $informations = $purifier->purify($informations->html());

        return $informations;
    }

    protected function addStringAsRecipeInformations(Recipe $recipe, string $informations) : bool
    {
        if (!empty($informations)) {
            $recipe->setInformations($informations);
            return true;
        }

        return false;
    }

    protected function guessRecipeSummaryAndAddThem(Recipe $recipe, Crawler $dom, $filter = '') : bool
    {
        if (!$filter && !empty($this->summaryFilter)) {
            $filter = $this->summaryFilter;
        }

        $summary = '';
        if (is_string($filter) && !empty($filter)) {
            $summary = $this->guessRecipeSummary($dom, $filter);
        }

        if (!is_string($summary)) {
            return false;
        }

        return $this->addStringAsRecipeSummary($recipe, $summary);
    }


    protected function guessRecipeSummary(Crawler $dom, string $filter) : string
    {
        $summary = $dom->filter($filter);

        if ($summary->count() == 0) {
            return '';
        }
        $summary = $this->cleanString($summary->html());

        return $summary;
    }

    protected function addStringAsRecipeSummary(Recipe $recipe, string $summary) : bool
    {
        if (!empty($summary)) {
            $recipe->setSummary($summary);
            return true;
        }

        return false;
    }


    protected function guessRecipeStepsAndAddThem(Recipe $recipe, Crawler $dom, $key = '', $subkeys = []) : bool
    {
        if (!$key && !empty($this->stepsFilter[self::KEY])) {
            $key = $this->stepsFilter[self::KEY];
        }
        if (!$subkeys && !empty($this->stepsFilter[self::SUBKEY])) {
            $subkeys = $this->stepsFilter[self::SUBKEY];
        }

        $steps = $this->guessRecipeStepsList($dom, $key, $subkeys);

        if (!$steps) {
            return false;
        }

        return $this->addListAsRecipeSteps($recipe, $steps);
    }

    protected function guessRecipeStepsList(Crawler $dom, string $key, array $subkeys) : array
    {
        $stepsLists = $dom->filter($key);
        $finalStepsList = [];
        $stepsLists->each(function (Crawler $stepsList, $i) use (&$finalStepsList, $subkeys) {
            if ($finalStepsList) {
                return null;
            }
            foreach ($subkeys as $subkey) {
                $subList = $stepsList->filter($subkey);
                if ($subList->count() > 0) {
                    $finalStepsList = $subList->each(function (Crawler $crawler, $i) {
                        return [self::TEXT => $this->cleanString($crawler->html())];
                    });

                }
            }
        });

        return $finalStepsList;
    }

    protected function addListAsRecipeSteps(Recipe $recipe, iterable $list) : bool
    {
        foreach ($list as $step) {
            $recipeStep = new RecipeStep();
            if (!($step[self::TEXT] ?? '')) {
                continue;
            }
            $recipeStep->setText(html_entity_decode($step[self::TEXT]));

            if ($step[self::TIME] ?? '') {
                $recipeStep->setDuration($step[self::TIME]);
            }
            $recipe->addRecipeStep($recipeStep);
        }

        if ($recipe->getRecipeSteps()->count() > 0) {
            return true;
        }

        return false;
    }
}
