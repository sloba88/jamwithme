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
            new FOS\UserBundle\FOSUserBundle(),
            new Jam\CoreBundle\JamCoreBundle(),
            new Jam\WebBundle\JamWebBundle(),
            new Jam\UserBundle\JamUserBundle(),
            new Jam\LocationBundle\JamLocationBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Misd\GuzzleBundle\MisdGuzzleBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Jam\ApiBundle\JamApiBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new SunCat\MobileDetectBundle\MobileDetectBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Liuggio\ExcelBundle\LiuggioExcelBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
