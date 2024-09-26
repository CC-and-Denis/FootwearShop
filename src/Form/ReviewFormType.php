<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Rating;
use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score',IntegerType::Class,[
                'label'=>false,
                'attr'=>[
                    'class'=>[
                        'absolute',
                        'opacity-0'
                    ]
                ],
            ])
            ->add('title',TextType::Class,[
                'label'=>'Review tittle'
            ])
            ->add('description',TextareaType::class,[
                'label'=>'description',
                
                'attr'=>[
                    'placeholder'=>".",
                    'maxlength' => 800,
                ],
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
