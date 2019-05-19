<?php

namespace LocalsBest\UserBundle\Models\Mls;

use Doctrine\Common\Persistence\ObjectManager;
use LocalsBest\UserBundle\Models\Mls\Mlsli;
use LocalsBest\UserBundle\Models\Mls\Mlslh;
use LocalsBest\UserBundle\Models\Mls\Mlspr;
use PHRETS;

/**
 * Get property data based on the MLS number
 *
 * @author jwhulette@gmail.com
 */
class Mls
{
    /**
   * A PHRETS object
   * @var object
   */
  protected $rets;

  /**
   * A EntityManager object
   * @var object
   */
  protected $em;

  /**
   * MLS board ids
   * @var array
   */
  protected $boardIds;

  /**
   * The MLS class to use
   * @var object
   */
  protected $class;

  /**
   * The save path
   * @var string
   */
  protected $savePath = '/facebook/';

  /**
   * The server domain
   * @var string
   */
  protected $domain;

  /**
   * __construct()
   * @param \Doctrine\ORM\EntityManager $em
   * @param array $associationId
   */
  public function __construct(ObjectManager $em, $associationId = '')
  {
      $this->em       = $em;
      $this->boardIds = $associationId;
  }

  /**
   * Delete any images downloaded for facebook post
   * @param string $mlsId
   */
  public function deleteImages()
  {
      $path = getcwd().$this->savePath;
      $now  = new \DateTime();
      foreach (new \DirectoryIterator($path) as $fileinfo) {
          if ($fileinfo->isDot()) {
              continue;
          }
          $dirDate = new \DateTime();
          $dirDate->setTimestamp($fileinfo->getMTime());
          $diff    = $dirDate->diff($now);
          if ($diff->format("%i") < 30) {
              $this->deleteDirectory($fileinfo->getPathname());
          }
      }
  }

  /**
   * Recursively delete a directory
   * @param string $dir
   * @return boolean
   */
  private function deleteDirectory($dir)
  {
      $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
      $ri = new \RecursiveIteratorIterator($di,
        \RecursiveIteratorIterator::CHILD_FIRST);
      foreach ($ri as $file) {
          echo $file;
          $file->isDir() ? rmdir($file) : unlink($file);
      }
      rmdir($dir);
      return true;
  }

  /**
   * Create a directory for facebook images we have to download
   * @param string $mlsId An MLS id
   * @return string
   */
  protected function getImageDirectory($mlsId)
  {
      $path = getcwd().$this->savePath.$mlsId;
      if (!is_dir($path)) {
          mkdir($path, 0777, true);
      }
      return $path;
  }

  /**
   * Get auto fill information based on MLS id
   * @param string $mlsId
   * @param string $domain The server domain
   * @return type
   */
  public function getFacebookInfo($mlsId, $domain)
  {
      foreach ($this->boardIds as $id) {
          switch ($id) {
        case 9:
          $this->class = new Mlsli($this->em, $id);
          break;
        case 8:
          $this->class = new Mlslh($this->em, $id);
          break;
        case 7:
          $this->class = new Mlspr($this->em, $id);
          break;
        default:
          return $this->buildResponse('failure',
                  'Unable to find requested board id!');
      }
          $data = $this->class->getFacebookData($mlsId, $domain);

          if ($data != false) {
              return $data;
          }
      }
  }

  /**
   * Get auto fill information based on MLS id
   * @param type $mlsId
   * @return type
   */
  public function autofill($mlsId)
  {
      foreach ($this->boardIds as $id) {
          switch ($id) {
        case 9:
          $this->class = new Mlsli($this->em, $id);
          break;
        case 8:
          $this->class = new Mlslh($this->em, $id);
          break;
        case 7:
          $this->class = new Mlspr($this->em, $id);
          break;
        default:
          return $this->buildResponse('failure',
                  'Unable to find requested board id!');
      }
          $data = $this->class->getMlsAutofillData($mlsId);
          if ($data != false) {
              return $data;
          }
      }
      return $this->buildResponse('failed', 'Unable to find property!');
  }

  /**
   * Build the response to send
   * @param string $result The result status success/failure
   * @param mixed $data On success an array of data, on failure a failure message
   * @return array
   */
  protected function buildResponse($result, $data)
  {
      return array(
        'result' => $result,
        'mls_data' => $data
    );
  }

