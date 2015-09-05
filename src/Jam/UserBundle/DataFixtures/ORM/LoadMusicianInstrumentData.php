<?php

namespace Jam\UserBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Instrument;
use Jam\CoreBundle\Entity\MusicianInstrument;
use Jam\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMusicianInstrumentData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $slobodan = $manager->getRepository('JamUserBundle:User')->findOneBy(array('username' => 'slobodan'));

        if($slobodan instanceof User) {

            $slobodanGuitar = new MusicianInstrument();
            $slobodanGuitar->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Electric Guitar')));
            $slobodanGuitar->setMusician($slobodan);
            $slobodanGuitar->setSkillLevel(2);

            $manager->persist($slobodanGuitar);

            $slobodanBass = new MusicianInstrument();
            $slobodanBass->setInstrument($manager->getRepository('JamCoreBundle:Instrument')->findOneBy(array('name' => 'Electric Bass')));
            $slobodanBass->setMusician($slobodan);
            $slobodanBass->setSkillLevel(1);

            $manager->persist($slobodanBass);
        }

        $manager->flush();

    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 100;
    }


}