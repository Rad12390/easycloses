<?php

namespace LocalsBest\UserBundle\Models;

use ZipStream;

/**
 * Description of S3download
 *
 * @author wes
 */
class S3download
{
  /**
   * A Zipstream object
   * @var object
   */
  private $zip;

  /**
   * An S3 client
   * @var AWS S3 object
   */
  private $client;

  /**
   * An S3 bucket name
   * @var string
   */
  private $bucket;

  /**
   * A bucket prefix
   * @var string
   */
  private $prefix;

  public function __construct($s3)
  {
    $this->client = $s3;
  }

  /**
   * An S3 bucketn name
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }

  /**
   * An S3 bucket prefix
   * @param string $prefix
   */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix.'/';
  }

  /**
   * A zip file name
   * @param string $filename
   */
  public function downloadZip($filename)
  {
    // Initialize the ZipStream object and pass in the file name which
    // will be what is sent in the content-disposition header.
    // This is the name of the file which will be sent to the client.
    $this->zip = new ZipStream\ZipStream($filename);

    $this->addFilesToZip();

    // Finalize the zip file.
    $this->zip->finish();
  }

  /**
   *
   * @param string $filename
   */
  private function addFilesToZip()
  {
    //  Get a list of objects from the S3 bucket. The iterator is a high
    //  level abstration that will fetch ALL of the objects without having
    //  to manually loop over responses.
    $result = $this->client->getIterator('ListObjects',
        array(
        'Bucket' => $this->bucket, // required
        'Prefix' => $this->prefix   // optional (path to folder to stream)
    ));

    // We loop over each object from the ListObjects call.
    foreach ($result as $file) {
      $url = $this->client->getObjectUrl($this->bucket, $file['Key']);

      // Get the file name on S3 so we can save it to the zip file
      // using the same name.
      $fileName = basename($file['Key']);
      // We want to fetch the file to a file pointer so we create it here
      // and create a curl request and store the response into the file
      // pointer.
      // After we've fetched the file we add the file to the zip file using
      // the file pointer and then we close the curl request and the file
      // pointer.
      // Closing the file pointer removes the file.
      $fp       = tmpfile();
      $ch       = curl_init($url);
      curl_setopt($ch, CURLOPT_TIMEOUT, 120);
      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_exec($ch);
      curl_close($ch);
      $this->zip->addFileFromStream($fileName, $fp);
      fclose($fp);
    }
  }
}