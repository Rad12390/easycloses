<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\SubjectProperty;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubjectPropertyController extends Controller
{
    /**
     * Create SubjectProperty Entity
     *
     * @param Request $request
     * @param int $contactId
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, $contactId)
    {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            // Get Contact Entity
            $contact = $em->find('LocalsBestUserBundle:AllContact', $contactId);
            // Get information from Request
            $data = $request->request->all();
            // Create new SubjectProperty Entity
            $subjectProperty = new SubjectProperty($data);
            $subjectProperty->setContact($contact);
            // Save
            $em->persist($subjectProperty);
            $em->flush();
            // Redirect user
            return $this->redirectToRoute('contact_view', ['id' => $contactId]);
        }
        // Render view with params
        return $this->render(
            '@LocalsBestUser/subject_property/create.html.twig',
            [
                'contactId' => $contactId,
            ]
        );
    }

    /**
     * Delete SubjectProperty Entity
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get SubjectProperty Entity by ID
        /** @var SubjectProperty $subjectProperty */
        $subjectProperty = $em->find('LocalsBestUserBundle:SubjectProperty', $id);
        // Get Contact attached to SubjectProperty
        $contact = $subjectProperty->getContact();

        if ($contact->getCreatedBy() == $this->getUser()) {
            // Remove SubjectProperty Entity
            $em->remove($subjectProperty);
            $em->flush();
            // Show flash message
            $this->addFlash('success', 'Subject Property deleted successfully.');
        } else {
            // Show flash message
            $this->addFlash('danger', 'you can not delete this Subject Property.');
        }
        // Redirect user
        return $this->redirectToRoute('contact_view', ['id' => $contact->getId()]);
    }
}
