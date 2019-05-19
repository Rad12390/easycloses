<?php

namespace LocalsBest\UserBundle\Models\Mls;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * This class gets the board id of the transacton
 *
 * @author wes
 */
class Boards
{
  /**
   * Doctrine2 Entitymanager
   * @var object
   */
  private $em;

  public function __construct(ObjectManager $em)
  {
    $this->em = $em;
  }

  /**
   * Get a listing of board the user belongs to
   * @param  object $transaction A doctrine transaction object
   * @return array An array of board ids
   */
  public function getBoardIds($transaction)
  {
    /** @var \LocalsBest\UserBundle\Entity\User $user */
    $user            = $transaction->getCreatedBy();
    // to get user phone number you can use next: $user->getPrimaryPhone()->getNumber()
    $usersBoardsInfo = $this->em->getRepository('LocalsBestUserBundle:AssociationRow')->findBy(['user' => $user]);

    $boardsIds = [];

    if (count($usersBoardsInfo) > 0) {
      /** @var \LocalsBest\UserBundle\Entity\AssociationRow $item */
      foreach ($usersBoardsInfo as $item) {
        $boardsIds[] = $item->getAssociation()->getId();
      }
    } else {
      $business         = $user->getBusinesses()[0];
      $owner            = $business->getOwner();
      $brokerBoardsInfo = $this->em->getRepository('LocalsBestUserBundle:AssociationRow')->findBy(['user' => $owner]);

      foreach ($brokerBoardsInfo as $item) {
        $boardsIds[] = $item->getAssociation()->getId();
      }
    }
    return $boardsIds;
  }

  public function getBoardIdsByUser($userId)
  {
    $user = $this->em->getRepository('LocalsBestUserBundle:User')->find($userId);
    // to get user phone number you can use next: $user->getPrimaryPhone()->getNumber()
    $usersBoardsInfo = $this->em->getRepository('LocalsBestUserBundle:AssociationRow')->findBy(['user' => $user]);

    $boardsIds = [];

    if (count($usersBoardsInfo) > 0) {
      /** @var \LocalsBest\UserBundle\Entity\AssociationRow $item */
      foreach ($usersBoardsInfo as $item) {
        $boardsIds[] = $item->getAssociation()->getId();
      }
    } else {
      $business         = $user->getBusinesses()[0];
      $brokerBoardsInfo = $this->em->getRepository('LocalsBestUserBundle:AssociationRow')
          ->findBy(['business' => $business]);

      foreach ($brokerBoardsInfo as $item) {
        $boardsIds[] = $item->getAssociation()->getId();
      }
    }
    return $boardsIds;
  }
}