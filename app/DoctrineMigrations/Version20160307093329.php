<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\GenreCategory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160307093329 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("SET FOREIGN_KEY_CHECKS=0;");
        $this->addSql("TRUNCATE TABLE genres");
        $this->addSql("TRUNCATE TABLE genre_categories");
    }

    public function postUp(Schema $schema)
    {
        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($this->container->get('kernel')->getRootDir() . '/../data/genres.xls');
        $manager = $this->container->get('doctrine.orm.entity_manager');

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
                        $genreCategory->setName($cell->getValue());
                    }

                    $genre = new Genre();
                    print($cell->getValue());
                    $genre->setName($cell->getValue());
                    $genre->setCategory($genreCategory);
                    $manager->persist($genre);
                }
                if ($genreCategory->getname()) {
                    $manager->persist($genreCategory);
                }
            }
        }

        $manager->flush();

        $this->addSql("SET FOREIGN_KEY_CHECKS=1;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
