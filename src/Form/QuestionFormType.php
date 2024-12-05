<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statement', TextType::class)
            ->add('attachement', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('answer1', AnswerFormType::class, [
                'required' => true,
            ])
            ->add('answer2', AnswerFormType::class, [
                'required' => true,
            ])
            ->add('answer3', AnswerFormType::class, [
                'required' => false,
            ])
            ->add('answer4', AnswerFormType::class, [
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
