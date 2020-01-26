<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use App\Entity\RecipeTag;
use App\Service\IngredientService;
use function Clue\StreamFilter\fun;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This data transformer is used to translate the array of recipeTags into a comma separated format
 * that can be displayed and managed by Bootstrap-recipeTagsinput js plugin (and back on submit).
 *
 * See https://symfony.com/doc/current/form/data_transformers.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Jonathan Boyer <contact@grafikart.fr>
 */
class IngredientArrayToStringTransformer implements DataTransformerInterface
{
    private $manager;
    private $translator;
    private $ingredientService;

    public function __construct(TranslatorInterface $translator, ObjectManager $manager, IngredientService $ingredientService)
    {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->ingredientService = $ingredientService;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($recipeTags): string
    {
        // The value received is an array of Tag objects generated with
        // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
        // The value returned is a string that concatenates the string representation of those objects

        $translator = $this->translator;
        /* @var Ingredient $ingredient */
        return implode(',', array_map(function ($ingredient) use ($translator) {
            return $translator->trans($ingredient->getName(), [], 'messages');
        }, $recipeTags));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string): array
    {
        if ('' === $string || null === $string) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', explode(',', $string))));

        $ingredients = [];
        foreach ($names as $name) {
            $ingredients[] = $this->ingredientService->getIngredientFromString($name);
        }

        return $ingredients;
    }
}
