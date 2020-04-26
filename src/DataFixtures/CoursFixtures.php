<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Cours;

class CoursFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        for($i=1; $i<=10; $i++) {
            $cours = new Cours();
            $cours->setTitle("Titre du cours n°$i")
                  ->setContent("Contenu de l'article n°$i")
                  ->setCreatedAt(new \DateTime());
            $manager->persist($cours);
        }

        $manager->flush();
    }
}
