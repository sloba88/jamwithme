<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\GenreCategory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadGenresData implements FixtureInterface, ContainerAwareInterface
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

        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($this->container->get('kernel')->getRootDir() . '/../data/genres.xls');

        //  Get worksheet dimensions
        $sheet = $phpExcelObject->getSheet(0);

        foreach ($sheet->getColumnIterator() as $column) {
            //echo 'Row number: ' . $column->getColumnIndex() . "\r\n";

            $genreCategory = new GenreCategory();
            $k = 0;

            $cellIterator = $column->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell) && $cell->getValue() != '') {
                    //echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
                    $k++;

                    if ($k==1) {
                        $genreCategory->setName(trim($cell->getValue()));
                    }

                    $genre = new Genre();
                    $genre->setName(trim($cell->getValue()));
                    $genre->setCategory($genreCategory);
                    $manager->persist($genre);

                }
                if ($genreCategory->getname()) {
                    $manager->persist($genreCategory);
                }
            }
        }

        $manager->flush();
    }
}