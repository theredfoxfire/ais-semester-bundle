<?php

namespace Ais\SemesterBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ais\SemesterBundle\Exception\InvalidFormException;
use Ais\SemesterBundle\Form\SemesterType;
use Ais\SemesterBundle\Model\SemesterInterface;


class SemesterController extends FOSRestController
{
    /**
     * List all semesters.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing semesters.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many semesters to return.")
     *
     * @Annotations\View(
     *  templateVar="semesters"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSemestersAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('ais_semester.semester.handler')->all($limit, $offset);
    }

    /**
     * Get single Semester.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Semester for a given id",
     *   output = "Ais\SemesterBundle\Entity\Semester",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the semester is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="semester")
     *
     * @param int     $id      the semester id
     *
     * @return array
     *
     * @throws NotFoundHttpException when semester not exist
     */
    public function getSemesterAction($id)
    {
        $semester = $this->getOr404($id);

        return $semester;
    }

    /**
     * Presents the form to use to create a new semester.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @return FormTypeInterface
     */
    public function newSemesterAction()
    {
        return $this->createForm(new SemesterType());
    }
    
    /**
     * Presents the form to use to edit semester.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisSemesterBundle:Semester:editSemester.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the semester id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when semester not exist
     */
    public function editSemesterAction($id)
    {
		$semester = $this->getSemesterAction($id);
		
        return array('form' => $this->createForm(new SemesterType(), $semester), 'semester' => $semester);
    }

    /**
     * Create a Semester from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new semester from the submitted data.",
     *   input = "Ais\SemesterBundle\Form\SemesterType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisSemesterBundle:Semester:newSemester.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postSemesterAction(Request $request)
    {
        try {
            $newSemester = $this->container->get('ais_semester.semester.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newSemester->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_semester', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing semester from the submitted data or create a new semester at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ais\SemesterBundle\Form\SemesterType",
     *   statusCodes = {
     *     201 = "Returned when the Semester is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisSemesterBundle:Semester:editSemester.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the semester id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when semester not exist
     */
    public function putSemesterAction(Request $request, $id)
    {
        try {
            if (!($semester = $this->container->get('ais_semester.semester.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $semester = $this->container->get('ais_semester.semester.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $semester = $this->container->get('ais_semester.semester.handler')->put(
                    $semester,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $semester->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_semester', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing semester from the submitted data or create a new semester at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ais\SemesterBundle\Form\SemesterType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisSemesterBundle:Semester:editSemester.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the semester id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when semester not exist
     */
    public function patchSemesterAction(Request $request, $id)
    {
        try {
            $semester = $this->container->get('ais_semester.semester.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $semester->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_semester', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Semester or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return SemesterInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($semester = $this->container->get('ais_semester.semester.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $semester;
    }
    
    public function postUpdateSemesterAction(Request $request, $id)
    {
		try {
            $semester = $this->container->get('ais_semester.semester.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $semester->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_semester', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
	}
}
