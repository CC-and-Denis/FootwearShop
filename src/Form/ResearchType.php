<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResearchType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('male', CheckboxType::class, [
                'required' => false,
            ])
            ->add('female', CheckboxType::class, [
                'required' => false,
            ])
            ->add('unisex', CheckboxType::class, [
                'required' => false,
            ])
            ->add('adult', CheckboxType::class, [
                'required' => false,
            ])
            ->add('kid', CheckboxType::class, [
                'required' => false,
            ])
            ->add('research_content', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'w-11/12 h-full text-2xl border-y-0 border-black px-10 mx-4 border-solid',
                ],
            ])
            ->add('submit', ButtonType::class, [
                'label' => false,  // No label
                'attr' => [
                    'class' => 'submit-btn',
                ],
            ]);

        // 'src' => '/build/images/magnifying-glass-solid.2b32bcc1.png',
    }
}
















?>