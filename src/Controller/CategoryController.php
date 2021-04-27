<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $category = new Category;
        
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();
        
        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }
    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, Security $security)
    {
        $user = $security->getUser();

        if($user === null){
            return $this->redirectToRoute('security_login');
        }

        if(!in_array("ROLE_ADMIN", $user->getRoles())){
            throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        }
        
        $category = $categoryRepository->find($id);
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            
            return $this->redirectToRoute('homepage');
        }
        
        
        $formView = $form->createView();
        
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}