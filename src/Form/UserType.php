<?php

namespace App\Form;

use App\Entity\IngredientDisplayPreferenceOverride;
use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\RefIngredientDisplayPreference;
use App\Entity\User;
use App\Form\Type\IngredientDisplayPreferenceOverrideType;
use App\Form\Type\RecipeAlternativeType;
use App\Form\Type\RecipeHintType;
use App\Form\Type\RecipeImageType;
use App\Form\Type\RecipeIngredientType;
use App\Form\Type\RecipeStepType;
use App\Form\Type\RecipeTagsInputType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();
        $builder
            ->add(
                'fullName',
                TextType::class,
                [
                    'attr' => ['autofocus' => true],
                    'label' => 'label.fullname',
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'label.username',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'label.email',
                ]
            )
            ->add(
                'ingredientDisplayPreference',
                EntityType::class,
                [
                    'class' => RefIngredientDisplayPreference::class,
                    'choice_label' => 'name',
                    'label' => "label.ingredient_display_preference",
                    'choice_translation_domain' => 'messages',
                    'required' => true,
                ]
            )
            ->add('ingredientDisplayPreferenceOverrides',
                CollectionType::class,
                [
                    'entry_type' => IngredientDisplayPreferenceOverrideType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true
                ]
            )
            ->add('dailyDishRecipeList',
                EntityType::class,
                [
                    'class' => RecipeList::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Alle Rezepte',
                    'label' => "label.daily_dish_recipe_list",
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        $queryBuilder = $er->createQueryBuilder('rl');
                        $queryBuilder->andWhere('rl.author = :user');
                        $queryBuilder->setParameter('user', $user);
                        return $queryBuilder;
                    },
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => "label.profile_image",
                    'allow_delete' => false,
                    'download_uri' => true,
                    'image_uri' => true,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => "action.save",
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
            'data_class' => User::class,
        ]);
    }
}
