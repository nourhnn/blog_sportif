<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class,[
            'required' => true,
            'label'=> 'Name:',
            'help'=> 'le nom de l\'article',
            'constraints'=>[
                new Assert\NotBlank([
                    'message' => 'Le titre de l\'article est obligatoire'
                ]),
                new Assert\Type("string"),
                new Assert\Length([
                    'min' => 3,
                    'max' => 80,
                    'minMessage' => "Votre titre doit contenir au moins 5 caractères",
                    'maxMessage' => "Vous avez dépasse le nombre max de caractére",
                ]),
            ],
        ])
        ->add('description', TextType::class,[
            'required' => true,
            'label'=> 'Description:',
            'help'=> 'le nom de la description',
            'constraints'=>[
                new Assert\NotBlank([
                    'message' => 'La description de l\'article est obligatoire'
                ]),
                new Assert\Type("string"),
                new Assert\Length([
                    'min' => 5,
                    'minMessage' => "Votre description doit contenir au moins 5 caractères",
                ]),
            ],
        ])
        ->add('Enregister', SubmitType::class)
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
