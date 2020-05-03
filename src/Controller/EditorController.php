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
     */
    public function form_exercise($id1,  Exercise $exercise = null, Request $request, EntityManagerInterface $manager, CoursRepository $repo_cours)
    {

        $cours = $repo_cours->find($id1);

        if (!$exercise) {
            $exercise = new Exercise();
        }

        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cours->addExercise($exercise);
            $exercise->setCours($cours);

            $manager->persist($exercise);
            $manager->flush();

            return $this->redirectToRoute('editor_create_exercise_lines', [
                'id1' => $cours->getId(),
                'id2' => $exercise->getId()
            ]);
        }

        return $this->render("editor/formExercise.html.twig", [
            'form' => $form->createView(),
            'editMode' => $exercise->getId() !== null
        ]);
    }

    /**
     * @Route("/cours/{id1}/exercice/{id2}/newLines", name="create_exercise_lines")
     */
    public function form_exercise_lines($id1, $id2,  Request $request, EntityManagerInterface $manager, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);

        $task = new LinesTask();

        for ($i = 0; $i < $exercise->getnbLines(); $i++) {
            $line = new Line();
            $line->setRanking($i);
            $task->getLines()->add($line);
        }

        $form = $this->createForm(LinesTaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            for ($i = 0; $i < $exercise->getnbLines(); $i++) {
                $line = $task->getLines()->get($i);
                $exercise->addLigne($line);
                $line->setExerciseId($exercise);
                $manager->persist($line);
            }
            $manager->flush();

            return $this->redirectToRoute('editor_show_exercises', [
                'id1' => $id1,
                'id2' => $id2
            ]);
        }

        return $this->render('editor/formLines.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cours{id1}/exercice/{id2}", name="show_exercises")
     */
    public function show_exo($id2, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);

        return $this->render('editor/showExercise.html.twig', [
            "exercise" => $exercise
        ]);
    }
}
