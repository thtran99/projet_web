<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Cours;
use App\Entity\Exercise;

class CoursFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Créer de 5 cours
        for($i=1; $i<=5; $i++) {
            $cours = new Cours();
            $cours->setTitle($faker->sentence())
                  ->setContent($faker->paragraph())
                  ->setCreatedAt($faker->dateTimeBetween('-3 months'));

            $manager->persist($cours);

            for($j=1; $j<=mt_rand(4,6); $j++) {
                $exercice = new Exercise();
                $exercice->setTitle($faker->sentence())
                        ->setDescription($faker->paragraph())
                        ->setContent($faker->sentence())
                        ->setCours($cours);

                $manager->persist($exercice);
            }
            var_dump($cours);
        }

        $manager->flush();
    }
}