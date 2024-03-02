<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dueDate', DateTimeType::class, [
                'required' => true,
                'label' => 'Date d\'écheance:',
                'empty_data' => (new \DateTime('now'))->format('Y-m-d H:i:s'),
                'constraints'=> [
                    new Assert\Type("\DateTimeInterface"),
                ],
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'help' => 'Le titre de la tâche',
                'help_attr' => [
                    'class' => 'blue-help',
                ],
                'label' => 'Titre:',
                'label_attr' => [
                    'class' => 'blue-help',
                ],
                'row_attr' => [
                    'id' => 'taskTitle',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le titre de la tâche est obligatoire',
                    ]),
                    new Assert\Type("string"),
                    new Assert\Length([
                        'max' => 255,
                        'min' => 3,
                        'maxMessage' => '255 caractères max!',
                        'minMessage' => '3 caractères minimum.'
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Description',
                'attr' => [
                    'rows' => 4,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description de la tâche est obligatoire',
                    ]),
                    new Assert\Type("string"),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => '3 caractères minimum.'
                    ])
                ],
            ])
            ->add('cover', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Image de couverture',
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Formats acceptés: jpg, png, svg, webp uniquement.'
                    ])
                ]
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
