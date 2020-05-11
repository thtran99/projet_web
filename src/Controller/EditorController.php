<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Exercise;
use App\Entity\Line;
use App\Entity\LinesTask;
use App\Form\CoursType;
use App\Form\ExerciseType;
use App\Form\LinesTaskType;
use App\Form\LineType;
use App\Repository\CoursRepository;
use App\Repository\ExerciseRepository;
use App\Repository\NotationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/editor", name = "editor_")
 */
class EditorController extends AbstractController
{
    /**
     * @Route("/cours/new", name="create_cours")
     * @Route("/cours/{id}/edit", name="edit_cours",
     *  requirements= {"id" = "\d+"})
     */
    public function form_cours(Cours $cours = null, Request $request, EntityManagerInterface $manager)
    {

        if (!$cours) {
            $cours = new Cours();
        } else {
            if (!$this->getUser()->my_lesson($cours)) {
                return $this->redirectToRoute('profile_show_cours', [
                    'id' => $cours->getId()
                ]);
            }
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            if (!$cours->getID()) {

                $cours->setCreatedAt(new \DateTime());
                $cours->setCreatedBy($user->getUsername());
            }

            $user->addLesson($cours);

            $manager->persist($cours);
            $manager->flush();

            return $this->redirectToRoute('profile_show_cours', [
                'id' => $cours->getId()
            ]);
        }

        return $this->render("editor/formCours.html.twig", [
            'formCours' => $form->createView(),
            'editMode' => $cours->getId() !== null
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/new", name="create_exercise")
     * @Route("/cours/{id1}/exercice/{id2}/edit", name="edit_exercise")
     */
    public function form_exercise($id1, $id2 = -1, Exercise $exercise = null, Request $request, EntityManagerInterface $manager, CoursRepository $repo_cours, ExerciseRepository $repo_exo)
    {
        $cours = $repo_cours->find($id1);

        $editMode = $id2 != -1;

        if ($editMode) {
            $exercise = $repo_exo->find($id2);
            if (!$this->getUser()->my_exercise($exercise)) {
                return $this->redirectToRoute('editor_show_exercises', [
                    'id1' => $id1,
                    'id2' => $id2
                ]);
            }
        } else {
            $exercise = new Exercise();
        }

        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cours->addExercise($exercise);

            $manager->persist($exercise);
            $manager->flush();

            return $this->redirectToRoute('editor_create_exercise_lines', [
                'id1' => $cours->getId(),
                'id2' => $exercise->getId(),
            ]);
        }

        return $this->render("editor/formExercise.html.twig", [
            'form' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}/newLines", name="create_exercise_lines")
     */
    public function form_exercise_lines($id1, $id2, Request $request, EntityManagerInterface $manager, ExerciseRepository $repo)
    {


        $exercise = $repo->find($id2);

        $task = new LinesTask();


        $nbLinesUser =  $exercise->getnbLines();
        $nbLinesData = $exercise->getLignes()->count();
        $editMode = $nbLinesData == 0;
        $diff = $nbLinesUser - $nbLinesData;


        if ($diff == $nbLinesUser) {
            for ($i = 0; $i < $nbLinesUser; $i++) {
                $line = new Line();
                $line->setRanking($i);
                $task->getLines()->add($line);
            }
        } elseif ($diff == 0) {
            for ($i = 0; $i < $nbLinesUser; $i++) {
                $line = $exercise->getLignes()->get($i);
                $task->getLines()->add($line);
            }
        } elseif ($diff < 0) {
            for ($i = 0; $i < $nbLinesData; $i++) {
                if ($i >= ($nbLinesData + $diff)) {
                    $line = $exercise->getLignes()->get($i);
                    $exercise->removeLigne($line);
                } else {
                    $line = $exercise->getLignes()->get($i);
                    $task->getLines()->add($line);
                }
            }
        } else {
            for ($i = 0; $i < $nbLinesUser; $i++) {
                if ($i < $nbLinesData) {
                    $line = $exercise->getLignes()->get($i);
                    $task->getLines()->add($line);
                } else {
                    $line = new Line();
                    $line->setRanking($i);
                    $task->getLines()->add($line);
                }
            }
        }

        $form = $this->createForm(LinesTaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            for ($i = 0; $i < $exercise->getnbLines(); $i++) {
                $line = $task->getLines()->get($i);
                $exercise->addLigne($line);
                $manager->persist($line);
            }
            $manager->flush();

            return $this->redirectToRoute('editor_show_exercises', [
                'id1' => $id1,
                'id2' => $id2
            ]);
        }

        return $this->render('editor/formLines.html.twig', [
            'form' => $form->createView(),
            'editMode' => $editMode,
            'id1' => $id1,
            'id2' => $id2
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}", name="show_exercises", 
     *  requirements= {"id1" = "\d+","id2" = "\d+"})
     */
    public function show_exo($id1, $id2, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);

        return $this->render('editor/showExercise.html.twig', [
            'exercise' => $exercise,
            'id1' => $id1,
            'id2' => $id2
        ]);
    }
}
