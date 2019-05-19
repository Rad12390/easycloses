<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new LocalsBest\UserBundle\LocalsBestUserBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new LocalsBest\NotificationBundle\LocalsBestNotificationBundle(),
            new LocalsBest\CommonBundle\LocalsBestCommonBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new LocalsBest\RestBundle\LocalsBestRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new NimbleImps\DeployBundle\NimbleImpsDeployBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Aws\Symfony\AwsBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Core23\DompdfBundle\Core23DompdfBundle(),
            new Jhg\NexmoBundle\JhgNexmoBundle(),
            new LocalsBest\AdminBundle\LocalsBestAdminBundle(),
            new LocalsBest\WordPressApiBundle\LocalsBestWordPressApiBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new LocalsBest\ShopBundle\LocalsBestShopBundle(),
            new LocalsBest\MedAppUserBundle\LocalsBestMedAppUserBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }
        
        if ('dev' === $this->getEnvironment()) {
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
        }
       
        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
