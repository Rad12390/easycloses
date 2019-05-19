<?php

namespace LocalsBest\UserBundle\Services;

use LocalsBest\ShopBundle\Entity\Image;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\UserBundle\Entity\Advertisement;
use LocalsBest\UserBundle\Entity\BlankDocument;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\Product;
use LocalsBest\UserBundle\Entity\User;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Class Namer
 *
 * @package LocalsBest\UserBundle\Services
 */
class Namer implements NamerInterface
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Create name of file for Vich Uploader Bundle
     *
     * @param object $obj
     * @param PropertyMapping $mapping
     *
     * @return mixed|string
     */
    public function name($obj, PropertyMapping $mapping): string
    {
        //to get the business id
        if(!empty($this->container->get('security.token_storage')->getToken()->getUser()->getOwner()))
            $business_id = $this->container->get('security.token_storage')->getToken()->getUser()->getOwner()->getId();
        else
            $business_id='';
        // Replace special symbols from filename
        $file = str_replace(
            ['/', ' ', ',', '%', '*', '(', ')', '+', '!', '@', '?', '&', '<', '>', '~'],
            '-',
            $obj->getFile()->getClientOriginalName()
        );

        $path = null;

        // Check Object that file Attached to
        if ($obj instanceof User) {
            // Get file path for User Avatar
            $path = 'logo/users/' . $obj->getId();
        } elseif ($obj instanceof Business) {
            // Get file path for Business Logo
            $path = 'logo/businesses/' . $obj->getId();
        } elseif ($obj instanceof Image) {
            // Get file path for EC Shop Image
            $path = 'ec_shop/'.$business_id.'/item/' . $obj->getItem()->getId();
	} elseif ($obj instanceof Package) {
            // Get file path for EC Shop Image
            $path = 'ec_shop/'.$business_id.'/package/' . $obj->getId();
        } elseif ($obj instanceof Product) {
            // Get file path for Product image
            $path = 'imagesA/products/' . $obj->getId();
        } elseif ($obj instanceof Advertisement) {
            $path = 'images/advertisement/' . $obj->getId();
        } elseif ($obj instanceof BlankDocument) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $path = 'blank-documents/' . $user->getBusinesses()->first()->getId();
        } else {
            if (!is_null($obj->getUser()) && !is_null($obj->getUser()->getId())) {
                // Get file path for User file
                $path = 'users/' . $obj->getUser()->getId();
            } elseif(!is_null($obj->getJob()) && !is_null($obj->getJob()->getId())) {
                // Get file path for Job file
                $path = $obj->getJob()->getId();
            } elseif(!is_null($obj->getAllContact()) && !is_null($obj->getAllContact()->getId())) {
                // Get file path for Contact file
                $path = 'contacts/' . $obj->getAllContact()->getId();
            } elseif(!is_null($obj->getTransaction()) && !is_null($obj->getTransaction()->getId())) {
                // Get current User
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                // Get file path for Transaction file
                $path = $user->getBusinesses()->first()->getId() . '/' . $obj->getTransaction()->getId();
            } else {
                // Get file path
                $path = 'single-documents/' . time();
            }
        }
        return !is_null($path) ? ($path . '/' . $file) : $file;
    }
}