<?php

namespace App\DataFixtures;

use App\Controller\SecurityController;
use App\Entity\Cours;
use App\Entity\Exercise;
use App\Entity\Line;
use App\Entity\Notation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");

        $enseignant = new User();
        $enseignant
            ->setEmail('prof@prof.prof')
            ->setUsername('prof2020')
            ->setRoles(['ROLE_EDITOR']);
        $enseignant->setPassword($this->encoder->encodePassword($enseignant, 'pass1234'));
        $manager->persist($enseignant);

        $student = new User();
        $student
            ->setEmail('etu@etu.etu')
            ->setUsername('etu2020')
            ->setRoles(['ROLE_USER']);
        $student->setPassword($this->encoder->encodePassword($student, 'pass1234'));
        $manager->persist($student);


        for ($i = 0; $i < 5; $i++) {
            $lesson = new Cours();
            $lesson
                ->setTitle($faker->sentence(6))
                ->setContent($faker->paragraph(4))
                ->setCreatedBy($enseignant->getUsername())
                ->setCreatedAt($faker->dateTimeBetween('-3 months'));

            $lesson->addUser($enseignant);
            $lesson->addUser($student);
            $manager->persist($lesson);

            for ($j = 0; $j <= mt_rand(2, 4); $j++) {
                $exercise = new Exercise();
                $exercise
                    ->setTitle($faker->sentence(1))
                    ->setDescription($faker->paragraph(2))
                    ->setAttempts(mt_rand(1, 10));

                $manager->persist($exercise);
                $lesson->addExercise($exercise);

                $notation = new Notation();
                $notation
                    ->setNote(mt_rand(0, 100));
                $student->addNotation($notation);
                $exercise->addNotation($notation);
                $manager->persist($notation);

                for ($k = 0; $k < $rand = mt_rand(4, 8); $k++) {
                    $line = new Line();
                    $line
                        ->setRanking($k)
                        ->setContent($faker->sentence(mt_rand(4, 8)))
                        ->setIndentation(mt_rand(0, 2));

                    $manager->persist($line);
                    $exercise->addLigne($line);
                }
                $exercise->setnbLines($rand);
            }
        }

        $manager->flush();
    }
}
