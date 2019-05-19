<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LocalsBest\UserBundle\Models;

use Facebook\Facebook;

/**
 * Description of Facebook
 *
 * @author wes
 */
class FacebookPost
{
  /**
   *
   * @var type
   */
  private $appId;

  /**
   *
   * @var type
   */
  private $fb;

  /**
   *
   * @var type
   */
  protected $logger;

  /**
   * __construct
   * @param string $app_id The app id
   * @param sting $app_secret The app seceret phrase
   */
  public function __construct($app_id, $app_secret, $logger)
  {
    $this->logger = $logger;
    $this->appId  = $app_id;
    $this->fb     = new Facebook([
        'app_id' => $app_id,
        'app_secret' => $app_secret,
        'default_graph_version' => 'v2.5',
    ]);
  }

  /**
   * Create a new facebook albumn from the selected images
   * @param string $token The token for the page to post to
   * @param int $pageId The id of the page to post to
   * @param string $message The album message
   * @param string $albumName The ablum name
   * @param string $defaultImg The default or main image
   * @param array $images An array of images to add to the ablum
   */
  public function createFacebookPost($token, $pageId, $message, $albumName,
                                     $defaultImg, $images)
  {
    $album   = $this->createAlbumn($pageId, $message, $albumName, $token);
    $albumId = $album->getDecodedBody();
    $this->uploadImages($token, $albumId['id'], $defaultImg, $images);
    $this->shareAlbumn($token, $albumId['id'], $message, $pageId);
  }

  /**
   * Create a new ablum for the page
   * @param string $message
   * @param string $albumName
   * @param string $token
   * @return int
   */
  private function createAlbumn($pageId, $message, $albumName, $token)
  {
    $params = [
        'message' => $message,
        'name' => $albumName,
    ];

    $album = $this->fb->post("/$pageId/albums", $params, $token);
    return $album;
  }

  /**
   * Upload images to an album
   * @param string $token
   * @param int $albumId
   * @param string $defaultImg
   * @param array $images
   */
  private function uploadImages($token, $albumId, $defaultImg, $images)
  {
    // Upload the default photo first
    $params = [
        'url' => $defaultImg,
        'no_story' => true
    ];
    $this->fb->post("/$albumId/photos", $params, $token);

    // Upload remaining images
    foreach ($images as $img) {
      $params = [
          'url' => $img,
          'no_story' => true
      ];

      $this->fb->post("/$albumId/photos", $params, $token);
    }
  }

  /**
   * Share the ablum on the page news feed
   * @param string $token
   * @param int $albumId
   * @param string $message
   * @param int $pageId
   * @throws Exception
   */
  private function shareAlbumn($token, $albumId, $message, $pageId, $cnt = 0)
  {
    if ($cnt > 5) {
      throw new Exception('Unable to post album to feed!');
    }
    $link   = 'https://www.facebook.com/media/set/?set=a.'.$albumId;
    $params = [
        'message' => $message,
        'link' => $link,
    ];

    try {
      $this->fb->post("/$pageId/feed", $params, $token);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
      $msg    = 'Graph returned an error: '.$e->getMessage();
      $this->logger->error($msg);
      $newCnt = $cnt + 1;
      $this->shareAlbumn($token, $albumId, $message, $pageId, $newCnt);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
      $msg    = 'Facebook SDK returned an error: '.$e->getMessage();
      $this->logger->error($msg);
      $newCnt = $cnt + 1;
      $this->shareAlbumn($token, $albumId, $message, $pageId, $newCnt);
    }
  }

  /**
   * Get the pages the user can post to
   * @param string $token
   * @return array
   */
  public function getFacebookAccounts($token)
  {
    $userAccounts = array();
    $accounts     = $this->fb->get('/me/accounts', $token);

    $data = $accounts->getDecodedBody();
    foreach ($data['data'] as $accts) {
      $userAccounts[] = array(
          'token' => $accts['access_token'],
          'name' => $accts['name'],
          'id' => $accts['id']);
    }

    $personal       = $this->fb->get('/me', $token);
    $userAccounts[] = array(
        'token' => $token,
        'name' => 'My Personal Page',
        'id' => $personal->getDecodedBody()['id']);

    return array_reverse($userAccounts);
  }

  /**
   * Get the facebook token after allowing the app access
   * @return string
   */
  public function getFacebookToken()
  {
    $helper = $this->fb->getRedirectLoginHelper();

    try {
      $accessToken = $helper->getAccessToken();
      if (is_null($accessToken)) {
        return;
      }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
      $msg = 'Graph returned an error: '.$e->getMessage();
      $this->logger->error($msg);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
      $msg = 'Facebook SDK returned an error: '.$e->getMessage();
      $this->logger->error($msg);
    }
    // The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $this->fb->getOAuth2Client();
    if (!$accessToken->isLongLived()) {
      // Exchanges a short-lived access token for a long-lived one
      try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
      } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $msg = "Error getting long-lived access token: ".$helper->getMessage();
        $this->logger->error($msg);
      }
    }
    return (string) $accessToken;
  }

  /**
   * Get the URL to have th user log into facebook
   * @param string $callbackUrl The facebook callback url to return to
   * @return string A facebook login URL
   */
  public function getFacebookLoginUrl($callbackUrl)
  {
    $helper      = $this->fb->getRedirectLoginHelper();
    $permissions = ['manage_pages,publish_pages,publish_actions,user_photos']; // Optional permissions
    $loginUrl    = $helper->getLoginUrl($callbackUrl, $permissions);
    return $loginUrl;
  }

  /**
   * Check to see if we have a connection to facebook
   * @param string $url The current url
   * @return mixed
   */
  public function facebookCheck($url, $token)
  {
    $check       = false;
    $callbackUrl = $this->getFacebookLoginUrl($url.'/fb-callback');

    if ($token === NULL) {
      $check = $callbackUrl;
    } else {
      try {
        $this->fb->get('/me', $token); // Check for app authorization
      } catch (\Facebook\Exceptions\FacebookResponseException $e) {
        $msg   = 'Graph returned an error: '.$e->getMessage();
        $this->logger->error($msg);
        $check = $callbackUrl;
      } catch (\Facebook\Exceptions\FacebookSDKException $e) {
        $check = $callbackUrl;
        $msg   = 'Facebook SDK returned an error: '.$e->getMessage();
        $this->logger->error($msg);
      }
    }
    return $check;
  }


    /**
     * Get the pages the user can post to (update)
     * @param string $token
     * @return array
     */
    public function getFacebookAccountsClear($token)
    {
        $userAccounts = array();
        $accounts     = $this->fb->get('/me/accounts', $token);

        $data = $accounts->getDecodedBody();
        foreach ($data['data'] as $accts) {
            $userAccounts[] = array(
                'token' => $accts['access_token'],
                'name' => $accts['name'],
                'id' => $accts['id']);
        }

        $personal       = $this->fb->get('/me', $token);
        $info = $personal->getDecodedBody();
        $userAccounts[] = array(
            'token' => $token,
            'name' => $info['name'],
            'id' => $info['id']
        );

        return array_reverse($userAccounts);
    }
}