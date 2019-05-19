<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MemberController extends Controller
{
    /**
     * Display members summary page
     *
     * @return array
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $states = $em->getRepository('LocalsBestUserBundle:Business')->getStatesArray();

        return [
            'states' => $states,
        ];
    }

    /**
     * Search for members by $query
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        // Get Doctrine Entity Manages
        $em = $this->getDoctrine()->getManager();
        // get data from http request
        $data = $request->query->all();
        // get Users by information from request
        $members = $em->getRepository('LocalsBestUserBundle:User')->getMembersByInfo($data);
        // get class that works with avatars and logos
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        // for each User that does not have avatar set default image
        foreach ($members as $key => $member) {
            if($member['fileName'] === null || $member['fileName'] == '') {
                $members[$key]['logo'] = '/images/empty-avatar.png';
            } else {
                $members[$key]['logo'] = $helper->asset($member, 'file', 'LocalsBest\\UserBundle\\Entity\\User');
            }
        }

        // send Users back with JSON format
        return new JsonResponse($members);
    }
    /**
     * Display details about User
     *
     * @param int $id User ID
     *
     * @throws NotFoundHttpException
     *
     * @return array
     * @Template()
     */
    public function detailsAction($id)
    {
        // Get Doctrine Entity Manages
        $em = $this->getDoctrine()->getManager();
        // Get User by ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($id);
        // if User was not found - throw exception
        if($user === null) {
            throw $this->createNotFoundException();
        }

        // get Events
        $events = $em->getRepository('LocalsBest\UserBundle\Entity\Event')
            ->findMyEvents($this->getUser(), $user, 'user');
        // get Documents
        $documents = $em->getRepository('LocalsBest\UserBundle\Entity\DocumentUser')
            ->findMyDocuments($this->getUser(), $user, 'user');

        // get Notes

        if ($this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $notes    = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
                ->findNotesForAdmin($this->getBusiness()->getOwner(), 'LocalsBestUserBundle:User', $user);
        } else {
            $notes = $em->getRepository('LocalsBest\CommonBundle\Entity\Note')
                ->findMyNotes($this->getUser(), 'LocalsBestUserBundle:User', $user);
        }

        // send data to template
        return [
            'objectType' => 'LocalsBestUserBundle:User',
            'events' => $events,
            'notes' => $notes,
            'documents' => $documents,
            'user' => $user
        ];
    }
}
