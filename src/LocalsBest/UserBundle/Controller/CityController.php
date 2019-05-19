<?php

namespace LocalsBest\UserBundle\Controller;

use Doctrine\ORM\Query\ResultSetMapping;
use LocalsBest\CommonBundle\Controller\SuperController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends SuperController
{
    /**
     * Get Cities by State
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWorkingCitiesByStateAction(Request $request)
    {
        $state = $request->query->get('state', null);
        $q = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('LocalsBestUserBundle:City', 'c');
        $rsm->addFieldResult('c', 'id', 'id');
        $rsm->addFieldResult('c', 'name', 'name');

        $query = 'SELECT c.name, c.id 
              FROM cities as c 
              INNER JOIN states as s on c.state_id = s.id 
              INNER JOIN user_city as u_s on u_s.city_id = c.id 
              LEFT JOIN users as u on u_s.user_id = u.id 
              LEFT JOIN role as r on u.role_id = r.id 
              WHERE u.deleted IS NULL 
              AND r.level > 4
              AND r.level <= 7 '
            . (($state !== null && $state != '') ? ' AND s.short_name = "' . $state .'" ' : '')
            . ' AND c.name LIKE "%' . $q . '%" '
            . ' GROUP BY c.name, c.id 
              ORDER BY c.name ASC'
        ;

        $query = $em->createNativeQuery(
            $query,
            $rsm
        );

        $cities = $query->getArrayResult();

        return new JsonResponse($cities);
    }

    /**
     * Search city by name
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function findCityAction(Request $request)
    {
        $query = $request->query->get('q');
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('LocalsBestUserBundle:City')->createQueryBuilder('c');

        $qb
            ->where('c.name like :query')
            ->setParameter('query', '%'.$query.'%')
        ;

        $results = $qb->getQuery()->getArrayResult();
        $response = [
            'items' => [],
            'incomplete_results' => true,
            'total_count' => count($results)
        ];

        foreach ($results as $city) {
            $array = [];
            $array['id'] = $city['id'];
            $array['name'] = $city['name'];

            $response['items'][] = $array;
        }

        return JsonResponse::create($response);
    }
}