  /**
   * Return listing information
   * @param string $address
   * @param string $unit
   * @param string $city
   * @param string $state
   * @param string $zip
   * @param string $yearBuilt
   * @param string $price
   * @param string $listDate
   * @param string $expirationDate
   * @param string $sellerName
   * @param string $listAgentName
   * @param string $listAgentEmail
   * @param string $listAgentPhone
   * @param string $listOfficeName
   * @param string $listOfficeEmail
   * @param string $listOfficePhone
   * @return array
   */
  protected function setReponseArray($address, $unit, $city, $state, $zip,
                                     $yearBuilt, $price, $listDate,
                                     $expirationDate, $sellerName,
                                     $listAgentName, $listAgentEmail,
                                     $listAgentPhone, $listOfficeName,
                                     $listOfficeEmail, $listOfficePhone)
  {
      return [
        'address' => $address,
        'unit' => $unit,
        'city' => $city,
        'state' => $state,
        'zip' => (string) $zip,
        'year_built' => (string) $yearBuilt,
        'price' => (string) $price,
        'list_date' => $this->formatDate($listDate),
        'expiration_date' => $this->formatDate($expirationDate),
        'seller_name' => $sellerName,
        'list_agent_name' => $listAgentName,
        'list_agent_email' => $listAgentEmail,
        'list_agent_phone' => $this->cleanPhoneNumbers($listAgentPhone),
        'list_office_name' => $listOfficeName,
        'list_office_email' => $listOfficeEmail,
        'list_office_phone' => $this->cleanPhoneNumbers($listOfficePhone)
    ];
  }

  /**
   * Format a date into a standard format of YYYY-MM-DD
   * @param string $date
   * @return string
   */
  private function formatDate($date)
  {
      if ($date != '') {
          $format = new \DateTime($date);
          return $format->format('Y-m-d');
      }
      return '';
  }

  /**
   * Return a phone number with only digits, removing seperators
   * @param string $phoneNumber
   * @return string
   */
  private function cleanPhoneNumbers($phoneNumber)
  {
      return preg_replace("/[^0-9]/", "", $phoneNumber);
  }

  /**
   * Query a RETS board for property information
   *
   * @param string $resource A rets resouce
   * @param string $class A rets class
   * @param string $query A rets class
   * @param string $select A custom query
   * @param int    $limit The number of records to return
   *
   * @return object
   */
  protected function runRetsQuery($resource, $class, $query, $select = '', $limit = 1)
  {
      $results = $this->rets->Search(
        $resource, $class, $query,
        [
        'Select' => $select,
        'QueryType' => 'DMQL2',
        'Count' => 1, // count and records
        'Format' => 'COMPACT-DECODED',
        'Limit' => $limit, // Only one record returned
        'StandardNames' => 0, // give system names
        ]
    );
      return $results;
  }

  /**
   * Connect to a rets server
   * @return prets object
   */
  protected function connectToRets($credentials)
  {
      try {
          \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
          $this->rets = new PHRETS\Session($this->getConfiguraton($credentials));

          $this->rets->Login();
      } catch (\PHRETS\Exceptions\MissingConfiguration $ex) {
          die($ex->getMessage());
          return $ex->getMessage();
      } catch (\PHRETS\Exceptions\CapabilityUnavailable $ex) {
          die($ex->getMessage());
          return $ex->getMessage();
      }
  }

  /**
   * Get a rets configuration object
   * @param array $credentials The rets board credentials and settings
   * @return \PHRETS\Configuration
   */
  protected function getConfiguraton($credentials)
  {
      $config = new \PHRETS\Configuration;
      $config->setLoginUrl($credentials['feed_url']);
      $config->setUsername($credentials['username']);
      $config->setPassword($credentials['password']);
      $config->setRetsVersion($credentials['rets_version']); // see constants from \PHRETS\Versions\RETSVersion
//      $config->setUserAgent('PHRETS/2.0');
      $config->setUserAgent($credentials['rets_user_agent']);
      $config->setUserAgentPassword($credentials['rets_user_agent_password']); // string password, if given
      $config->setHttpAuthenticationMethod($credentials['http_authentication_method']); // or 'basic' if required
      $config->setOption('use_post_method', $credentials['use_post_method']); // boolean
      $config->setOption('disable_follow_location', $credentials['disable_follow_location']); // boolean

      return $config;
  }

  /**
   * Get the credentials for the board
   * @return array
   */
  protected function getRetsCredentials($id)
  {
      $connection = $this->em->getConnection();
      $statement  = $connection->prepare("SELECT * FROM `feed_credentials` WHERE `association_id` = :id");
      $statement->bindValue('id', $id);
      $statement->execute();
      return $statement->fetch();
  }
}
