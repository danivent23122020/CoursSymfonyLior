<?php

namespace App\Controller;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManagerInterface $em){
        
        $builder = $factory->createBuilder(ProductType::class);       
        
        $form = $builder->getForm();
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();
            
            dd($product);
            }
        
        $formView = $form->createView();
        
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}