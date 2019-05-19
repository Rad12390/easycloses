<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class JobIndustryType extends AbstractEnumType
{

    const Document      = 'LocalsBestUserBundle:Document';
    const Event         = 'LocalsBestUserBundle:Event';
    

    protected static $choices = array(
        self::Document      => 'LocalsBestUserBundle:Document',
        self::Event         => 'LocalsBestUserBundle:Event',
     
    );
} 