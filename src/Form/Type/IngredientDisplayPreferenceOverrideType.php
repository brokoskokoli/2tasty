<?php

namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\IngredientDisplayPreferenceOverride;
use App\Entity\RecipeIngredient;
use App\Entity\RefIngredientDisplayPreference;
use App\Entity\RefUnit;
use App\Form\DataTransformer\EntityToTranslatedStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class IngredientDisplayPreferenceOverrideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'ingredient',
            EntityType::class,
            [
                'label' => "label.ingredient",
                'required' => true,
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'placeholder' => 'form.defaultvalue.ingredient',
                'choice_translation_domain' => 'messages',
            ]
        )->add(
            'unit',
            EntityType::class,
            [
                'class' => RefUnit::class,
                'choice_label' => 'name',
                'label' => "label.unit",
                'placeholder' => 'form.defaultvalue.unit',
                'required' => true,
                'choice_translation_domain' => 'messages',
            ]
        )->add(
            'displayPreference',
            EntityType::class,
            [
                'class' => RefIngredientDisplayPreference::class,
                'choice_label' => 'name',
                'label' => "label.ingredient_display_preference",
                'placeholder' => 'form.defaultvalue.none',
                'required' => true,
                'choice_translation_domain' => 'messages',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => IngredientDisplayPreferenceOverride::class
            ]
        );
    }
}
