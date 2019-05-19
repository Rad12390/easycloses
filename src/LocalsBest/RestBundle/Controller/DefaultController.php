<?php

namespace LocalsBest\RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('@LocalsBestRest/default/index.html.twig', array('name' => $name));
    }
}
