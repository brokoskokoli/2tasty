<?php

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Service\IngredientService;
use App\Service\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Translation\TranslatorInterface;

class EntityToTranslatedStringTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $translator;
    private $translationService;

    /**
     * @var Recipe
     */
    protected $recipe;

    /**
     * @var IngredientService
     */
    private $ingredientService;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator, TranslationService $translationService, IngredientService $ingredientService)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->translationService = $translationService;
        $this->ingredientService = $ingredientService;
    }

    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    /**
     * Transforms an object ($ingredient) to a string.
     *
     * @param  Ingredient|null $ingredient
     * @return string
     */
    public function transform($ingredient)
    {
        if (null === $ingredient) {
            return '';
        }

        if(!$ingredient instanceof Ingredient) {
            return '';
        }

        $locale = $this->translator->getLocale();
        if ($this->recipe && $this->recipe->getLanguage()) {
            $locale = $this->recipe->getLanguage();
        }

        $function = 'get' . $locale;
        return $ingredient->$function() ?? $ingredient->getName();
    }

    /**
     * @param $ingredientText
     * @return Ingredient|null
     */
    public function reverseTransform($ingredientText)
    {
        // no issue number? It's optional, so that's ok
        if (!$ingredientText) {
            return null;
        }

        $locale = null;
        if ($this->recipe && $this->recipe->getLanguage()) {
            $locale = $this->recipe->getLanguage();
        }

        return $this->ingredientService->getIngredientFromString($ingredientText, $locale);
    }
}
