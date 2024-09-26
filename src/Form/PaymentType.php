<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\AddressType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = date("Y") - 1;

        $builder
            ->add('addressCountry', TextType::class, [
            'label' => "Country",
            'mapped' => false,
            ])
            ->add('addressCity', TextType::class, [
                'label' => "City",
                'mapped' => false,
            ])
            ->add('addressPostal', TextType::class, [
                'label' => 'Zip code',
                'mapped' => false,
            ]);
           

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        
        ]);
    }
}
