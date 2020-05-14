<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\CoursRepository;
use App\Repository\ExerciseRepository;
use App\Repository\NotationRepository;
use App\Entity\Cours;
use App\Entity\Line;
use App\Entity\LinesTask;
use App\Entity\Notation;
use App\Form\StudentLinesTaskType;

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
            "lessons" => $cours
        ]);
    }

    /**
     * @Route("/cours/{id}", name="show_cours", 
     * requirements= {"id" = "\d+"})
     */
    public function show_cours(Cours $cour)
    {
        return $this->render('profile/showCours.html.twig', [
            "lesson" => $cour
        ]);
    }

    /**
     * @Route("/cours/{id}/inscription", name="registerLesson",
     * requirements= {"id" = "\d+"})
     */
    public function registerLesson($id, Cours $cour, EntityManagerInterface $manager)
    {

        if (!$this->isGranted('ROLE_EDITOR')) {

            $user = $this->getUser();

            $cour->addUser($user);

            $manager->flush();

            return $this->redirectToRoute("profile_show_cours", [
                'id' => $id
            ]);
        }

        return $this->redirectToRoute("profile_show_cours", [
            'id' => $id
        ]);
    }


    /**
     * @Route("/cours/{id}/desinscription", name="unregisterLesson",
     * requirements= {"id" = "\d+"})
     */
    public function unregisterLesson($id, Cours $cour, EntityManagerInterface $manager)
    {

        if (!$this->isGranted('ROLE_EDITOR')) {

            $user = $this->getUser();

            $cour->removeUser($user);

            $manager->flush();

            return $this->redirectToRoute("profile_show_cours", [
                'id' => $id
            ]);
        }

        return $this->redirectToRoute("profile_show_cours", [
            'id' => $id
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}", name="show_exercises")
     */
    public function valideLines($id1, $id2, Request $request,  EntityManagerInterface $manager, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);
        /* On mélange les lignes pour que l'étudiant les remette en place */
        $random_lignes = $exercise->getLignes()->toArray();
        shuffle($random_lignes);
        $task = new LinesTask();
        $notation = null;


        /* Récupere la note si elle existe sinon la créer */
        $user = $this->getUser();
        $notation = $user->getNotation($exercise);
        if ($notation == null) {
            $notation = new Notation();
            $notation->setStudent($user);
            $notation->setExercise($exercise);
            $notation->setNote(0);
        }

        $succes = $notation->getNote() == 100;


        for ($i = 0; $i < $exercise->getnbLines(); $i++) {
            $line = new Line();
            $line->setRanking($i);
            $task->getLines()->add($line);
        }

        $form = $this->createForm(StudentLinesTaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $error_content = []; // erreur lié aux lignes
            $error_indent = []; // erreur lié aux indentation

            $good_line = 0;
            $note = 0;

            /* On analyse ligne par ligne si les entrées de l'étudiant correspondent à la correction  */
            for ($i = 0; $i < $exercise->getnbLines(); $i++) {
                $etu_line = $task->getLines()->get($i);
                $exo_line = $exercise->getLignes()->get($i);
                if ($etu_line->getContent() != $exo_line->getContent()) {
                    array_push($error_content, 'ligne ' . $i);
                } else if ($etu_line->getIndentation() != $exo_line->getIndentation()) {
                    array_push($error_indent, 'ligne ' . $i);
                } else {
                    $good_line++;
                }
            }

            $exercise->addAttemps();

            /* On attribue la note en pourcentage */
            $note =  $good_line * 100 / $exercise->getnbLines();
            $notation->setNote($note);
            $manager->persist($notation);
            $manager->flush();
            /* Fin de la note */

            if ($note == 100) {
                $succes = true;
            }

            return $this->render('profile/showExercise.html.twig', [
                'note' => $note,
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
            'note' => $notation->getNote(),
            'succes' => $succes,
            'random_lignes' => $random_lignes,
            'error_content' => [],
            'error_indent' => [],
            'exercise' => $exercise,
            'form' => $form->createView(),
            'id1' => $id1,
            'id2' => $id2
        ]);
    }

    /**
     * @Route("/mesCours", name="my_lessons")
     */
    public function my_lessons()
    {
        return $this->render('/profile/myLessons.html.twig');
    }
}
