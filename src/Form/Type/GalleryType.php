<?php

namespace App\Form\Type;


use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GalleryType extends AbstractType
{
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label'       => false,
                'required'    => 'required',
                'trim'        => false,
                'attr'        => [
                    'placeholder' => 'Titre de la galerie',
                    'class' => 'gal-img-to-resize'
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ]
        );

        // On ajoute le champ "photos" dans le formulaire
        // Il n'est pas lié à la base de données (mapped à false)
        $builder->add('photos', FileType::class, [
            'label'    => false,
            'multiple' => true,
            'mapped'   => false,
            'required' => false,
            'attr'     => [
                'placeholder' => 'Ajouter des photos',
                'class' => 'img-to-resize'
            ],
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Save',
            'attr'  => [
                'class' => 'button'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }

}
