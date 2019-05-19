<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventTypeType extends AbstractEnumType
{

    const TASK              = 'Task';
    const CALL              = 'Call';
    const APPOINTMENT       = 'Appointment';
    const EMAIL             = 'Email';
    const CUSTOM            = 'Custom';
    

    protected static $choices = array(
        self::TASK              => 'Task',
        self::CALL              => 'Call',
        self::APPOINTMENT       => 'Appointment',
        self::EMAIL             => 'Email',
        self::CUSTOM            => 'Custom'
     
    );
} 