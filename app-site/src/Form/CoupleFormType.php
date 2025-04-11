<?php


namespace App\Form;

use App\Entity\Couple;
use App\Entity\Device;
use App\Service\DeviceService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoupleFormType extends AbstractType
{
    private $deviceService;
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Title of the couple'],
                'required' => true,
            ])
            ->add('actionDevice', EntityType::class, [
                'choices' => $this->deviceService->getUnpairedActionDevices(),
                'class' => Device::class,
                'choice_label' => 'mac_address',
                'placeholder' => 'Select action device',
                'required' => true,
            ])
            ->add('cameraDevice', EntityType::class, [
                'choices' => $this->deviceService->getUnpairedCameraDevices(),
                'class' => Device::class,
                'choice_label' => 'mac_address',
                'placeholder' => 'Select camera device',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Couple::class,
        ]);
    }
}