<?php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingFormType extends AbstractType
{
    public function __construct() {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}