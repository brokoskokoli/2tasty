<?php

namespace App\URLParser;


use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Entity\RefUnit;
use App\Entity\RefUnitName;
use App\Helper\FileHelper;
use App\Service\ImportService;
use App\Service\RefUnitService;
use Doctrine\ORM\EntityManagerInterface;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\AbstractNode;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

class URLParserBase
{
    protected $importService;

    const MAX_NUMBER_OF_IMGAES = 10;

    /**
     * @inheritDoc
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }


    /**
     * @param Recipe $recipe
     * @param $url
     * @return Recipe
     */
    public function readSingleRecipeFromUrl(Recipe $recipe, $url)
    {
        try {
            $dom = new Dom;
            $dom->loadFromUrl($url);

            $recipe->setLanguage(Recipe::LANGUAGE_GERMAN);
            $this->importService->initForRecipe($recipe);
            $title = html_entity_decode(trim(strip_tags($dom->find('h1', 0)->innerHtml)));
            if ($title != '') {
                $recipe->setTitle($title);
            }

            $element = $dom->find('div.summary', 0);
            if ($element) {
                $recipe->setSummary(html_entity_decode($element->text));
            }

            $finalIngredientList = $this->guessIngredientList($dom, true,'ul');
            $this->addStringListAsRecipeIngredients($recipe, $finalIngredientList);

            $finalStepsList = $this->guessStepsList($dom, true,'ol');
            if (!is_iterable($finalStepsList)) {
                $finalStepsList = $this->guessStepsList($dom, true,'ul');
            }
            if (is_iterable($finalStepsList)) {
                $this->addListAsRecipeSteps($recipe, $finalStepsList);
            }


            $images = $this->guessImageList($dom);
            $this->addListAsImages($recipe, $images);

            return $recipe;
        } catch (\Exception $e) {
            dump($e);
            return null;
        }
    }

    /**
     * @param $url
     * @return bool
     */
    public function canHandleUrl($url)
    {
        return true;
    }

    protected function parseStringToRecipeIngredient(string $text) : ?RecipeIngredient
    {
        $recipeIngredient = new RecipeIngredient();

        $this->importService->parseAmount($recipeIngredient, $text);
        $this->importService->parseUnit($recipeIngredient, $text);
        $this->importService->parseIngredient($recipeIngredient, $text);

        $recipeIngredient->setText($text);

        if (!$recipeIngredient->getUnit() && !$recipeIngredient->getAmount() && !$recipeIngredient->getIngredient()) {
            return null;
        }

        return $recipeIngredient;
    }



    protected function addStringListAsRecipeIngredients(Recipe $recipe, array &$ingredientStringList)
    {
        $ingredients = [];
        foreach ($ingredientStringList as $index => &$ingredientString) {
            $ingredient = $this->parseStringToRecipeIngredient($ingredientString);
            $ingredients[] = $ingredient;
            if ($ingredient) {
                unset($ingredientStringList[$index]);
                $recipe->addRecipeIngredient($ingredient);
            }
        }
    }

    protected function guessIngredientList(Dom $dom, $asText = true, $tag = 'ul', $classesToCheck = ['recipe-ingredients', 'ingredients'])
    {
        $ingredientsLists = $dom->find($tag);
        $finalIngredientList = [];
        foreach ($ingredientsLists as $ingredientsList) {
            $classes = $ingredientsList->tag->getAttribute('class')['value'];
            foreach ($classesToCheck as $class) {
                if (stripos($classes, $class) !== false) {
                    $finalIngredientList = $ingredientsList;
                    break;
                }
            }

            if ($finalIngredientList) {
                break;
            }
        }


        if ($finalIngredientList && $asText) {
            $result = [];

            foreach ($finalIngredientList as $finalIngredient) {
                $ingredient = trim(strip_tags($finalIngredient->innerHtml));
                if ($ingredient) {
                    $result[] = preg_replace('!\s+!', ' ', $ingredient);
                }
            }

            return $result;
        }

        return $finalIngredientList;
    }

    protected function guessImageList(Dom $dom, $tags = ['.images', '.recipe-img'])
    {
        $images = [];
        foreach ($tags as $tag) {

            /** @var Collection $slideshow */
            $slideshow = $dom->find($tag);

            if ($slideshow->count()) {
                $imgs = $slideshow->find('img');
                foreach ($imgs as $img) {
                    $images[] = $img->tag->getAttribute('src')['value'];
                }
            }
        }

        return $images;
    }

    protected function guessStepsList(Dom $dom, $asText = true, $tag = 'ol', $classesToCheck = ['recipe-steps', 'steps', 'preparation'])
    {
        $stepsLists = $dom->find($tag);
        $finalStepsList = null;
        foreach ($stepsLists as $stepsList) {
            $classes = $stepsList->tag->getAttribute('class')['value'];
            foreach ($classesToCheck as $class) {
                if (stripos($classes, $class) !== false) {
                    $finalStepsList = $stepsList;
                    break;
                }
            }

            if ($finalStepsList) {
                break;
            }
        }

        if ($finalStepsList && $asText) {
            $result = [];

            foreach ($finalStepsList as $finalIngredient) {
                $ingredient = trim(strip_tags($finalIngredient->innerHtml));
                if ($ingredient) {
                    $result[] = preg_replace('!\s+!', ' ', $ingredient);
                }
            }

            return $result;
        }

        return $finalStepsList;
    }

    protected function addListAsRecipeSteps(Recipe $recipe, iterable $list)
    {
        foreach ($list as $preparationStepText) {
            $step = new RecipeStep();
            $step->setText(html_entity_decode($preparationStepText));
            $recipe->addRecipeStep($step);
        }
    }

    protected function addListAsImages(Recipe $recipe, $images)
    {
        foreach ($images as $index => $image) {
            if (pathinfo($image, PATHINFO_EXTENSION) != 'jpg') {
                continue;
            }

            if ($index >= self::MAX_NUMBER_OF_IMGAES) {
                continue;
            }

            $filename = FileHelper::getTempFileName() . '.jpg';
            if (stripos($image, 'http') !== 0) {
                $image = 'http:' . $image;
            }

            file_put_contents($filename, fopen($image, 'r'));
            $mimeType = mime_content_type($filename);
            $size = filesize($filename);

            $uploadedFile = new UploadedFile($filename, 'image' . $index . '.jpg', $mimeType, $size, null, true);

            $imageFile = new ImageFile();
            $imageFile->setImageFile($uploadedFile);
            $recipe->addImage($imageFile);
        }

        $this->importService->storeImages($recipe);
    }

    protected function getNodeFindText(AbstractNode $node, $selector, $index = 0)
    {
        /** @var Collection $nodes */
        $nodes = $node->find($selector);
        if ($nodes->count() > $index) {
            return $nodes[$index]->text(true);
        }

        return '';
    }
}
