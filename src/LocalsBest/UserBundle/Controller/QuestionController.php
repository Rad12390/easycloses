<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Entity\TransactionQuestion;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    /**
     * Create Yes/No Question Entity
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Create Question Entity
        $question = new TransactionQuestion();

        if($request->getMethod() == 'POST') {
            // Set information to entity
            $question->setBusiness($this->getUser()->getBusinesses()->first());
            $question->setCreating($request->get('creating'));
            $question->setRepresent($request->get('represent'));
            $question->setPropertyType($request->get('propertyType'));
            $question->setStatus('Any');
            $question->setTransactionType($request->get('transactionType'));
            $question->setQuestion($request->get('question'));
            $question->setDocumentName($request->get('documentName'));
            $question->setSlug('doc-' . $this->getUser()->getBusinesses()->first()->getId() . '-' . time());
            // Save
            $em->persist($question);
            $em->flush();
            // Redirect user to prev page
            return $this->redirect($request->headers->get('referer') . '#business_doc_questions');
        }
        // Render view with params
        return $this->render('@LocalsBestUser/question/create.html.twig', ['question' => $question]);
    }

    /**
     * Edit Yes/No Question
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Question Entity by ID
        $question = $em->getRepository('LocalsBestUserBundle:TransactionQuestion')->find($id);
        if($request->getMethod() == 'POST') {
            // Set information to entity
            $question->setBusiness($this->getUser()->getBusinesses()->first());
            $question->setCreating($request->get('creating'));
            $question->setRepresent($request->get('represent'));
            $question->setPropertyType($request->get('propertyType'));
            $question->setStatus('Any');
            $question->setTransactionType($request->get('transactionType'));
            $question->setQuestion($request->get('question'));
            $question->setDocumentName($request->get('documentName'));
            $question->setSlug('doc-' . $this->getUser()->getBusinesses()->first()->getId() . '-' . time());
            // Update DB
            $em->flush();
            // Redirect user to prev page
            return $this->redirect($request->headers->get('referer') . '#business_doc_questions');
        }
        // Render view with params
        return $this->render('@LocalsBestUser/question/edit.html.twig', ['question' => $question]);
    }

    /**
     * Remove Yes/No Question
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Question Entity by ID
        $question = $em->getRepository('LocalsBestUserBundle:TransactionQuestion')->find($id);
        // Get Answer for this Question
        $answers = $em->getRepository('LocalsBestUserBundle:TransactionAnswer')->findBy(['question' => $question]);
        // Remove Answers
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        // Remove Question
        $em->remove($question);
        // Save DB Changes
        $em->flush();

        // Redirect user
        return $this->redirect($request->headers->get('referer') . '#business_doc_questions');
    }
}
