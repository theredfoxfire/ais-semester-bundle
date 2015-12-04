<?php

namespace Ais\SemesterBundle\Handler;

use Ais\SemesterBundle\Model\SemesterInterface;

interface SemesterHandlerInterface
{
    /**
     * Get a Semester given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return SemesterInterface
     */
    public function get($id);

    /**
     * Get a list of Semesters.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Semester, creates a new Semester.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return SemesterInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Semester.
     *
     * @api
     *
     * @param SemesterInterface   $semester
     * @param array           $parameters
     *
     * @return SemesterInterface
     */
    public function put(SemesterInterface $semester, array $parameters);

    /**
     * Partially update a Semester.
     *
     * @api
     *
     * @param SemesterInterface   $semester
     * @param array           $parameters
     *
     * @return SemesterInterface
     */
    public function patch(SemesterInterface $semester, array $parameters);
}
