<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\BusinessUserSet;
use LocalsBest\UserBundle\Form\BusinessUserSetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessUserSetController extends Controller
{
    /**
     * Will take BusinessUserSet ID or Business ID from session and return Business Object back
     *
     * @param $em
     * @return Business|null
     */
    private function getBusiness($em)
    {
        // Get businessUserSet ID from session by key
        $businessUserSetId = $this->get('session')->get('current_business_user_set');

        if ($businessUserSetId) {
            // Get BusinessUserSet Object from DB
            /** @var BusinessUserSet $businessUserSet */
            $businessUserSet = $em->getRepository('LocalsBestUserBundle:BusinessUserSet')->find($businessUserSetId);

            // Get Business Object attached to BusinessUserSet Object
            $business = $businessUserSet->getBusiness();
        } else {
            // Get business ID from session by key
            $currentBusinessId = $this->get('session')->get('current_business');

            if ($currentBusinessId) {
                // Get Business Object from DB
                $business = $em->getRepository('LocalsBestUserBundle:Business')->find($currentBusinessId);
            } else {
                // Set Business NULL
                $business = null;
            }
        }
        // Return business object back
        return $business;
    }

    /**
     * Show edit form for BusinessUserSet Object
     *
     * @param Request $request
     * @param integer $userId
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $userId)
    {
        // Get Doctrine Manager Object
        $em = $this->getDoctrine()->getManager();
        // Get User object by ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($userId);
        // Get Business object
        $business = $this->getBusiness($em);
        // Get BusinessUserSet using user and business Objects
        $businessUserSet = $em->getRepository('LocalsBestUserBundle:BusinessUserSet')->findOneBy([
            'user' => $user,
            'business' => $business,
        ]);
        // if user for this business does not have set entity
        if ($businessUserSet === null) {
            // create set entity for this user for this business
            $businessUserSet = new BusinessUserSet();
            // attache user to new set entity
            $businessUserSet->setUser($user);
            // attache business to new set entity
            $businessUserSet->setBusiness($business);
        }
        // Using businessUserSet create form for editing
        $form = $this->createForm(BusinessUserSetType::class, $businessUserSet, [
            'action' => $this->generateUrl('set_edit', ['userId' => $user->getId()])    ,
        ]);
        // attach request data to form
        $form->handleRequest($request);
        // check form for submitting
        if ($form->isSubmitted() && $form->isValid()) {
            if($businessUserSet->getId() === null) {
                $em->persist($businessUserSet);
            }
            // save changes
            $em->flush();
            // redirect user to another page
            $referrer = $request->headers->get('referer');

            return $this->redirect($referrer);
        }
        // render view with form
        return $this->render(
            '@LocalsBestUser/business_user_set/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
