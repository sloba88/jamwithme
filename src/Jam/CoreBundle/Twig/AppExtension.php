<?php

namespace Jam\CoreBundle\Twig;

use Jam\CoreBundle\Services\JamStages;
use Jam\CoreBundle\Services\JamStatuses;
use Jam\CoreBundle\Services\JamTypes;

class AppExtension extends \Twig_Extension
{
    private $jamTypes;
    private $jamStatuses;
    private $jamStages;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jam_type_to_string', array($this, 'jamTypeToString')),
            new \Twig_SimpleFilter('jam_status_to_string', array($this, 'jamStatusToString')),
            new \Twig_SimpleFilter('jam_stage_to_string', array($this, 'jamStageToString')),
        );
    }

    public function setJamTypes(JamTypes $jamTypes)
    {
        $this->jamTypes = $jamTypes->getChoices();
    }

    public function jamTypeToString($index)
    {
        return $this->jamTypes[$index];
    }

    public function setJamStatuses(JamStatuses $jamStatuses)
    {
        $this->jamStatuses = $jamStatuses->getChoices();
    }

    public function jamStatusToString($index)
    {
        return $this->jamStatuses[$index];
    }

    public function setJamStages(JamStages $jamStages)
    {
        $this->jamStages = $jamStages->getChoices();
    }

    public function jamStageToString($index)
    {
        return $this->jamStages[$index];
    }

    public function getName()
    {
        return 'app_extension';
    }
}