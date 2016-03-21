<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Jam\CoreBundle\Entity\Instrument;
use Jam\CoreBundle\Entity\InstrumentCategory;
use Jam\CoreBundle\Entity\InstrumentTranslation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160321100555 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql("TRUNCATE TABLE instruments");
        $this->addSql("TRUNCATE TABLE instrument_categories");
    }

    public function postUp(Schema $schema)
    {
        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject($this->container->get('kernel')->getRootDir() . '/../data/instruments.xlsx');
        $manager = $this->container->get('doctrine.orm.entity_manager');

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
                        $instrumentCategory->setName($cell->getValue());
                    } else {
                        $instrument = new Instrument();
                        $instrument->setName($cell->getValue());
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
