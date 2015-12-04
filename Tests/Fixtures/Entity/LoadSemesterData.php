<?php

namespace Ais\SemesterBundle\Tests\Fixtures\Entity;

use Ais\SemesterBundle\Entity\Semester;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadSemesterData implements FixtureInterface
{
    static public $semesters = array();

    public function load(ObjectManager $manager)
    {
        $semester = new Semester();
        $semester->setTitle('title');
        $semester->setBody('body');

        $manager->persist($semester);
        $manager->flush();

        self::$semesters[] = $semester;
    }
}
