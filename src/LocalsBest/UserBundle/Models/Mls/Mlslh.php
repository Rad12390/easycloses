<?php

namespace LocalsBest\UserBundle\Models\Mls;

/**
 * Get information from http://www.floridarealtors.org/
 * for Locatehomes
 * @author jwhulette@gmail.com
 */
class Mlslh extends Mls
{

  /**
   *
   * @param \Doctrine\ORM\EntityManager $em
   * @param int $boardId
   */
  public function __construct(\Doctrine\ORM\EntityManager $em, $boardId)
  {
    parent::__construct($em, $boardId);
  }

  /**
   * Get MLS data for autofilling form
   * @param string $mlsId
   * @return boolean
   */
  public function getMlsAutofillData($mlsId)
  {
    $data = $this->getPropertyData($mlsId);
    if ($data) {
      $property = $this->formatResults($data);
      return $this->buildResponse('success', $property);
    }
    return false;
  }

  /**
   * Get the public remarks and photos for facebook post
   * @param string $mlsId
   * @return array
   */
  public function getFacebookData($mlsId)
  {
    $sql  = "SELECT remarks, photo_quantity, photo_url FROM `idx_floridarealtors` WHERE `mls_listing_id` = :id";
    $stmt = $this->em->getConnection()->prepare($sql);
    $stmt->bindValue('id', $mlsId);
    $stmt->execute();
    $rs   = $stmt->fetch();
    if ($rs) {
      $photos = $this->getPhotos($rs['photo_quantity'], $rs['photo_url']);
      return array('remarks' => $rs['remarks'], 'photos' => $photos);
    }
    return false;
  }

  /**
   * Get photos for a facebook posts
   * @param stirng $mlsId
   * @return array
   */
  private function getPhotos($quantity, $photo_url)
  {
    $photos = [];
    if ($quantity > 1) {
      for ($i = 2; $i < $quantity; $i++) {
        $photos[] = str_replace(".jpg", "_$i.jpg", $photo_url);
      }
    }
    return array('main' => $photo_url, 'urls' => $photos);
  }

  /**
   * Get the results to display
   * @param array $data
   * @return array
   */
  private function formatResults($data)
  {
    $address = $data['street_number'].' '.$data['street_direction'].' '
        .$data['street_name'].' '.ucwords(strtolower($data['street_type']));
    return $this->setReponseArray(preg_replace('/\s+/', ' ', $address),
            $data['unit_number'], $data['city'], $data['property_state_id'],
            $data['zip_code'], $data['year_built'], $data['sale_price'],
            $data['listing_date'], $data['listing_expiration_date'],
            $data['owners_name'], $data['mls_agent_name'], '',
            $data['mls_agent_phone'], $data['mls_office_name'], '',
            $data['mls_office_phone']);
  }

  /**
   * Get listing information
   * @param stirng $mlsId The MLS id to search on
   * @return array
   */
  private function getPropertyData($mlsId)
  {
    $sql  = "SELECT * FROM `idx_floridarealtors` WHERE `mls_listing_id` = :id";
    $stmt = $this->em->getConnection()->prepare($sql);
    $stmt->bindValue('id', $mlsId);
    $stmt->execute();
    return $stmt->fetch();
  }
}