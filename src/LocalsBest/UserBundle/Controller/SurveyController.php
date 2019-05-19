<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\Survey;
use LocalsBest\UserBundle\Entity\SurveyAnswer;
use LocalsBest\UserBundle\Entity\SurveyResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends Controller
{
    /**
     * Show users Surveys List
     *
     * @return array
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get Surveys List
        $surveys = $em->getRepository('LocalsBestUserBundle:Survey')->findBy(['createdBy' => $this->getUser()]);
        // Return params to view
        return ['surveys' => $surveys];
    }

    /**
     * Create Survey
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function createAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $em = $this->getDoctrine()->getManager();
            // Create Survey Entity
            $survey = new Survey();
            $survey->setQuestion($request->get('question'));
            $survey->setType($request->get('type'));
            $survey->setCreatedBy($this->getUser());
            // Save changes
            $em->persist($survey);
            $em->flush();

            $answers = $request->get('answers');
            foreach ($answers as $item) {
                // Create Answer Entity
                $answer = new SurveyAnswer();
                $answer->setText($item);
                $answer->setSurvey($survey);
                $em->persist($answer);
            }
            // Update DB
            $em->flush();
            // Redirect user
            return $this->redirectToRoute('survey_index');
        }
        // Return empty array to View
        return [];
    }

    /**
     * Detail Page of Survey
     *
     * @param int $id
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Survey Entity
        $survey = $em->getRepository('LocalsBestUserBundle:Survey')->find($id);

        $surveyResults = $em->getRepository('LocalsBestUserBundle:SurveyResult')
            ->findBy(['users' => $this->getUser(), 'survey' => $survey]);

        // if user answer for Survey redirect him to results
        if(count($surveyResults) > 0) {
            return $this->redirectToRoute('survey_result', ['id' => $id]);
        }
        // Return array of params to view
        return [
            'survey' => $survey
        ];
    }

    /**
     * Display Survey results
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function resultAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Survey Entity
        $survey = $em->getRepository('LocalsBestUserBundle:Survey')->find($id);

        if ($request->getMethod() === 'POST') {
            // get results from request
            $results = $request->get('results');
            foreach ($results as $item) {
                // Get Survey Answers
                $answer = $em->getRepository('LocalsBestUserBundle:SurveyAnswer')->find($item);
                // Create SurveyResult Entity
                $surveyResult = new SurveyResult();
                $surveyResult->setSurvey($survey);
                $surveyResult->setAnswer($answer);
                $surveyResult->setUser($this->getUser());
                $em->persist($surveyResult);
            }
            $em->flush();
            // Redirect user
            return $this->redirectToRoute('survey_view', ['id' => $id]);
        }
        // Return array of params to view
        return [
            'survey' => $survey
        ];
    }
}
