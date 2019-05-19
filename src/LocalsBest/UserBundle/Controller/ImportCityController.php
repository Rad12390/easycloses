<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportCityController extends SuperController
{
   
    public function importCityAction(Request $request)
    {
        $file = fopen("/home/intersoft-admin/Downloads/USPS.com Full State list Full City list cities usps list  - Sheet1.csv","r");
        $csv = Reader::createFromPath('/home/intersoft-admin/Downloads/USPS.com Full State list Full City list cities usps list  - Sheet1.csv', 'r');
        $em = $this->getDoctrine()->getManager();
        foreach ($csv as $record) {
            $state= ucwords(strtolower($record[0]));
            $city= ucwords(strtolower($record[1]));
            
            $query = 'SELECT * FROM `states` where name=":state"';
            $statement = $em->getConnection()->prepare($query);
            $statement->bindValue(':state', $state);
            $statement->execute();
            $result = $statement->fetchAll();
            
            if(!empty($result)){
                $id= $result[0]['id'];
                $query = 'INSERT INTO cities (name, state_id) VALUES ("'.$city.'",'.$id.')';
                $statement = $em->getConnection()->prepare($query);
                $statement->execute();
            }
            else{
                echo $state;
                dd("dsdsd");
                echo '</br>';
            }
        }
        return new Response(json_encode([
            'result' => 'success',
        ]));
    }
}
