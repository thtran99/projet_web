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
    }
}
