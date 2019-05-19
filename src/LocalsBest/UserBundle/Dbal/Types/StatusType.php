<?php

namespace LocalsBest\UserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class StatusType extends AbstractEnumType
{

    const ACTIVE    = 1;
    const INACTIVE  = 0;
    

    protected static $choices = array(
        self::ACTIVE      => 'Active',
        self::INACTIVE    => 'Inactive',
        
    );
} 