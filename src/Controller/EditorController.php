<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function form_cours(Cours $cours = null,Request $request, EntityManagerInterface $manager) 
    {
        if (!$cours) {
            $cours = new Cours();
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$cours->getID()) {

                $user = $this->getUser();

                $cours->setCreatedAt(new \DateTime());
                $cours->setCreatedBy($user->getUsername());
            }

            $user = $this->getUser();

            $user->addLesson($cours);

            $manager->persist($cours);
            $manager->flush();

            return $this->redirectToRoute('profile_show_cours', [
                'id' => $cours->getId()]);
            }

        return $this->render("editor/formCours.html.twig", [
            'formCours' => $form->createView(),
            'editMode' => $cours->getId() !== null
        ]);
    }

}
