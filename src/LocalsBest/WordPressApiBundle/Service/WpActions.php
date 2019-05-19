<?php

namespace LocalsBest\WordPressApiBundle\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class WpActions
{
    protected $container;
    
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param \LocalsBest\UserBundle\Entity\User $user
     * @param $title
     * @param $body
     * @param array $files
     */
    public function sendPost($user, $title, $body, $files = [])
    {
        if (/*$user->getWpAgentId() !== null &&*/ $user->getWpWebsiteUrl() !== null) {

            $url = $user->getWpWebsiteUrl()
                . (substr($user->getWpWebsiteUrl(), -1) == '/' ? '' : '/');
            //Create Post
            $postPath = "wp-json/wp/v2/posts";

            $business = $user->getBusinesses()->first();

            $wpCredentials = $user->getWpCredentials();

            if ($business->getId() == 155) {
                $username = "basil";
                $password = "147896325";
            } else {
                $username = $wpCredentials['wp_username'];
                $password = $wpCredentials['wp_password'];
            }

            $token = base64_encode($username.':'.$password);

            $headers = ['Authorization: Basic '.$token];

            $params = [
                'status'        => 'publish',
                'title'         => $title,
                'slug'          => $this->slugify($title),
                'content'       => $body,
//                'author'        => $user->getWpAgentId(),
                'categories'    => [138],
                'ping_status'   => 'open',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . $postPath); // set url to post to
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            $postResultJson = curl_exec($ch); // run the whole process

            if($postResultJson == false) {
                echo 'Error1 curl: ' . curl_error($ch);die;
            }

            // Get information about WP Post
            /** @var \stdClass $postResult */
            $postResult = json_decode($postResultJson);
            curl_close($ch);

            $primaryImage = false;

            //Create All Media
            foreach ($files as $image) {
                if ($image === null) {
                    continue;
                }
                $imagePath = "wp-json/wp/v2/media";

                $filename = $image->getClientOriginalName();
                $filedata = $image->getFileInfo()->getPathName();

                $file = fopen($filedata, 'r');
                $size = filesize($filedata);
                $fildata = fread($file, $size);

                $headers = [
                    'authorization: Basic ' . $token,
                    'cache-control: no-cache',
                    'content-disposition: attachment; filename=' . $filename,
                    'content-type: multipart/form-data'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . $imagePath); // set url to post
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fildata);
                curl_setopt($ch, CURLOPT_INFILE, $file);
                curl_setopt($ch, CURLOPT_INFILESIZE, $size);

                $mediaResultJson = curl_exec($ch); // run the whole process

                if($mediaResultJson == false) {
                    echo 'Error2 curl: ' . curl_error($ch);die;
                }

                $mediaResult = json_decode($mediaResultJson);
                curl_close($ch);

                $headers = [
                    'Authorization: Basic ' . $token,
                ];

                $params = [
                    'status'        => 'publish',
//                    'author'        => $user->getWpAgentId(),
                    'post'          => $postResult->id,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url . $imagePath . '/' . $mediaResult->id); // set url to post
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $mediaResultJson = curl_exec($ch); // run the whole process

                if($mediaResultJson == false) {
                    echo 'Error3 curl: ' . curl_error($ch); die;
                }
                $mediaResult = json_decode($mediaResultJson);
                curl_close($ch);

                //IF first media make it primary for Post with Post Update
                if($primaryImage == false) {
                    $params = [
                        'featured_media' => $mediaResult->id,
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url . $postPath . '/' . $postResult->id); // set url to post
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
                    $postResultJson = curl_exec($ch); // run the whole process

                    if($postResultJson == false) {
                        echo 'Error4 curl: ' . curl_error($ch);
                    }
                    curl_close($ch);

                    $primaryImage = true;
                }
            }
        }
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
