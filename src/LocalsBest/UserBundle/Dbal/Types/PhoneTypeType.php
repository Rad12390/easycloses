<?php

namespace LocalsBest\UserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PhoneTypeType extends AbstractEnumType
{

    const HOME      = 'H';
    const MOBILE    = 'M';
    const OFFICE    = 'O';

    protected static $choices = array(
        self::OFFICE    => 'Office',
        self::MOBILE    => 'Mobile',
        self::HOME      => 'Home',
    );
} 