<?php

namespace Ais\SemesterBundle\Model;

Interface SemesterInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set semester
     *
     * @param string $semester
     *
     * @return Semester
     */
    public function setSemester($semester);

    /**
     * Get semester
     *
     * @return string
     */
    public function getSemester();

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Semester
     */
    public function setIsActive($isActive);

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive();

    /**
     * Set isDelete
     *
     * @param boolean $isDelete
     *
     * @return Semester
     */
    public function setIsDelete($isDelete);

    /**
     * Get isDelete
     *
     * @return boolean
     */
    public function getIsDelete();
}
