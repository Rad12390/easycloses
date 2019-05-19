<?php

namespace LocalsBest\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SystemMessageController extends Controller
{
    public function blocksForLoginAction(Request $request)
    {
        $scope = $request->get('route');

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('LocalsBestUserBundle:SystemMessage')->createQueryBuilder('sm');

        $qb
            ->where(
                $qb->expr()->andX(
                    'sm.status = 1',
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            'sm.startedAt IS NULL',
                            'sm.endedAt IS NULL'
                        ),
                        $qb->expr()->andX(
                            'sm.startedAt IS NULL',
                            'sm.endedAt > :date'
                        ),
                        $qb->expr()->andX(
                            'sm.startedAt < :date',
                            'sm.endedAt IS NULL'
                        ),
                        $qb->expr()->andX(
                            'sm.startedAt < :date',
                            'sm.endedAt > :date'
                        )
                    )
                )
            )
            ->setParameter('date', date('Y-m-d H:i:s'))
        ;

        if($scope == 'login') {
            $qb
                ->andWhere('sm.scope <> :scope')
                ->setParameter('scope', 'inner')
            ;
        } else {
            $qb
                ->andWhere('sm.scope <> :scope')
                ->setParameter('scope', 'login')
            ;
        }

        $messages = $qb->getQuery()->getArrayResult();

        return $this->render('@LocalsBestUser/system_message/blocks_for_login.html.twig', ['messages' => $messages]);
    }
}
