<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model')
            ->add('colors')
            ->add('materials')
            ->add('brand')
            ->add('type')
            ->add('gender')
            ->add('forKids')
            ->add('price')
            ->add('description')
            ->add('quantity')
            ->add('views')
            ->add('itemsSold')
            ->add('sellerUsername', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('cartedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
