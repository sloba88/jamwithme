<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Instrument;
use Jam\CoreBundle\Entity\InstrumentCategory;
use Jam\CoreBundle\Entity\InstrumentTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInstrumentsData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($this->container->get('kernel')->getRootDir() . '/../data/instruments.xlsx');

        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);

        foreach ($sheet->getColumnIterator() as $column) {
            $instrumentCategory = new InstrumentCategory();
            $k = 0;
            $language = 'en';

            $cellIterator = $column->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell) && $cell->getValue() != '') {
                    $k++;

                    if ($language != 'en') {
                        continue;
                    }

                    if ($k==1) {
                        //its language
                        $language = $cell->getValue();
                    } else if ($k==2) {
                        //its category
                        $instrumentCategory->setName(trim($cell->getValue()));
                    } else {
                        $instrument = new Instrument();
                        $instrument->setName(trim($cell->getValue()));
                        $instrument->setCategory($instrumentCategory);

                        $cIndex = $column->getColumnIndex();
                        $fi = $sheet->getCell(++$cIndex . $k);
                        $instrument->addTranslation(new InstrumentTranslation('fi', 'name', $fi->getValue()));

                        $manager->persist($instrument);
                    }
                }
                if ($instrumentCategory->getname()) {
                    $manager->persist($instrumentCategory);
                }
            }
        }

        $manager->flush();
    }
}