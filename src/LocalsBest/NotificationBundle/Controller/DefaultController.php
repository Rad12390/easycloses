<?php

namespace LocalsBest\NotificationBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * Display Notification List
     *
     * @return array
     * @Template
     */
    public function indexAction()
    {
        $notifications = [];
        $count = 0;

        if ($this->getUser()) {
            $em = $this->getDoctrine()->getManager();

            $notifications = $em->getRepository('LocalsBestNotificationBundle:Notification')->getForMainPage(
                $this->getUser()
            );

            $count = $em->getRepository('LocalsBestNotificationBundle:Notification')->getCountForMainPage(
                $this->getUser()
            );
        }

        return [
            'notificationCount' => $count,
            'notifications' => $notifications,
        ];
    }

    /**
     * Display Notification List
     *
     * @return array
     * @Template
     */
    public function listAction()
    {
        $notifications = array();

        if ($this->getUser()) {
            $notifications = $this->getUser()->getNotifications(true);
        }

        return [
            'notifications' => $notifications
        ];
    }

    /**
     * Open page from Notification
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function clickAction($id)
    {
        $userNotification = $this->findOr404(
            'LocalsBestNotificationBundle:UserNotification',
            array('notification' => $id),
            'Invalid or expired notification'
        );

//        var_dump($userNotification->getNotification()->getTargetParams()); die;

        $userNotification
            ->setReadOn(time())
            ->setRead(true)
        ;

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        if($userNotification->getNotification()->getTargetPath() == 'user_view') {
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy($userNotification->getNotification()->getTargetParams());
            $currBusinessId = $this->getBusiness()->getId();
            $businesses = $user->getBusinesses();
            $sameBusiness = false;
            foreach ($businesses as $business) {
                if($business->getId() == $currBusinessId) {
                    $sameBusiness = true;
                    break;
                }
            }
            if($sameBusiness == false) {
                return $this->redirectToRoute('members_details', ['id' => $user->getId()]);
            }
        }
        
        return $this->redirectToRoute(
            $userNotification->getNotification()->getTargetPath(),
            $userNotification->getNotification()->getTargetParams()
        );
    }
}
