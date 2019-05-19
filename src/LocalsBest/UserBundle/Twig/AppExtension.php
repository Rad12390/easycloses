<?php

namespace LocalsBest\UserBundle\Twig;
use LocalsBest\ShopBundle\Entity\Sku;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('phone', array($this, 'phoneFilter')),
            new \Twig_SimpleFilter('rating', array($this, 'ratingFilter')),
        );
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('vichUploderStatus', array($this, 'vichUploderStatus')),
        );
    }

    public function phoneFilter($number)
    {
        $phone = "(" . substr($number, 0, 3) . ") " . substr($number, 3, 3) . "-" . substr($number, 6);

        return $phone;
    }

    public function ratingFilter($rating)
    {
        $a = floor($rating);
        $b = $rating - $a;

        $result = '';

        for($i=0; $i<$a; $i++) {
            $result .= '<li><i class="fa fa-star"></i></li>';
        }

        if($b > 0) {
            $i++;
            $result .= '<li><i class="fa fa-star-half-o"></i></li>';
        }

        if($i<5) {
            for($j=0; $j<(5-$i); $j++) {
                $result .= '<li><i class="fa fa-star-o"></i></li>';
            }
        }

        return $result;
    }
    
    public function vichUploderStatus($entity,$temp_download_aws_folder)
    {
        $image_path= $entity->getPackage()->getImages();
        $full_path= $temp_download_aws_folder.$image_path;
        $header_response = get_headers($full_path, 1);
            if ( strpos( $header_response[0], "403" ) !== false )
            {
               //image not found
                return 0;
            } 
            else 
            {
               //image found
                return 1;
            }
    }

    public function getName()
    {
        return 'app_extension';
    }
}