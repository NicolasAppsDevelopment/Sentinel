<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingFormType extends AbstractType
{
    public function __construct() {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serverTime', DateTimeType::class, [
                'required' => false,
            ])
            ->add('accessPointName', TextType::class, [
                'required' => false,
            ])
            ->add('accessPointPassword', PasswordType::class, [
                'required' => false,
            ])
            ->add('accessPointPasswordConfirm', PasswordType::class, [
                'required' => false,
            ])
            ->add('mondayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('mondayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('tuesdayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('tuesdayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('wednesdayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('wednesdayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('thursdayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('thursdayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('fridayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('fridayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('saturdayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('saturdayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('sundayFrom', TimeType::class, [
                'required' => false,
            ])
            ->add('sundayTo', TimeType::class, [
                'required' => false,
            ])
            ->add('sendMail', CheckboxType::class, [
                'required' => false,
                'label' => 'Send you mail on new detection (1 per hour max)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}