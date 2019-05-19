<?php namespace LocalsBest\MedAppUserBundle\Services;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class VichUploaderCustom
{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Check file existing on AWS
     *
     * @param $object
     * @param string $bucket
     * @param bool $isSmall
     *
     * @return string
     */
    public function getImage($object, $bucket, $isSmall = true,$isheader = false)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($object, $bucket);
        try {
            if ($path != '' && $path !== null) {
                $client = new Client(['defaults' => ['http_errors' => false]]);

                $response = $client->get($path);

                if ($response->getStatusCode() != 200) {
                    $path = "/images/empty-avatar.png";
                }
            } else {
                $path = "/images/empty-avatar.png";
            }
        } catch (\Exception $e) {
            $path = "/images/empty-avatar.png";
        }
        if(!$isheader){
            $result = '<img style="width: '
                . ($isSmall ? '29px' : '100%') . ';" src="'
                . $path . '" class="img" alt="Image"/>';
        }
        else{
            $result= $path;
        }
        return $result;
    }
}