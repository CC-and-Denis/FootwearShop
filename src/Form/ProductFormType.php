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




class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $items_sold = $options['data']->getItemsSold();
        $mainImg = $options['data']->getMainImage();
        $otherImages = $options['data']->getOtherImages();

        $builder
            ->add('model',TextType::class,[                    
                'label'=>"Shoe model",
                    'attr'=>[
                        'placeholder'=>".",
                        'maxlenght'=>35

                    ],
                    ]
                    )
            ->add('color',ChoiceType::class,[
                'label'=>"Main shoe color",
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
            ->add('material',ChoiceType::class,[
                'label'=>"Main shoe material",
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
                'label'=>"Shoe brand",
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
                'label'=>"Shoe type",
                'choices'=>[
                    'Trekking' => 'trekking',
                    'Running' => 'running',
                    'Hiking' => 'hiking',
                    'Sandals' => 'sandals',
                    'Heels' => 'heels',
                    'Boots' => 'boots',
                    'Ankle boots' => 'ankle boots',
                    'Sneakers' => 'sneakers',
                    'Formal' => 'formal',
                    'flip flops' => 'flip flops',
                    'Other' => 'other'
                ]
            ])
            ->add('gender',ChoiceType::class,[
                'label'=>'Gender',
                'choices'=>[
                    "Unisex"=>"unisex",
                    "Male"=>"male",
                    "Female"=>"female",
                ]
            ])
            ->add('size',ChoiceType::class,[
                'label'=>false,
                'choices' => array_combine(range(20, 56), range(20, 56)),
            ])
            ->add('forKids',CheckboxType::class, [
                'label' => "Kids shoe",
                'required' => false,     // The checkbox is not required
            ])
            ->add('price',MoneyType::class,[
                'label'=>"Price",
                'constraints' => [
                    new Range([
                        'min' => 0.2, // Minimum value
                        'max' => 10000, // Maximum value
                        'notInRangeMessage' => 'The price must be between {{ min }} and {{ max }}.',
                    ]),
                ],
                'attr'=>[
                    'pattern' => '[0-9\.]+',
                    'placeholder'=>".",
                    'min'=>0.2,
                    'max'=>10000,
                    'maxlength'=>7,
                ]
            ],
            )
            ->add('description',TextareaType::class,[
                'label'=>'description',
                
                'attr'=>[
                    'placeholder'=>".",
                    'maxlength' => 800,
                ],
                
            ])
            ->add('quantity',IntegerType::class,[
                'label'=>'Quantity',
                'mapped'=>false,
            ]);
            if(! $mainImg){
                $builder->add('mainImage',FileType::class,[
                    'data_class'=>null,
                    'required'=>true,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image (JPG,JPEG or PNG).',
                        ]),
                    ],
    
                ]);
            }
            if(!count($otherImages)){
                $builder->add('otherImages', FileType::class,[
                    'label'=>false,
                    'mapped'=>false,
                    'multiple'=>true,
                    'attr'=>[
                        
                        'multiple'=>true,
                        
                    ],
                    
                    'required' => false,
                ]);
            }
            


        
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
