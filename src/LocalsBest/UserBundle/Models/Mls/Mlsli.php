<?php

namespace LocalsBest\UserBundle\Models\Mls;

use Doctrine\Common\Cache\FilesystemCache;

/**
 * Get information from http://www.mlsli.com/
 * for CRR Long Island
 * @author jwhulette@gmail.com
 */
class Mlsli extends Mls
{
    private $cache;
    protected $resource = 'Property';
    protected $class    = ['ResidentialProperty', 'RentalHome', 'MultiFamily', 'LotsAndLand',
      'CommercialProperty', 'BusinessOpportunity'];

    public function __construct(\Doctrine\ORM\EntityManager $em, $boardId)
    {
        parent::__construct($em, $boardId);
        $this->cache = new FilesystemCache(sys_get_temp_dir());
        $this->connectToRets($this->getCredentials($boardId));
    }

  /**
   * Get all listing associated with an agent or brokerage
   *
   * @method getListingsByAgentBrokerageId
   *
   * @param $id int     The agent or brokerage board id
   *
   * @return array      The listings
   */
  public function getListingsByAgentBrokerageId($id)
  {
      $listings = [];
      foreach ($this->class as $class) {
          $agentId = (int) $id; // The agent id must be an integer
          $query = "(Agent_id=$agentId)|(Code=$id),(Ld=2017-01-01+)"; // Search for agent or brokerage id
          switch ($class) {
            case 'ResidentialProperty':
              $select = 'Disp_addr,Ml_num,Ld,Addr,Unit_num,Town,Ste,Zip,Lp_dol,List_agent,Rltr,Sqft,Br,Bath_tot,Tour_url,Ad_text,Photo_count';
              break;
            case 'RentalHome':
              $select = 'Disp_addr,Ml_num,Addr,Apt_num,Ld,Town,Ste,Zip,Lp_dol,List_agent,Rltr,Sqft,Br,Bath_tot,Tour_url,Ad_text,Photo_count';
              break;
            default:
              $select = 'Disp_addr,Ml_num,Ld,Addr,Town,Ste,Zip,Lp_dol,List_agent,Rltr,Tour_url,Ad_text,Photo_count';
              break;
          }

          $results =  $this->runRetsQuery($this->resource, $class, $query, $select, 9999);
          if ($results->getTotalResultsCount() > 0) {
              foreach ($results->toArray() as $listing) {
                  /*
                 *  Check if unit/apt # is returned
                 */
                $unit    = '';
                  if (isset($info['Unit_num'])) {
                      $unit =  ' '.$info['Unit_num'];
                  }
                  if (isset($info['Apt_num'])) {
                      $unit = ' '.$info['Apt_num'];
                  }

                  $listings[] = [
                    'dispaly_address_internet' => $listing['Disp_addr'],
                    'mls_number'               => $listing['Ml_num'],
                    'address'                  => $listing['Addr'] . $unit .' ' . $listing['Town'] .',' . $listing['Ste'] .' '. $listing['Zip'],
                    'listing_date'             => $listing['Ld'],
                    'price'                    => $listing['Lp_dol'],
                    'listing_agent'            => $listing['List_agent'],
                    'brokerage'                => $listing['Rltr'],
                    'sqft'                     => (isset($listing['Sqft']) ? $listing['Sqft'] : null),
                    'bedrooms'                 => (isset($listing['Br']) ? $listing['Br'] : null),
                    'bathrooms'                => (isset($listing['Bath_tot']) ? $listing['Bath_tot'] : null),
                    'virtual_tour'             => $listing['Tour_url'],
                    'remarks'                  => $listing['Ad_text'],
                    'photo_count'              => $listing['Photo_count'],
                    'photo_url'                => 'http://rets.mlsli.com:6103/objects/Property/Photo/'.$listing['Ml_num'].'/PHOTO_ID',
                ];
              }
          }
      }
      return $listings;
  }

    public function getPhotoUrlsForMls($mlsid)
    {
        $photos = null;
        $objects = $this->rets->GetObject('Property', 'Photo', $mlsid, '*', 1);
        if ($objects->count() == 0) {
            $photos = null;
        } else {
            foreach ($objects as $object) {
                $photos[] = $object->getLocation();
            }
        }
        return $photos;
    }

  /**
   * Get the public remarks and photos for facebook post
   * @param string $mlsId
   * @return array
   */
  public function getFacebookData($mlsId, $domain)
  {
      foreach ($this->class as $class) {
          $results = $this->facebookQueryRemarks($class, $mlsId);
          if ($results->getTotalResultsCount() == 1) {
              $listing = $results->toArray();
              $photos  = $this->getPhotos($mlsId, $domain);
              return array('remarks' => $listing[0]['Ad_text'], 'photos' => $photos);
          }
      }
  }

  /**
   * Get photos for a facebook posts
   * @param stirng $mlsId
   * @return array
   */
  private function getPhotos($mlsId, $domain)
  {
      $photos  = [];
      $default = false;
      $objects = $this->rets->GetObject('Property', 'Photo', $mlsId, '*', 1);
      foreach ($objects as $object) {
          $save  = $this->getImageDirectory($mlsId).'/'.$object->getObjectId().'.jpg';
          file_put_contents($save, $object->getContent());
          $photo = explode('web', $save);
          if ($object->isPreferred()) {
              $mainImage = $photo[1];
              $default   = true;
          }

          $photos[] = $domain.$photo[1];
      }
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
      $query = "(Ml_num=$mlsId)";
      return $this->runRetsQuery($this->resource, $class, $query, 'Ad_text');
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
    return $this->buildResponse('failure',
            'Unable to find listing for given MLS Id');
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
    /*
     *  Check if unit/apt # is returned
     */
    $unit    = '';
      if (isset($info['Unit_num'])) {
          $unit = $info['Unit_num'];
      }
      if (isset($info['Apt_num'])) {
          $unit = $info['Apt_num'];
      }

      $agent  = $this->getAgentInfo($info['Agent_id']);
      $office = $this->getOfficeInfo($agent['Office_num']);

      return $this->setReponseArray(
            $info['Addr'], $unit, $info['Town'], $info['Ste'], $info['Zip'],
            $info['Yr_built'], $info['Lp_dol'], $info['Ld'], $info['Xd'],
            $info['Owner'], $agent['F_name'].' '.$agent['L_name'],
            $agent['Mem_email'], $info['Lagt_ph'], $office['Firm_name'],
            $office['Email'], $office['Phone_num']
    );
  }

  /**
   * Get information about the agent
   * @param int $agentId The agent id from the listing
   * @return array
   */
  private function getAgentInfo($agentId)
  {
      $results = $this->runRetsQuery('Agent', 'Agent', "(Member_num=$agentId)");
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
      $results = $this->runRetsQuery('Office', 'Office', "(Office_num=$officeId)");
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
      $query = "(Ml_num=$mlsId)";
      return $this->runRetsQuery($this->resource, $class, $query);
  }

  /**
   * Get the board credentials
   * @return array
   */
  private function getCredentials($boardId)
  {
      $cacheId = 'retsboard_mlsli';
      if ($this->cache->contains($cacheId)) {
          return $this->cache->fetch($cacheId);
      } else {
          $retsCred = $this->getRetsCredentials($boardId);
          $this->cache->save($cacheId, $retsCred, 3600);
          return $retsCred;
      }
  }
}
