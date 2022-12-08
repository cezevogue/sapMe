<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{

    #[Route('/ajoutProduit', name: 'ajoutProduit')]
    public function ajoutProduit(Request $request, EntityManagerInterface $manager): Response
    {
        // cette méthode doit nous permettre de créer un nouveau produit. On instancie donc un objet Product de App\Entity que l'on va remplir de toutes ses propriétés
        $product=new Product();

        // ici on instancie un objet Form via la méthode createForm() existante de notre abstractController
        $form=$this->createForm(ProductType::class, $product);
        // cette méthode attend en argument le formulaire à utiliser et l'objet Entité auquel il fait référence, ainsi il va controler la conformité entre les champs de formulaire et les propriétés présentes dans l'entité pour pouvoir remplir l'objet Product par lui-même.

        // grace à la méthode handleRequest de notre objet de formulaire, il charge à présent l'objet Product de données receptionnées du formulaire présentent dans notre objet request (Request étant la classe de symfony qui récupère la majeur partie des données de superGlobale =>$_GET, $_POST ...)
        $form->handleRequest($request);

        // $request->request est la surcouche de $_POST. ->get() permet d'accéder à une entrée de notre tableau de donnée
        //$request->request->get('title');

        //  pour accéder à la surcouche de $_GET on utilise $request->query
        // qui possède les mêmes méthodes que $request->request

        if ($form->isSubmitted() && $form->isValid()){

            //dd($product);
           // dd($form->get('picture')->getData());
            // on récupère les données de notre input type File du formulaire qui a pour name 'picture'
            $picture=$form->get('picture')->getData();
            // condition d'upload de photo
            if ($picture){

                $picture_bdd=date('YmdHis').uniqid().$picture->getClientOriginalName();

                $picture->move($this->getParameter('upload_directory'),$picture_bdd);
                // move() est une méthode de notre objet File qui permet de déplacer notre fichier temporaire uploadé à un emplacement donné (le 1er paramètre) et de nommé ce fichier (le second paramètre de la méthode)

                $product->setPicture($picture_bdd);
                $manager->persist($product);
                $manager->flush();


            }


        }


        return $this->render('back/ajoutProduit.html.twig', [
            'form'=>$form->createView()

        ]);
    }


}
