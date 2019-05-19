<?php

namespace LocalsBest\MedAppUserBundle\Dbal\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ObjectTypeType extends AbstractEnumType
{

    const Document          = 'LocalsBestUserBundle:Document';
    const DocumentUser      = 'LocalsBestUserBundle:DocumentUser';
    const DocumentSingle    = 'LocalsBestUserBundle:DocumentSingle';
    const DocumentJob       = 'LocalsBestUserBundle:DocumentJob';
    const Event             = 'LocalsBestUserBundle:Event';
    const Job               = 'LocalsBestUserBundle:Job';
    const Listing           = 'LocalsBestUserBundle:Listing';
    const Transaction       = 'LocalsBestUserBundle:Transaction';
    const Contact           = 'LocalsBestUserBundle:AllContact';
    const User              = 'LocalsBestUserBundle:User';
    

    protected static $choices = array(
        self::Document      => 'LocalsBestUserBundle:Document',
        self::DocumentUser  => 'LocalsBestUserBundle:DocumentUser',
        self::DocumentSingle=> 'LocalsBestUserBundle:DocumentSingle',
        self::DocumentJob   => 'LocalsBestUserBundle:DocumentJob',
        self::Event         => 'LocalsBestUserBundle:Event',
        self::Job           => 'LocalsBestUserBundle:Job',
        self::Listing       => 'LocalsBestUserBundle:Listing',
        self::Transaction   => 'LocalsBestUserBundle:Transaction',
        self::Contact       => 'LocalsBestUserBundle:AllContact',
        self::User          => 'LocalsBestUserBundle:User'
    );
} 