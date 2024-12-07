<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statement', TextType::class, [
                'attr' => ['placeholder' => 'Text of the question'],
                'required' => true,
            ])
            ->add('position', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Order of the question in the quiz',
                    'min' => 1,
                ],
                'required' => false,
            ])
            ->add('ressourceFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'audio/mp4',
                            'audio/mpeg',
                            'audio/ogg',
                            'audio/wav',
                            'image/avif',
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'video/mp4',
                            'video/mpeg',
                            'video/ogg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid document',
                    ])
                ],
            ])
            ->add('removeFile', CheckboxType::class, [
                'label' => 'Remove the current file',
                'required' => false,
                'mapped' => false,
            ])
            ->add('answer1', AnswerFormType::class, [
                'label' => 'Answer 1',
                'required' => true,
            ])
            ->add('answer2', AnswerFormType::class, [
                'label' => 'Answer 2',
                'required' => true,
            ])
            ->add('answer3', AnswerFormType::class, [
                'label' => 'Answer 3',
                'required' => false,
            ])
            ->add('answer4', AnswerFormType::class, [
                'label' => 'Answer 4',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
