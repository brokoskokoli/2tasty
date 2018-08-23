<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Recipe;
use App\Form\Type\CollectionEntityLoadType;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\RecipeAlternativeType;
use App\Form\Type\RecipeHintType;
use App\Form\Type\RecipeImageType;
use App\Form\Type\RecipeIngredientType;
use App\Form\Type\RecipeLinkType;
use App\Form\Type\RecipeListsInputType;
use App\Form\Type\RecipeStepType;
use App\Form\Type\RecipeTagsInputType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate blog recipes.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RecipeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => ['autofocus' => true],
                    'label' => 'label.title',
                ]
            )
            ->add('language', HiddenType::class,
                [
                ]
            )
            ->add(
                'portions',
                IntegerType::class,
                [
                    'label' => 'label.portions',
                    'required' => false,
                ]
            )
            ->add(
                'summary',
                TextareaType::class,
                [
                    'label' => 'label.summary',
                    'required' => false,
                ]
            )->add(
                'workingTime',
                IntegerType::class,
                [
                    'label' => "label.working_time",
                    'required' => false,
                ]
            )->add(
                'waitingTime',
                IntegerType::class,
                [
                    'label' => "label.waiting_time",
                    'required' => false,
                ]
            )->add(
                'informations',
                CKEditorType::class,
                [
                    'label' => "label.further_informations",
                    'required' => false,
                ]
            )
            ->add('recipeIngredients',
                CollectionType::class,
                [
                    'entry_type' => RecipeIngredientType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                    'entry_options' => [
                        'recipe' => $options['recipe'],
                    ]
                ]
            )
            ->add('images',
                CollectionEntityLoadType::class,
                [
                    'entry_type' => RecipeImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                    'child_property' => 'uniqueId',
                ]
            )
            ->add('recipeSteps',
                CollectionType::class,
                [
                    'entry_type' => RecipeStepType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
                ]
            )
            ->add('recipeHints',
                CollectionType::class,
                [
                    'entry_type' => RecipeHintType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
                ]

            )
            ->add('recipeLinks',
                CollectionType::class,
                [
                    'entry_type' => RecipeLinkType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
                ]

            )
            ->add('recipeAlternatives',
                CollectionType::class,
                [
                    'entry_type' => RecipeAlternativeType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
                ]
            )
            ->add('recipeTags', RecipeTagsInputType::class,
                [
                    'label' => 'label.recipeTags',
                    'required' => false,
                ]
            )
            ->add('authorRecipeLists', RecipeListsInputType::class,
                [
                    'label' => 'label.recipeLists',
                    'required' => false,
                    'user' => $options['user'],
                    'recipe' => $options['recipe'],
                ]
            )
            ->add('private', CheckboxType::class,
                [
                    'label' => 'label.private',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'user' => null,
            'recipe' => null,
        ]);
    }
}
