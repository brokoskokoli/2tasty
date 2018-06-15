<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Form\Type\DateTimePickerType;
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

class ShareMailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => 'label.title',
                    'required' => true,
                ]
            )
            ->add('link', HiddenType::class,
                [
                ]
            )
            ->add('email', EmailType::class,
                [
                    'label' => 'label.email',
                    'required' => true,
                ])
            ->add('message', TextareaType::class,
                [
                    'label' => 'label.personal_message',
                    'required' => false,
                ])
            ->add('submit', SubmitType::class,
                [
                    'label' => 'action.send',
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
