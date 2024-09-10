<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('cardNumber', TextType::class, [
                'label' => false,
                'mapped'=> false,
                'constraints' => [
                    new Length([
                        'min' => 16, // Minimum length for card number
                        'max' => 16, // Maximum length for card number
                        'exactMessage' => 'The field "Card Number" must have exactly 16 characters.',
                    ]),
                    new Assert\CardScheme([
                        'schemes' => ['VISA', 'MASTERCARD', 'AMEX', 'DISCOVER', 'DINERS', 'JCB'],
                        'message' => 'Please enter a valid card number.',
                    ]),
                ],
                'attr' => [
                    'maxlenght' => 7,
                ]
            ])
            ->add('expMonth', ChoiceType::class, [
                'label' => false,
                'mapped' => false,
                'choices' => [
                    'Genuary (01)' => '01',
                    'February (02)' => '02',
                    'March (03)' => '03',
                    'April (04)' => '04',
                    'May (05)' => '05',
                    'June (06)' => '06',
                    'July (07)' => '07',
                    'August (08)' => '08',
                    'September (09)' => '09',
                    'October (10)' => '10',
                    'November (11)' => '11',
                    'Dicember (12)' => '12',
                ]
            ])
            ->add('expYear',ChoiceType::class, [
                'label'=>false,
                'mapped'=>false,
                'choices' => array_combine(
                    range($currentYear, $currentYear+150), // Left column (years from 2000 to 2100)
                    array_map(function($year) { return (string)$year; }, range($currentYear, $currentYear+150)) // Right column (same years as strings)
                )
            ])
            ->add('cvc', TextType::class, [
                'label' => 'CVC',
                'mapped' => false,
                'attr' => [
                    'maxlenght' => 3,
                ],
                'constraints' => [
                    new Length([
                        'min' => 3, // Minimum length for card number
                        'max' => 3, // Maximum length for card number
                        'exactMessage' => 'The field "CVC/CVV" must have exactly 16 characters.',
                    ]),]
            ])
            ->add('ccOwner', TextType::class,[
                'label' => false,
                'mapped' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        
        ]);
    }
}
