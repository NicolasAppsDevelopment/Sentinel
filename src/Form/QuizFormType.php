<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Quizz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Title of the quiz'],
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'attr' => ['placeholder' => 'Description of the quiz'],
                'required' => false,
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => QuestionFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => ['label' => false],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quizz::class,
        ]);
    }
}
