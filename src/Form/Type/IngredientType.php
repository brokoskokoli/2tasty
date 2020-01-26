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

class IngredientType extends AbstractType
{

    private $entityTransformer;

    public function __construct(EntityToTranslatedStringTransformer $transformer)
    {
        $this->entityTransformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['recipe'] !== null) {
            $this->entityTransformer->setRecipe($options['recipe']);
        }
        $builder->addModelTransformer($this->entityTransformer);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'recipe' => null,
            ]
        );
    }
}
