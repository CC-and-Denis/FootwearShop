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

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'label' => 'Card Number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your card number',
                    ]),
                    new Length([
                        'min' => 16, // Minimum length for card number
                        'max' => 16, // Maximum length for card number
                        'minMessage' => 'Your card number must be at least {{ limit }} characters long',
                        'maxMessage' => 'Your card number cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'maxlenght' => 7,
                ]
            ])
            ->add('expMonth',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    'Genuary' => '01',
                    'February' => '02',
                    'March' => '03',
                    'April' => '04',
                    'May' => '05',
                    'June' => '06',
                    'July' => '07',
                    'August' => '08',
                    'September' => '09',
                    'October' => '10',
                    'November' => '11',
                    'Dicember' => '12',
                ]
            ])
            ->add('expYear',ChoiceType::class,[
                'label'=>false,
                'choices' => array_combine(
                    range(2000, 2100), // Left column (years from 2000 to 2100)
                    array_map(function($year) { return (string)$year; }, range(2000, 2100)) // Right column (same years as strings)
                )
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
