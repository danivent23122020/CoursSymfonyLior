<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if(!$category){
            throw $this->createNotFoundException("La catégorie demandée n'existe pas.");
        }
        
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository){
        
        $product =$productRepository->findOneBy([
            'slug' => $slug
            ]);
            
            if(!$product){
                throw $this->createNotFoundException("Le produit n'existe pas.");
            }
            
            return $this->render('product/show.html.twig', [
                'product' => $product
                ]);
            }    
            
    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(FormFactoryInterface $factory){
        
        $builder = $factory->createBuilder();
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
        
        $form = $builder->getForm();
        
        $formView = $form->createView();
        
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}