<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Jam\CoreBundle\Entity\Service;
use Jam\LocationBundle\Entity\Location;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161230081914 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // ... migration content
    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $service = new Service();
        $service->setDisplayName('Aron Soitin');
        $service->setEmail('jmpajunen@aronsoitin.fi');
        $service->setWebsite('http://aronsoitin.fi/');
        $service->setPhone('09 42891920');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Sahaajankatu 12');
        $location->setLat('60.196497');
        $location->setLng('25.04643');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Bändikeskus');
        $service->setEmail('info@bandikeskus.fi');
        $service->setWebsite('http://www.bandikeskus.fi/');
        $service->setPhone('041 482 4503');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Metsäpurontie 16');
        $location->setLat('60.22983');
        $location->setLng('24.928541');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('How Violins');
        $service->setEmail('info@howviolins.fi');
        $service->setWebsite('http://www.howviolins.fi/');
        $service->setPhone('358504654844');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Mechelininkatu 28 B');
        $location->setLat('60.174881');
        $location->setLng('24.918272');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Kitarapaja');
        $service->setEmail('paja@kitarapaja.com');
        $service->setWebsite('http://www.kitarapaja.com/');
        $service->setPhone('09 1351951');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Kirjatyöntekijänkatu 4');
        $location->setLat('60.176095');
        $location->setLng('24.957478');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Levytukku');
        $service->setEmail('levytukku@gmail.com');
        $service->setWebsite('http://www.levytukku.fi/');
        $service->setPhone('09 625502');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Vuorikatu 4');
        $location->setLat('60.170325');
        $location->setLng('24.947515');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Millbrook');
        $service->setEmail('info@milbrook.fi');
        $service->setWebsite('http://www.millbrook.fi/');
        $service->setPhone('09 1351488');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Pohjoisranta 22');
        $location->setLat('60.175576');
        $location->setLng('24.96119');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Soundtools');
        $service->setEmail('store@soundtools.fi');
        $service->setWebsite('http://www.soundtools.fi/');
        $service->setPhone('029 0800830');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Teollisuuskatu 21');
        $location->setLat('60.194007');
        $location->setLng('24.94846');

        $service->setLocation($location);

        $em->persist($service);

        $service = new Service();
        $service->setDisplayName('Uraltone');
        $service->setEmail('help@uraltone.com');
        $service->setWebsite('http://www.uraltone.com/');
        $service->setPhone('044 7743695');

        $location = new Location();
        $location->setCountry('Finland');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $location->setAddress('Helsinginkatu 30');
        $location->setLat('60.186353');
        $location->setLng('24.947465');

        $service->setLocation($location);

        $em->persist($service);

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
