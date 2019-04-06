<?php

namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeIngredientList;
use App\Entity\RefUnit;
use App\Form\DataTransformer\EntityToTranslatedStringTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class RecipeIngredientListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            IngredientType::class,
            [
                'label' => "label.ingredient",
                'required' => false,
                'recipe' => $options['recipe'],
            ]
        )
        ->add('recipes',
            EntityType::class,
            [
                'label' => "label.recipes",
                'required' => false,
                'class' => Recipe::class,
                'choice_label' => 'title',
                'multiple' => true,
                'by_reference' => false,
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
