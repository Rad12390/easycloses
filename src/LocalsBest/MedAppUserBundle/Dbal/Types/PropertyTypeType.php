<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PropertyTypeType extends AbstractEnumType
{

    const SINGLE        = 'Single_Family_Home';
    const CONDO         = 'Condo/Co-op';
    const MULTI         = 'Multi-Family';
    const VACANT        = 'Vacant_Land';
    const COMMERCIAL    = 'Commercial';
    const MANUFACTURED  = 'Manufactured_Home';
    const APARTMENT     = 'Apartment';
//    const FARMS         = 'Farm & Ranch';
    
    
    protected static $choices = array(
        self::SINGLE        => 'Single Family Home',
        self::CONDO         => 'Condo/Co-op',
        self::MULTI         => 'Multi-Family',
        self::VACANT        => 'Vacant Land',
        self::COMMERCIAL    => 'Commercial',
        self::MANUFACTURED  => 'Manufactured Home',
        self::APARTMENT     => 'Apartment',
//        self::FARMS         => 'Farm & Ranch',
        
    );
} 