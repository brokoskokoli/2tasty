<?php

namespace App\Form\Type;

use App\Form\DataTransformer\EntityToIdStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Entity hidden custom type class definition
 */
class HiddenEntityType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // attach the specified model transformer for this entity list field
        // this will convert data between object and string formats
        $builder->addModelTransformer(new EntityToIdStringTransformer($this->manager, $options['entity_data_class']), true);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return HiddenType::class;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entity_data_class' => null,
        ]);
    }
}