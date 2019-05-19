<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class IndustryType extends AbstractEnumType
{
    public static $choices = array(
        13,9,7,101,95,96,92,97,106,102,98,99,59,4,32,114,115,20,82,72,21,133
    );
} 