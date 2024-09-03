<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Card Number',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('expMonth', TextType::class, [
                'label' => 'Expiration Month',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('expYear', TextType::class, [
                'label' => 'Expiration Year',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('cvc', TextType::class, [
                'label' => 'CVC',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Pay']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // n
        ]);
    }
}
