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




class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model',TextType::class,[                    'label'=>false,
                    'attr'=>[
                        'placeholder'=>".",
                        'maxlenght'=>35
                    ],
                    ]
                    )
            ->add('colors',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    'White' => 'white',
                    'Gray' => 'gray',
                    'Black' => 'black',
                    'Brown' => 'brown',
                    'Pink' => 'pink',
                    'Purple' => 'purple',
                    'Blue' => 'blue',
                    'Green' => 'green',
                    'Yellow' => 'yellow',
                    'Orange' => 'orange',
                    'Red' => 'red',
                    'Other' => 'other',
                ]
            ])
            ->add('materials',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    "Leather" => "Leather",
                    "Rubber" => "Rubber",
                    "Textile" => "Textile",
                    "Foam" => "Foam",
                    "Plastic" => "Plastic",
                    "Ethylene Vinyl Acetate" => "Ethylene Vinyl Acetate",
                    "Mesh" => "Mesh",
                    "Suede" => "Suede",
                    "Synthetic leather" => "Synthetic leather",
                    "Canvas" => "Canvas",
                    "Polyurethane (PU)" => "Polyurethane (PU)",
                    "Nylon" => "Nylon",
                    "Cotton" => "Cotton",
                    "Polyester" => "Polyester",
                    "Gore-Tex" => "Gore-Tex",
                    "Neoprene" => "Neoprene",
                    "Other" => "Other"
                ]
            ])
            ->add('brand',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    "Nike" => "Nike",
                    "Adidas" => "Adidas",
                    "Puma" => "Puma",
                    "Reebok" => "Reebok",
                    "Asics" => "Asics",
                    "New Balance" => "New Balance",
                    "Under Armour" => "Under Armour",
                    "Vans" => "Vans",
                    "Converse" => "Converse",
                    "Timberland" => "Timberland",
                    "Sketchers" => "Sketchers",
                    "Salomon" => "Salomon",
                    "Merrell" => "Merrell",
                    "Fila" => "Fila",
                    "Birkenstock" => "Birkenstock",
                    "Clarks" => "Clarks",
                    "Hoka One One" => "Hoka One One",
                    "Dr. Martens" => "Dr. Martens",
                    "UGG" => "UGG",
                    "Saucony" => "Saucony",
                    "Columbia" => "Columbia",
                    "Crocs" => "Crocs",
                    'Other' => 'Other'
                ]
            ])
            ->add('type',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    'Running' => 'Running',
                    'Hiking' => 'Hiking',
                    'Sandals' => 'Sandals',
                    'Heels' => 'Heels',
                    'Boots' => 'Boots',
                    'Ankle boots' => 'Ankle boots',
                    'Sneakers' => 'Sneakers',
                    'Formal' => 'Formal',
                    'flip flops' => 'flip flops',
                    'Other' => 'Other'
                ]
            ])
            ->add('gender',ChoiceType::class,[
                'label'=>false,
                'choices'=>[
                    "Unisex"=>"unisex",
                    "Male"=>"male",
                    "Female"=>"female",
                ]
            ])
            ->add('forKids',CheckboxType::class, [
                'label' => false,
                'required' => false,     // The checkbox is not required
                'empty_data' => false,
            ])
            ->add('price',MoneyType::class,[
                'label'=>false,
                'attr'=>[
                    'pattern' => '[0-9\.]+',
                    'placeholder'=>".",
                    'min'=>0.2,
                    'max'=>1000
                ]
            ],
            )
            ->add('description',TextareaType::class,[
                'label'=>false,
                'attr'=>[
                    'placeholder'=>"."
                ],
                'required'=>false,
            ])
            ->add('quantity',IntegerType::class)
            ->add('mainImage',FileType::class,[
                'required'=>true,
            ])
            ->add('otherImages',FileType::class,[
                'label' => 'Upload Files',
                'multiple' => true,  // Allows multiple files to be selected
                'mapped' => false,    // This field is not associated with any entity property
                'required' => false,  // Makes the file input optional
                'attr' => [
                    'multiple' => 'multiple'  // Allows the user to select multiple files
                ],
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
