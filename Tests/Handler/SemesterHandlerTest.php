<?php

namespace Ais\SemesterBundle\Tests\Handler;

use Ais\SemesterBundle\Handler\SemesterHandler;
use Ais\SemesterBundle\Model\SemesterInterface;
use Ais\SemesterBundle\Entity\Semester;

class SemesterHandlerTest extends \PHPUnit_Framework_TestCase
{
    const DOSEN_CLASS = 'Ais\SemesterBundle\Tests\Handler\DummySemester';

    /** @var SemesterHandler */
    protected $semesterHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::DOSEN_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::DOSEN_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::DOSEN_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $semester = $this->getSemester();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($semester));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);

        $this->semesterHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $semesters = $this->getSemesters(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($semesters));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);

        $all = $this->semesterHandler->all($limit, $offset);

        $this->assertEquals($semesters, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $semester = $this->getSemester();
        $semester->setTitle($title);
        $semester->setBody($body);

        $form = $this->getMock('Ais\SemesterBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($semester));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $semesterObject = $this->semesterHandler->post($parameters);

        $this->assertEquals($semesterObject, $semester);
    }

    /**
     * @expectedException Ais\SemesterBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $semester = $this->getSemester();
        $semester->setTitle($title);
        $semester->setBody($body);

        $form = $this->getMock('Ais\SemesterBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $this->semesterHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $semester = $this->getSemester();
        $semester->setTitle($title);
        $semester->setBody($body);

        $form = $this->getMock('Ais\SemesterBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($semester));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $semesterObject = $this->semesterHandler->put($semester, $parameters);

        $this->assertEquals($semesterObject, $semester);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $semester = $this->getSemester();
        $semester->setTitle($title);
        $semester->setBody($body);

        $form = $this->getMock('Ais\SemesterBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($semester));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->semesterHandler = $this->createSemesterHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $semesterObject = $this->semesterHandler->patch($semester, $parameters);

        $this->assertEquals($semesterObject, $semester);
    }


    protected function createSemesterHandler($objectManager, $semesterClass, $formFactory)
    {
        return new SemesterHandler($objectManager, $semesterClass, $formFactory);
    }

    protected function getSemester()
    {
        $semesterClass = static::DOSEN_CLASS;

        return new $semesterClass();
    }

    protected function getSemesters($maxSemesters = 5)
    {
        $semesters = array();
        for($i = 0; $i < $maxSemesters; $i++) {
            $semesters[] = $this->getSemester();
        }

        return $semesters;
    }
}

class DummySemester extends Semester
{
}
