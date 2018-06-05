<?php

namespace App\URLParser;


use App\Entity\ImageFile;
use App\Entity\Recipe;
use App\Entity\RecipeStep;
use App\Helper\FileHelper;
use App\Service\ImportService;
use App\Service\RefUnitService;
use Doctrine\ORM\EntityManagerInterface;
use PHPHtmlParser\Dom\AbstractNode;
use PHPHtmlParser\Dom\Collection;
use PHPHtmlParser\Dom\HtmlNode;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

class URLParserBase
{
    protected $importService;

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

            $filename = FileHelper::getTempFileName().'.jpg';
            copy($image, $filename);
            $mimeType = mime_content_type($filename);
            $size = filesize ($filename);

            $uploadedFile = new UploadedFile($filename,'image'.$index.'.jpg',$mimeType, $size, null, true);

            $imageFile = new ImageFile();
            $imageFile->setImageFile($uploadedFile);
            $recipe->addImage($imageFile);
        }
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
