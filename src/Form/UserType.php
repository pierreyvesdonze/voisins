<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            TextType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Username'
                ]
            ]
        );

        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Email'
                ]
            ]
        );

        $builder->add(
            'plain_password',
            PasswordType::class,
            [
                'label'  => false,
                'mapped' => false,
                'attr'   => [
                    'placeholder' => 'Password'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(["min" => 6])
                ]
            ]
        );

        $builder->add('brochure', FileType::class, [

            'label'         => 'Ajoute un avatar si tu veux (1Mo max)',
            'mapped'        => false,
            'required'      => false,
            'constraints'   => [
                new File([
                    'maxSize' => '1024k'
                ])
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
