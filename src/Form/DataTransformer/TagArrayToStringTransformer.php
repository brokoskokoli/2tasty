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

use App\Entity\RecipeTag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * This data transformer is used to translate the array of recipeTags into a comma separated format
 * that can be displayed and managed by Bootstrap-recipeTagsinput js plugin (and back on submit).
 *
 * See https://symfony.com/doc/current/form/data_transformers.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Jonathan Boyer <contact@grafikart.fr>
 */
class TagArrayToStringTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($recipeTags): string
    {
        // The value received is an array of Tag objects generated with
        // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
        // The value returned is a string that concatenates the string representation of those objects

        /* @var RecipeTag[] $recipeTags */
        return implode(',', $recipeTags);
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

        // Get the current recipeTags and find the new ones that should be created.
        $recipeTags = $this->manager->getRepository(RecipeTag::class)->findBy([
            'name' => $names,
        ]);
        $newNames = array_diff($names, $recipeTags);
        foreach ($newNames as $name) {
            $tag = new RecipeTag();
            $tag->setName($name);
            $recipeTags[] = $tag;

            // There's no need to persist these new recipeTags because Doctrine does that automatically
            // thanks to the cascade={"persist"} option in the App\Entity\Recipe::$recipeTags property.
        }

        // Return an array of recipeTags to transform them back into a Doctrine Collection.
        // See Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::reverseTransform()
        return $recipeTags;
    }
}
