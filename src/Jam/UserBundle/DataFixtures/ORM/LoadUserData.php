<?php

namespace Jam\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\Instrument;
use Jam\CoreBundle\Entity\MusicianGenre;
use Jam\CoreBundle\Entity\MusicianInstrument;
use Jam\LocationBundle\Entity\Location;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jam\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /* @var $user \Jam\UserBundle\Entity\User */

        $user = $userManager->createUser();

        $user->setUsername('test');
        $user->setFirstName('test');
        $user->setLastName('test');
        $user->setEmail('test2@test.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);
        $user->setAboutMe("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting");

        $location = new Location();
        $location->setLat('60.15917175');
        $location->setLng('24.88327687623');
        $location->setAddress('Pajalahdentie, Lauttasaari, Helsinki, Finland');
        $location->setNeighborhood('Lauttasaari');
        $location->setAdministrativeAreaLevel3('Helsinki');
        $user->setLocation($location);

        $userManager->updateUser($user);

        /**
         * @var User
         */
        $user = $userManager->createUser();

        $user->setUsername('slobodan');
        $user->setFirstName('Slobodan');
        $user->setLastName('Stanic');
        $user->setEmail('slobodan.stanic88@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.1570861');
        $location->setLng('24.8692208');
        $location->setAddress('Isokaari, Lauttasaari, Helsinki, Finland');
        $user->setLocation($location);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Electric Guitar')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $heavyMetal = new MusicianGenre();
        $heavyMetal->setMusician($user);
        $heavyMetal->setGenre($manager->getRepository('JamCoreBundle:Genre')->findOneBy(array('name' => 'Heavy Metal')));
        $heavyMetal->setPosition(1);

        $user->addGenre($heavyMetal);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('mstanic');
        $user->setFirstName('Marija');
        $user->setLastName('Stanic');
        $user->setEmail('malusev.marija@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Drums')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(5);
        $user->addInstrument($guitar);

        $location = new Location();
        $location->setLat('60.173282');
        $location->setLng('24.919395');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('alexanderSax');
        $user->setFirstName('Alexander');
        $user->setLastName('McQuin');
        $user->setEmail('alexa.mcquin666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);
        $user->setLocation($location);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Saxophone')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Electric Guitar')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Acoustic Guitar')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $user->setUsername('mäentakanen');
        $user->setFirstName('Salomo');
        $user->setLastName('Mäentakanen');
        $user->setEmail('salomo666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);
        $user->setIsTeacher(true);

        $location = new Location();
        $location->setLat('60.155294');
        $location->setLng('24.879237');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Drums')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $user->setUsername('anna');
        $user->setFirstName('Anna');
        $user->setLastName('Hamalainen');
        $user->setEmail('anna@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.158594');
        $location->setLng('24.879227');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('pierre-emmanuel');
        $user->setFirstName('Pierre-Emmanuel');
        $user->setLastName('Léonard');
        $user->setEmail('pierre.emmanuel666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.158299');
        $location->setLng('24.879227');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('kampa');
        $user->setFirstName('Aleksi');
        $user->setLastName('Kemppainen');
        $user->setEmail('alek@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Piano')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $location = new Location();
        $location->setLat('60.152299');
        $location->setLng('24.878227');
        $location->setCountry('Finland');
        $location->setAddress('Helsinki');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('mikkoHel');
        $user->setFirstName('Mikko');
        $user->setLastName('Niskanen');
        $user->setEmail('mikko@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'DJ')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $location = new Location();
        $location->setLat('60.1544259');
        $location->setLng('24.879927');
        $location->setCountry('Finland');
        $location->setAddress('Helsinki');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('noor82');
        $user->setFirstName('Noora');
        $user->setLastName('Koistinen');
        $user->setEmail('noora@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $guitar = new MusicianInstrument();
        $guitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Vocals (Singing)')));
        $guitar->setMusician($user);
        $guitar->setSkillLevel(2);
        $user->addInstrument($guitar);

        $location = new Location();
        $location->setLat('60.154299');
        $location->setLng('24.879327');
        $location->setCountry('Finland');
        $location->setAddress('Helsinki');
        $user->setLocation($location);

        $userManager->updateUser($user);

        $manager->flush();
    }
}
