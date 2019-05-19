<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Entity\BusinessRecipient;
use LocalsBest\UserBundle\Form\BusinessRecipientType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessRecipientController extends SuperController
{
    /**
     * Display block of Email Recipients for Shredding Business
     *
     * @return Response
     */
    public function indexAction()
    {
        // Get current Business
        $business = $this->getBusiness();

        $em = $this->getDoctrine()->getManager();
        // Get Business Recipients by Business
        $recipients = $em->getRepository('LocalsBestUserBundle:BusinessRecipient')->findBy(['business' => $business]);
        // Render view
        return $this->render('@LocalsBestUser/business_recipient/index.html.twig', ['recipients' => $recipients]);
    }


    /**
     * Create Business Recipient
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        // Get current Business
        $business = $this->getBusiness();

        $em = $this->getDoctrine()->getManager();
        // Create new Recipient
        $businessRecipient = new BusinessRecipient();
        // Attach Business to Recipient
        $businessRecipient->setBusiness($business);
        // Create Form object
        $form = $this->createForm(BusinessRecipientType::class, $businessRecipient, [
            'action' => $this->generateUrl('business_recipient_create')    ,
        ]);
        // Attach request data to form
        $form->handleRequest($request);
        // check form for submitting
        if ($form->isSubmitted() && $form->isValid()) {
            // save object
            $em->persist($businessRecipient);
            // save changes
            $em->flush();
            // redirect user to another page
            return $this->redirect($this->generateUrl('users_profile') . '#business_recipients');
        }
        // render view with form
        return $this->render(
            '@LocalsBestUser/business_recipient/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit Business Recipient
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Recipient
        $businessRecipient = $em->getRepository('LocalsBestUserBundle:BusinessRecipient')->find($id);
        // Create Form object
        $form = $this->createForm(BusinessRecipientType::class, $businessRecipient, [
            'action' => $this->generateUrl('business_recipient_edit', ['id' => $businessRecipient->getId()]),
        ]);
        // attach request data to form
        $form->handleRequest($request);
        // check form for submitting
        if ($form->isSubmitted() && $form->isValid()) {
            // save changes
            $em->flush();
            // redirect user to another page
            return $this->redirect($this->generateUrl('users_profile') . '#business_recipients');
        }
        // render view with form
        return $this->render(
            '@LocalsBestUser/business_recipient/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Delete Recipient
     *
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Recipient
        $businessRecipient = $em->getRepository('LocalsBestUserBundle:BusinessRecipient')->find($id);
        // delete Recipient
        $em->remove($businessRecipient);
        // update DB
        $em->flush();
        // Redirect to another Page
        return $this->redirect($this->generateUrl('users_profile') . '#business_recipients');
    }
}
