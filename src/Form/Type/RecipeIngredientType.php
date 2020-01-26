<?php

namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;
use App\Form\DataTransformer\EntityToTranslatedStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'ingredient',
            IngredientType::class,
            [
                'label' => "label.ingredient",
                'required' => false,
                'recipe' => $options['recipe'],
            ]
        )->add(
            'amount',
            NumberType::class,
            [
                'label' => "label.amount",
                'required' => false,
            ]
        )->add(
            'unit',
            EntityType::class,
            [
                'class' => RefUnit::class,
                'choice_label' => 'name',
                'label' => "label.unit",
                'placeholder' => 'form.defaultvalue.unit',
                'required' => false,
            ]
        )->add(
            'text',
            TextType::class,
            [
                'label' => 'label.alternative_text',
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RecipeIngredient::class,
                'recipe' => null,
            ]
        );
    }
}
