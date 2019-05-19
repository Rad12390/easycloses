<?php

namespace LocalsBest\UserBundle\Models\Mls;

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Get information from http://www.northstarmls.com/
 * for Premier Realestate
 * @author jwhulette@gmail.com
 */
class Mlspr extends Mls
{
  /**
   *
   * @var object
   */
  private $cache;

  /**
   *
   * @var string
   */
  protected $resource = 'Property';

  /**
   *
   * @var array
   */
  protected $class = ['RES', 'MUL ', 'COM', 'LND', 'FRM'];

  /**
   * __construct()
   * @param \Doctrine\ORM\EntityManager $em
   * @param int $boardId The associaton id of the transactions owner
   */
  public function __construct(\Doctrine\ORM\EntityManager $em, $boardId)
  {
    parent::__construct($em, $boardId);
    $this->cache = new FilesystemCache(sys_get_temp_dir());
    $this->connectToRets($this->getCredentials($boardId));
  }

  /**
   * Get the public remarks and photos for facebook post
   * @param string $mlsId
   * @param string $domain The server domain
   * @return array
   */
  public function getFacebookData($mlsId, $domain)
  {
    $photos = [];
    foreach ($this->class as $class) {
      $results = $this->facebookQueryRemarks($class, $mlsId);
      if ($results->getTotalResultsCount() == 1) {
        $listing = $results->toArray();
        $photos  = $this->getPhotos($listing[0]['UID'], $domain);
        return array('remarks' => $listing[0]['PublicRemarks'], 'photos' => $photos);
      }
    }
    return false;
  }

  /**
   * Get photos for a facebook post
   * @param int $uid The unique board id
   * @return array
   */
  private function getPhotos($uid, $domain)
  {
    $photos   = [];
    $default  = false;
    $savepath = $this->getImageDirectory($uid);
    $objects  = $this->rets->GetObject('Property', 'Photo', $uid, '*');
    foreach ($objects as $object) {
      $save  = $savepath.'/'.$object->getObjectId().'.jpg';
      file_put_contents($save, $object->getContent());
      $photo = explode('facebook/', $save);
      if ($object->isPreferred()) {
        $mainImage = $photo[1];
        $default   = true;
      }
      $photos[] = $domain.$this->savePath.$photo[1];
    }
    // If we can' find a default photo, use the first one
    if (!$default) {
      $mainImage = array_shift($photos);
    }
    return array('main' => $mainImage, 'urls' => $photos);
  }

  /**
   * Get the public remarks for a facebook post
   * @param string $class
   * @param string $mlsId
   * @return object
   */
  private function facebookQueryRemarks($class, $mlsId)
  {
    $query = "(MLNumber=$mlsId)";
    return $this->runRetsQuery($this->resource, $class, $query);
  }

  /**
   * Get autofill infomration by query the rets server
   * @param sgring $mlsId
   * @return array
   */
  public function getMlsAutofillData($mlsId)
  {
    /*
     * Search the class to find a matching record
     */
    foreach ($this->class as $class) {
      $results = $this->searchPropertyTypes($class, $mlsId);
      if ($results->getTotalResultsCount() == 1) {
        $data = $this->buildDataArray($results);
        return $this->buildResponse('success', $data);
      }
    }
    /*
     * return error as we did not find the listing
     */
    return false;
  }

  /**
   * Parse the result for the auto fill info
   * @param object $results A PHRETS result object
   * @return array
   */
  private function buildDataArray($results)
  {
    $listing = $results->toArray();
    $info    = array_pop($listing);
    $address = $info['StreetNumber'].' '.$info['StreetDirPrefix'].' '
        .$info['streetdirsuffix'].' '.$info['STREETNAME'].' '.$info['streetsuffix'];
    // Check if unit/apt # is returned
    $unit    = '';
    if (isset($info['UNITNUMBER'])) {
      $unit = $info['UNITNUMBER'];
    }
    if (isset($info['UNIT1_UNITNUM'])) {
      $unit = $info['UNIT1_UNITNUM'];
    }
    if (isset($info['UnitNumber'])) {
      $unit = $info['UnitNumber'];
    }
    $agent  = $this->getAgentInfo($info['listAgent1_MUI']);
    $office = $this->getOfficeInfo($info['listOffice_MUI']);
    return $this->setReponseArray(preg_replace('/\s+/', ' ', $address), $unit,
            $info['City'], $info['StateOrProvince'], $info['PostalCode'],
            $info['YEARBUILT'], $info['LISTPRICE'], $info['LISTDATE'],
            $info['offmarketdate'], '', $agent['DisplayName'], $agent['EMail'],
            $agent['Phone'], $office['Name'], $office['Email'],
            $office['OfficePhone']);
  }

  /**
   * Get information about the agent
   * @param int $agentId The agent id from the listing
   * @return array
   */
  private function getAgentInfo($agentId)
  {
    $results = $this->runRetsQuery('roster', 'agent_roster',
        "(matrix_unique_ID=$agentId)");
    $array   = $results->toArray();
    return reset($array);
  }

  /**
   * Get information about the office
   * @param int $officeId The office id from the listing
   * @return array
   */
  private function getOfficeInfo($officeId)
  {
    $results = $this->runRetsQuery('roster', 'office_roster',
        "(matrix_unique_ID=$officeId)");
    $array   = $results->toArray();
    return reset($array);
  }

  /**
   * Search the rets server for a listiing based on property type and mls id
   * @param stirng $class The proprty type to search
   * @param string $mlsId The MLS listing ID to search
   * @return object
   */
  private function searchPropertyTypes($class, $mlsId)
  {
    $query = "(MLNumber=$mlsId)";
    return $this->runRetsQuery($this->resource, $class, $query);
  }

  /**
   * Get the board credentials
   * @param int $id An association id
   * @return array
   */
  private function getCredentials($id)
  {
    $cacheId = 'retsboard_mlspr';
    if ($this->cache->contains($cacheId)) {
      return $this->cache->fetch($cacheId);
    } else {
      $retsCred = $this->getRetsCredentials($id);
      $this->cache->save($cacheId, $retsCred, 3600);
      return $retsCred;
    }
  }
}