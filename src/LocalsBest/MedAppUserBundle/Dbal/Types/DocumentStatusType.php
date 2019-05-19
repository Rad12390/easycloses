<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class DocumentStatusType extends AbstractEnumType
{

    const ACTIVE    = 'A';
    const INACTIVE  = 'I';
    const PUBLISHED = 'P';

    protected static $choices = array(
        self::ACTIVE      => 'Active',
        self::INACTIVE    => 'Inactive',
        self::PUBLISHED    => 'Published',
    );
} 