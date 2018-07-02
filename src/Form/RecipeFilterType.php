<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\IngredientsInputType;
use App\Form\Type\RecipeAlternativeType;
use App\Form\Type\RecipeHintType;
use App\Form\Type\RecipeImageType;
use App\Form\Type\RecipeIngredientType;
use App\Form\Type\RecipeLinkType;
use App\Form\Type\RecipeListsInputType;
use App\Form\Type\RecipeStepType;
use App\Form\Type\RecipeTagsInputType;
use Brokoskokoli\StarRatingBundle\Form\StarRatingType;
use Brokoskokoli\StarRatingBundle\StarRatingBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class,
                [
                    'label' => 'label.searchText',
                    'required' => false,
                ]
            )
            ->add('recipeRating', StarRatingType::class,
                [
                    'label' => 'label.rating_minimum',
                    'required' => false,
                ]
            )
            ->add('recipeTags', RecipeTagsInputType::class,
                [
                    'label' => 'label.recipeTags',
                    'required' => false,
                ]
            )
            ->add('ingredients', IngredientsInputType::class,
                [
                    'label' => 'label.ingredients_must_have',
                    'required' => false,
                ]
            )
            ->add('ingredients_exclude', IngredientsInputType::class,
                [
                    'label' => 'label.ingredients_must_not_have',
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
                    'label' => 'label.own_recipes',
                    'required' => false,
                ]
            )
            ->add('filter', SubmitType::class,
                [
                    'label' => 'action.search',
                ])
            ->add('random', SubmitType::class,
                [
                    'label' => 'action.random_recipe',
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'user' => null,
            'recipe' => null,
        ]);
    }
}
