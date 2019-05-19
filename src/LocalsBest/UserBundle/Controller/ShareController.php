<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\Response;

class ShareController extends Controller
{
    /**
     * Remove share entity
     *
     * @param $id
     *
     * @return Response
     */
    public function removeShareAction($id)
    {
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );
        // Get Share Entity
        $share = $this->findOr404('LocalsBestUserBundle:Share', array('id' => $id));
        // Get Object Entity  attached to Share
        $document = $this->getRepository($share->getObjectType())->find($share->getObjectId());
        
        if (!$document) {
            throw $this->createNotFoundException('No such document found');
        }
        // Get business staff
        $users = $this->getStaffs();
        // Check user permissions
        if ($share->getCreatedBy() === $this->getUser() || in_array($share->getCreatedBy(), $users)) {
            // detach share from object
            $document->removeShare($share);
            // Remove Share
            $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Share')->remove($share);

            $response = array(
                'code'      => 1,
                'message'   => 'Share removed successfully'
            );
        }
        // Return JSON response
        return new Response(json_encode($response));
    }

    /**
     * Remove share entity
     *
     * @param int $id
     *
     * @return Response
     */
    public function removeEventShareAction($id)
    {
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );
        // Get Share Entity
        $share = $this->findOr404('LocalsBestUserBundle:Share', array('id' => $id));
        // Get Object Entity  attached to Share
        $event = $this->getRepository($share->getObjectType())->find($share->getObjectId());
        
        if (!$event) {
            throw $this->createNotFoundException('No such event found');
        }
        // Get business staff
        $users = $this->getStaffs();
        // Check user permission
        if ($share->getCreatedBy() === $this->getUser() || in_array($share->getCreatedBy(), $users)) {
            // detach share from object
            $event->removeShare($share);
            // Remove Share
            $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Share')->remove($share);
            
            $response = array(
                'code'      => 1,
                'message'   => 'Share removed successfully'
            );
        }
        // Return JSON response
        return new Response(json_encode($response));
    }

    /**
     * @param string $slug
     * @param int $id
     * @return Response
     */
    public function removeEventAction($slug, $id)
    {
        $response = array(
            'code'      => 0,
            'message'   => 'Some error occured'
        );
        // Get Event Entity by slug
        $event = $this->findOr404('LocalsBestUserBundle:Event', array('slug' => $slug));
        // Get User Entity by ID
        $user = $this->findOr404('LocalsBestUserBundle:User', array('id' => $id));
        
        if (!$event) {
            throw $this->createNotFoundException('No such Event found');
        }
        
        if (!$user) {
            throw $this->createNotFoundException('No such user found');
        }
        // Get business staff
        $users = $this->getStaffs();
        // Check user permission
        if ($event->getCreatedBy() === $this->getUser() || in_array($event->getCreatedBy(), $users)) {
            // Detach event from user
            $event->removeAssignedTo($user);
            // save Event changes
            $this->getDoctrine()->getManager()->getRepository('LocalsBest\UserBundle\Entity\Event')->save($event);
            
            $response = array(
                'code'      => 1,
                'message'   => 'Assign removed successfully'
            );
        }
        // Return JSON response
        return new Response(json_encode($response));
    }
}