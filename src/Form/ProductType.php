<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             // input name
            ->add('name', TextType::class, [
                'label'=> 'Nom du produit', 
                'attr'=> ['placeholder'=> 'Tapez le nom du produit']        
                ])
            // input textarea
            ->add('shortDescription', TextareaType::class, [
                'label'=>'Description courte',
                'attr'=>[
                'placeholder' => 'Tapez une descrition assez courte mais parlante pour le       visiteur' ]
                ])
            // input price
            ->add('price', MoneyType::class, [
                'label'=> 'Prix du produit',
                'attr'=>[
                    'placeholder'=>'Tapez le prix du produit']
            ])
            // input picture
            ->add('picture', UrlType::class, [
                'label' => 'Image du produit',
                'attr'=>['placeholder' => 'Tapez une url d\'image'],
                ])
            // input category
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr'=>['placeholder' => '-- Choisir une catégorie --'],
                'class' => Category::class,
                // 'choice_label' => 'name' OU en voulant les catégories en majuscule
                'choice_label' => function(Category $category){
                    return strtoupper($category->getName());
                }
            ]);
            
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            $product = $event->getData();

            if($product->getPrice() !== null){
                $product->setPrice($product->getPrice() * 100);
            }
        });
            
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $form = $event->getForm();
            
            /**
             * @var Product
             */
            $product = $event->getData();
            
            if($product->getPrice() !== null){
                $product->setPrice($product->getPrice() / 100);
            }
            
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}