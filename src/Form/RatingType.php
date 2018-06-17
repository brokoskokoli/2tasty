<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\RecipeRating;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\RecipeAlternativeType;
use App\Form\Type\RecipeHintType;
use App\Form\Type\RecipeImageType;
use App\Form\Type\RecipeIngredientType;
use App\Form\Type\RecipeLinkType;
use App\Form\Type\RecipeStepType;
use App\Form\Type\RecipeTagsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class RatingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipe', HiddenEntityType::class,
                [
                    'required' => true,
                    'entity_data_class' => Recipe::class,
                ]
            )
            ->add('rating', \Brokoskokoli\StarRatingBundle\Form\StarRatingType::class,
                [
                    'label' => 'label.rating',
                    'required' => true,
                    'stars' => 5,
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'action.rate',
                    'attr' => ['class' => 'btn-primary'],
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'POST',
        ]);
    }
}
