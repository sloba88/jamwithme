<?php

namespace Jam\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\LocationBundle\Entity\Location;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

        $user = $userManager->createUser();

        $user->setUsername('test');
        $user->setFirstName('test');
        $user->setLastName('test');
        $user->setEmail('test2@test.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.163718');
        $location->setLng('24.915447');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('slobodan');
        $user->setFirstName('Slobodan');
        $user->setLastName('Stanic');
        $user->setEmail('slobodan.stanic88@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.158294');
        $location->setLng('24.879227');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('malusev');
        $user->setFirstName('Marija');
        $user->setLastName('Malusev');
        $user->setEmail('malusev.marija@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $location = new Location();
        $location->setLat('60.173282');
        $location->setLng('24.919395');
        $user->setLocation($location);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('alexander666');
        $user->setFirstName('Alexander');
        $user->setLastName('McQuin');
        $user->setEmail('alexa.mcquin666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('mäentakanen');
        $user->setFirstName('Salomo');
        $user->setLastName('Mäentakanen');
        $user->setEmail('salomo666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('rada');
        $user->setFirstName('Radmila');
        $user->setLastName('Zelic Josimov');
        $user->setEmail('radmila.zelic666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $userManager->updateUser($user);

        //
        $user = $userManager->createUser();

        $user->setUsername('pierre-emmanuel');
        $user->setFirstName('Pierre-Emmanuel');
        $user->setLastName('Léonard');
        $user->setEmail('pierre.emmanuel666@gmail.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $manager->flush();
    }
}