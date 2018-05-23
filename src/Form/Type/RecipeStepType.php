<?php
namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Entity\RefUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecipeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'text',
            TextareaType::class,
            [
                'label' => "label.step",
            ]
        )->add(
            'duration',
            NumberType::class,
            [
                'label' => "label.duration",
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RecipeStep::class
            ]
        );
    }
}
