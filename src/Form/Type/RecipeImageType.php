<?php
namespace App\Form\Type;


use App\Entity\ImageFile;
use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Entity\RefUnit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'imageFile',
            VichImageType::class,
            [
                'label' => "label.recipe_image",
                'allow_delete' => false,
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
            ]
        )->add(
            'altText',
            TextType::class,
            [
                'required' => false,
                'label' => 'label.alternative_text'
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ImageFile::class
            ]
        );
    }
}
