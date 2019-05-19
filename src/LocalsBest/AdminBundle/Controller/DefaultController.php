<?php

namespace LocalsBest\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('@LocalsBestAdmin/default/index.html.twig', array('name' => $name));
    }

    /**
     * @Route("/contact-us", name="admin_contact_us")
     */
    public function contactUsAction()
    {
        $contactUsRequests = $this->getDoctrine()->getRepository('LocalsBestShopBundle:SkuContactUs')
            ->findBy(array(),array('id'=>'desc'));

        return $this->render('@LocalsBestAdmin/default/contact-us.html.twig', [
            'entities' => $contactUsRequests,
        ]);
    }
}
