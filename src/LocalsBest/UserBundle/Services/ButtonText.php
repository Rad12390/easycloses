<?php

namespace LocalsBest\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

class ButtonText
{
    protected $em;

    private $container;

    public function __construct(Container $container, EntityManager $manager) {
        $this->container = $container;
        $this->em = $manager;
    }

    public function getText($slug = null)
    {
        if ($slug === null) {
            return '';
        }

        $result = $this->em->getRepository('LocalsBestUserBundle:Buttons')->findOneBy([
            'id' => $slug,
        ]);
        
        if ($result === null) {
            return '';
        }
        return '<a class="btn btn-info dynamic_button" id="'.$result->getId().'" href="'.$result->getLink().'">'.$result->getName().'</a>';
    }
    
    public function getLink($slug = null){
        if ($slug === null) {
            return '';
        }
        $result = $this->em->getRepository('LocalsBestUserBundle:Buttons')->findOneBy([
            'id' => $slug,
        ]);
        
        if ($result === null) {
            return '';
        }
        return $result->getLink();
    }
}