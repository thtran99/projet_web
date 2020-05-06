<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Repository\CoursRepository;
use App\Entity\Cours;
use App\Entity\Line;
use App\Entity\LinesTask;
use App\Form\StudentLinesTaskType;
use App\Form\StudentLineType;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="home")
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
        $cours = $repo->findBy([], ['createdAt' => 'desc']);

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

    // /**
    //  * @Route("/cours/{id1}/exercice/{id2}", name="show_exercises")
    //  */
    // public function show_exo($id1, $id2, ExerciseRepository $repo)
    // {

    //     $exercise = $repo->find($id2);

    //     $random_lignes = $exercise->getLignes()->toArray();

    //     shuffle($random_lignes);

    //     return $this->render('profile/showExercise.html.twig', [
    //         'exercise' => $exercise,
    //         'random_lignes' => $random_lignes
    //     ]);
    // }


    /**
     * @Route("/cours/{id}/inscription", name="registerLesson",
     * requirements= {"id" = "\d+"})
     */
    public function registerLesson(Cours $cour, EntityManagerInterface $manager)
    {

        if (!$this->isGranted('ROLE_EDITOR')) {

            $user = $this->getUser();

            $cour->addUser($user);

            $manager->flush();

            return $this->redirectToRoute("profile_home");
        }

        return $this->redirectToRoute("profile_home");
    }


    /**
     * @Route("/cours/{id}/desinscription", name="unregisterLesson",
     * requirements= {"id" = "\d+"})
     */
    public function unregisterLesson(Cours $cour, EntityManagerInterface $manager)
    {

        if (!$this->isGranted('ROLE_EDITOR')) {

            $user = $this->getUser();

            $cour->removeUser($user);

            $manager->flush();

            return $this->redirectToRoute("profile_home");
        }

        return $this->redirectToRoute("profile_home");
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}", name="show_exercises")
     */
    public function valideLines($id1, $id2, Request $request, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);
        $random_lignes = $exercise->getLignes()->toArray();
        shuffle($random_lignes);
        $task = new LinesTask();

        for ($i = 0; $i < $exercise->getnbLines(); $i++) {
            $line = new Line();
            $line->setRanking($i);
            $task->getLines()->add($line);
        }

        $form = $this->createForm(StudentLinesTaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $error_content = [];
            $error_indent = [];
            $succes = false;

            for ($i = 0; $i < $exercise->getnbLines(); $i++) {
                $etu_line = $task->getLines()->get($i);
                $exo_line = $exercise->getLignes()->get($i);
                if ($etu_line->getContent() != $exo_line->getContent()) {
                    array_push($error_content, 'ligne ' . $i);
                } else if ($etu_line->getIndentation() != $exo_line->getIndentation()) {
                    array_push($error_indent, 'ligne ' . $i);
                }
            }

            if (empty($error_indent) && empty($error_indent)) {
                $succes = true;
            }

            return $this->render('profile/showExercise.html.twig', [
                'succes' => $succes,
                'error_content' => $error_content,
                'error_indent' => $error_indent,
                'random_lignes' => $random_lignes,
                'exercise' => $exercise,
                'form' => $form->createView(),
                'id1' => $id1,
                'id2' => $id2
            ]);
        }

        return $this->render('profile/showExercise.html.twig', [
            'succes' => false,
            'random_lignes' => $random_lignes,
            'exercise' => $exercise,
            'form' => $form->createView(),
            'id1' => $id1,
            'id2' => $id2
        ]);
    }
}
