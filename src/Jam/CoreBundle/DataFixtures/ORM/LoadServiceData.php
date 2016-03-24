<?php

namespace Jam\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Service;
use Jam\LocationBundle\Entity\Location;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServiceData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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
        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($this->container->get('kernel')->getRootDir() . '/../data/gigplaces.xlsx');

        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);

        foreach ($sheet->getRowIterator(4) as $row) {

            $location = new Location();
            $location->setCountry('Finland');
            $service = new Service();

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

                foreach ($cellIterator as $cell) {
                    if ($cell->getValue() != '') {

                        $cellValue = trim($cell->getValue());

                        if ($cell->getColumn() == 'A') {
                            $location->setAdministrativeAreaLevel3($cellValue);
                        }

                        if ($cell->getColumn() == 'B') {
                            $service->setDisplayName($cellValue);
                        }

                        if ($cell->getColumn() == 'C') {
                            $location->setAddress($cellValue);
                        }

                        if ($cell->getColumn() == 'D') {
                            $service->setWebsite($cellValue);
                        }

                        if ($cell->getColumn() == 'E') {
                            $service->setEmail($cellValue);
                        }

                        if ($cell->getColumn() == 'F') {
                            $service->setPhone($cellValue);
                        }

                        if ($cell->getColumn() == 'G') {
                            $location->setLat($cellValue);
                        }

                        if ($cell->getColumn() == 'H') {
                            $location->setLng($cellValue);
                        }
                    }
                }

            $service->setLocation($location);

            if ($service->getDisplayName() != '') {
                $manager->persist($service);
            }
        }

        $manager->flush();

    }
}
