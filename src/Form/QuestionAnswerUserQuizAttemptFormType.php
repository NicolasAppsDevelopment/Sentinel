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

class QuestionAnswerUserQuizAttemptFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answer1', CheckboxType::class, [
                'label' => $options['answer1'],
                'required' => false,
            ])
            ->add('answer2', CheckboxType::class, [
                'label' => $options['answer2'],
                'required' => false,
            ])
            ->add('answer3', CheckboxType::class, [
                'attr' => ['class' => $options['answer3'] ? '' : 'd-none'],
                'label' => $options['answer3'] ?? false,
                'required' => false,
            ])
            ->add('answer4', CheckboxType::class, [
                'attr' => ['class' => $options['answer3'] ? '' : 'd-none'],
                'label' => $options['answer4'] ?? false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ])
        ->setRequired('answer1')
        ->setRequired('answer2')
        ->setRequired('answer3')
        ->setRequired('answer4');
    }
}
