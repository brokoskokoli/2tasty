<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Entity\RecipeList;
use App\Form\DataTransformer\IngredientArrayToStringTransformer;
use App\Form\DataTransformer\ListArrayToStringTransformer;
use App\Form\DataTransformer\TagArrayToStringTransformer;
use App\Service\IngredientService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Defines the custom form field type used to manipulate recipeTags values across
 * Bootstrap-recipeTagsinput javascript plugin.
 *
 * See https://symfony.com/doc/current/cookbook/form/create_custom_field_type.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class  IngredientsInputType extends AbstractType
{
    private $manager;
    private $translator;
    private $ingredientService;

    public function __construct(ObjectManager $manager, TranslatorInterface $translator, IngredientService $ingredientService)
    {
        $this->manager = $manager;
        $this->translator = $translator;
        $this->ingredientService = $ingredientService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // The Tag collection must be transformed into a comma separated string.
            // We could create a custom transformer to do Collection <-> string in one step,
            // but here we're doing the transformation in two steps (Collection <-> array <-> string)
            // and reuse the existing CollectionToArrayTransformer.
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new IngredientArrayToStringTransformer($this->translator, $this->manager, $this->ingredientService), true)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['ingredients'] = $this->ingredientService->getAllNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

}
