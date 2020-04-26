<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\CoursRepository;
use App\Entity\Cours;
use App\Form\CoursType;

class SiteController extends AbstractController
{
    /**
     * @Route("/",name="home")
     */
    public function home()
    {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/cours", name="site_cours")
     */
    public function index_cours(CoursRepository $repo)
    {
        $cours = $repo->findAll();

        return $this->render('site/cours.html.twig', [
            "cours" => $cours
        ]);
    }


    /**
     * @Route("/cours/{id}", name="site_show_cours", 
     * requirements= {"id" = "\d+"})
     */
    public function show_cours(Cours $cour)
    {
        return $this->render('site/showCours.html.twig', [
            "cour" => $cour
        ]);
    }

    /**
     * @Route("/cours/new", name="site_create_cours")
     * @Route("/cours/{id}/edit", name="site_edit_cours",
     *  requirements= {"id" = "\d+"})
     */
    public function form_cours(Cours $cours = null,Request $request, EntityManagerInterface $manager) 
    {
        if (!$cours) {
            $cours = new Cours();
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$cours->getID()) {
                $cours->setCreatedAt(new \DateTime());
            }

            $manager->persist($cours);
            $manager->flush();

            return $this->redirectToRoute('site_show_cours', [
                'id' => $cours->getId()]);
            }

        return $this->render("site/formCours.html.twig", [
            'formCours' => $form->createView(),
            'editMode' => $cours->getId() !== null
        ]);
    }
    
}
