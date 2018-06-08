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

use App\Entity\RecipeList;
use App\Entity\RecipeTag;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This data transformer is used to translate the array of recipeTags into a comma separated format
 * that can be displayed and managed by Bootstrap-recipeTagsinput js plugin (and back on submit).
 *
 * See https://symfony.com/doc/current/form/data_transformers.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Jonathan Boyer <contact@grafikart.fr>
 */
class ListArrayToStringTransformer implements DataTransformerInterface
{
    private $manager;
    private $user;

    public function __construct(ObjectManager $manager, ?User $user = null)
    {
        $this->manager = $manager;
        $this->user = $user;

    }

    /**
     * {@inheritdoc}
     */
    public function transform($recipeLists): string
    {
        // The value received is an array of Tag objects generated with
        // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
        // The value returned is a string that concatenates the string representation of those objects

        /* @var RecipeList[] $recipelists */
        return implode(',', array_map(function ($recipeList) {
            return $recipeList->getName();
        }, $recipeLists));
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
        $recipeLists = $this->manager->getRepository(RecipeList::class)->findBy([
            'name' => $names,
            'author' => $this->user,
        ]);
        $newNames = array_diff($names, $recipeLists);
        foreach ($newNames as $name) {
            $list = new RecipeList();
            $list->setName($name);
            $list->setAuthor($this->user);
            $recipeLists[] = $list;
        }

        return $recipeLists;
    }
}
