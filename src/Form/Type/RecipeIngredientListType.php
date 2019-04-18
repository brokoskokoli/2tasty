<?php

namespace App\Form\Type;


use App\Entity\Recipe;
use App\Entity\RecipeIngredientList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeIngredientListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => "label.title",
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
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RecipeIngredientList::class,
                'recipe' => null,
            ]
        );
    }
}
