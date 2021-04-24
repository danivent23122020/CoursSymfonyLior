<?php

namespace App\Controller;

use Attribute;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger){
        
        $builder = $factory->createBuilder(FormType::class, null, [
            'data_class' => Product::class
        ]);
        
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
        
        $form = $builder->getForm();
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            dd($product);
            }
        
        $formView = $form->createView();
        
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}