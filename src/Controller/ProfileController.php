<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CoursRepository;
use App\Entity\Cours;
use App\Repository\ExerciseRepository;

/**
     * @Route("/profile", name="profile_")
     */
class ProfileController extends AbstractController
{
    /**
     * @Route("/",name="home")
     */
    public function home()
    {
        return $this->render('profile/home.html.twig');
    }

    /**
     * @Route("/cours", name="cours")
     */
    public function index_cours(CoursRepository $repo)
    {
        $cours = $repo->findAll();

        return $this->render('profile/cours.html.twig', [
            "cours" => $cours
        ]);
    }

    /**
     * @Route("/cours/{id}", name="show_cours", 
     * requirements= {"id" = "\d+"})
     */
    public function show_cours(Cours $cour)
    {
        return $this->render('profile/showCours.html.twig', [
            "cour" => $cour
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}", name="show_exercises")
     */
    public function index_exo($id1, $id2, ExerciseRepository $repo) 
    {

        $exercise = $repo->find($id2);

        return $this->render('profile/showExercise.html.twig', [
            "exercise" => $exercise
        ]);
    }
    
}