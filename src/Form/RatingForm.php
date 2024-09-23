<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\File;




class RatingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title',TextType::class,[
                'attr'=>[
                    'maxlength'=>50
                ],
            ])
            ->add('description',TextType::class,[
                    'attr'=>[
                        'maxlength'=>200
                    ],
                ]
            )
            ->add('score',ChoiceType::class,[
                'choices'=>[
                    'Terrible' => '0',
                    'Bad' => '1',
                    'Decent' => '2',
                    'Good' => '3',
                    'Very good' => '4',
                    'Excellent' => '5',
                ]
            ])
            ->add('product', EntityType::class, []);
    }
}
