<?php

namespace LocalsBest\UserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class DocumentTypeType extends AbstractEnumType
{

    const ACTIVE    = 'A';
    const INACTIVE  = 'I';
    const REQUIRED  = 'R';
    const OPTIONAL  = 'O';
    

    protected static $choices = array(
        self::ACTIVE      => 'Active',
        self::INACTIVE    => 'Inactive',
        self::REQUIRED    => 'Required',
        self::OPTIONAL    => 'Optional',
        
    );
} 