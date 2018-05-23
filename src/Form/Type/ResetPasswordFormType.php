<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'label.password'
                    ],
                    'second_options' => [
                        'label' => 'label.password_repeat'
                    ],
                    'invalid_message' => 'message.password_not_equal',
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            "min" => "8",
                            "minMessage" =>"message.password_min_length"
                        ]),
                        new Regex([
                            "pattern"=>"/\d+/",
                            "message"=>"message.password_no_number"
                        ]),
                    ]
                ]
            )
        ;
    }
}
