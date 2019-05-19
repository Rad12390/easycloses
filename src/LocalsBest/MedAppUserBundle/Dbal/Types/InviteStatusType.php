<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class InviteStatusType extends AbstractEnumType
{

    const INVITE    = 'I';
    const ACCEPTED  = 'A';
    const EXPIRED   = 'E';

    protected static $choices = array(
        self::INVITE      => 'Invite',
        self::ACCEPTED    => 'Accepted',
        self::EXPIRED    => 'Expired',
    );
} 