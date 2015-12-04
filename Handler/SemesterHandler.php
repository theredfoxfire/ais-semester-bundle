<?php

namespace Ais\SemesterBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Ais\SemesterBundle\Model\SemesterInterface;
use Ais\SemesterBundle\Form\SemesterType;
use Ais\SemesterBundle\Exception\InvalidFormException;

class SemesterHandler implements SemesterHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Semester.
     *
     * @param mixed $id
     *
     * @return SemesterInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Semesters.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Semester.
     *
     * @param array $parameters
     *
     * @return SemesterInterface
     */
    public function post(array $parameters)
    {
        $semester = $this->createSemester();

        return $this->processForm($semester, $parameters, 'POST');
    }

    /**
     * Edit a Semester.
     *
     * @param SemesterInterface $semester
     * @param array         $parameters
     *
     * @return SemesterInterface
     */
    public function put(SemesterInterface $semester, array $parameters)
    {
        return $this->processForm($semester, $parameters, 'PUT');
    }

    /**
     * Partially update a Semester.
     *
     * @param SemesterInterface $semester
     * @param array         $parameters
     *
     * @return SemesterInterface
     */
    public function patch(SemesterInterface $semester, array $parameters)
    {
        return $this->processForm($semester, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param SemesterInterface $semester
     * @param array         $parameters
     * @param String        $method
     *
     * @return SemesterInterface
     *
     * @throws \Ais\SemesterBundle\Exception\InvalidFormException
     */
    private function processForm(SemesterInterface $semester, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new SemesterType(), $semester, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $semester = $form->getData();
            $this->om->persist($semester);
            $this->om->flush($semester);

            return $semester;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createSemester()
    {
        return new $this->entityClass();
    }

}
