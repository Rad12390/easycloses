<?php

namespace LocalsBest\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManager;

class TooltipText
{
    protected $em;

    private $container;

    public function __construct(Container $container, EntityManager $manager) {
        $this->container = $container;
        $this->em = $manager;
    }

    public function getText($name = null, $view=null, $float = null)
    {
        if ($name === null) {
            return '';
        }

        $result = $this->em->getRepository('LocalsBestUserBundle:Tooltip')->findOneBy([
            'name' => $name,
        ]);

        if ($result === null) {
            return '';
        }
        if($view=='header'){
            return '<i class=" fa fa-info-circle" style="'.($float !== null ? 'float: '.$float.';"' : '') .'" data-toggle="tooltip" data-placement="right" title="'
            .$result->getBody()
            .'"></i>'
            ;
        }
        return '<i class=" fa fa-info-circle fa-lg" style="color: black; '.($float !== null ? 'float: '.$float.';"' : '') .'" data-toggle="tooltip" data-placement="right" title="'
            .$result->getBody()
            .'"></i>'
        ;
    }
}