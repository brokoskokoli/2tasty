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

    protected function parseStringToRecipeIngredient(string $text) : ?RecipeIngredient
    {
        $recipeIngredient = new RecipeIngredient();

        $this->importService->parseUnit($recipeIngredient, $text);
        $this->importService->parseAmount($recipeIngredient, $text);
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

    protected function guessIngredientList(Dom $dom, $asText = true, $tag = 'ul', $classesToCheck = ['ingredient'])
    {
        $ingredientsLists = $dom->find($tag);
        $finalIngredientList = null;
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

    protected function guessStepsList(Dom $dom, $asText = true, $tag = 'ol', $classesToCheck = ['steps'])
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
            copy($image, $filename);
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
