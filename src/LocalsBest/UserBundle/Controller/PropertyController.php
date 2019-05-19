<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Entity\Property;
use LocalsBest\UserBundle\Form\PropertyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PropertyController extends SuperController
{
    /**
     * Create or edit Property Entity
     *
     * @param Request $request
     * @param int $id
     * @param string $username
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function editAction(Request $request, $id = null, $username)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);

        if ($id) {
            // Get Property Entity by ID
            $property = $this->findOr404('LocalsBestUserBundle:Property', ['id' => $id], 'No such property found');
            
            if($property->getUser() !== $this->getUser() && $this->getUser()->getRole()->getLevel() > 5){
                throw $this->createAccessDeniedException('Access Denied');
            }
        } else {
            // Create new Property Entity
            $property = new Property();
        }
        // Create Property Form object
        $form = $this->createForm(PropertyType::class, $property);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $property = $form->getData();
                
                if (!$property->getUser()) {
                    $property->setUser($user);
                }
                
                $em->getRepository('LocalsBest\UserBundle\Entity\Property')->save($property);

                if($user == $this->getUser()) {
                    return $this->redirect($this->generateUrl('users_profile') . '#personal_property');
                } else {
                    return $this->redirect(
                        $this->generateUrl('users_profile', ['username' => $username]) . '#personal_property'
                    );
                }
            }
        }
        // Render view
        return [
            'form' => $form->createView(),
            'username' => $username,
            'property' => $property,
        ];
    }

    public function deleteAction($id, $username)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);

        $property = $this->findOr404('LocalsBestUserBundle:Property', array('id' => $id), 'No such property found');

        if($property->getUser() !== $this->getUser() && $this->getUser()->getRole()->getLevel() > 5){
            throw $this->createAccessDeniedException('Access Denied');
        }

        $em->remove($property);

        $em->flush();

        if($user == $this->getUser()) {
            return $this->redirect($this->generateUrl('users_profile') . '#personal_property');
        } else {
            return $this->redirect(
                $this->generateUrl('users_profile', ['username' => $username]) . '#personal_property'
            );
        }
    }
}
