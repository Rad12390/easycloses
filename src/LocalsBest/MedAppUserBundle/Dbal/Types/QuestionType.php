<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class QuestionType extends AbstractEnumType
{
    const job = 1;
    const other= 2;
    const note = 3;
    const image= 4;
    const event = 5;

    public static $choices = array(
        self::job => 'Do you Ask/Require a Job Address?',
        self::other => 'Do you Ask/Require Other Fields?',
        self::note => 'Do you Ask/Require a Note?',
        self::image => 'Do you Ask/Require an Image?',
        self::event => 'Do you Ask/Require an Event Date?'

    );
}
