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
use App\Entity\RecipeList;
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
class RecipeListType extends AbstractType
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
            )->add(
                'summary',
                CKEditorType::class,
                [
                    'label' => "label.summary",
                    'required' => false,
                ]
            )
            ->add('recipes',
                CollectionType::class,
                [
                    'entry_type' => \App\Form\Type\RecipeType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
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
            'data_class' => RecipeList::class,
            'user' => null,
        ]);
    }
}
