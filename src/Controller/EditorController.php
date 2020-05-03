<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Exercise;
use App\Entity\Line;
use App\Form\CoursType;
use App\Form\ExerciseType;
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
    public function form_exercise_lines($id2, ExerciseRepository $repo)
    {
        $exercise = $repo->find($id2);

        $lines = [];

        for ($i = 0; $i < $exercise->getnbLines(); $i++) {
            $lines[$i] = new Line();
        }

        return $this->render("editor/formLines.html.twig", [
            'lines' => $lines
        ]);
    }
}
