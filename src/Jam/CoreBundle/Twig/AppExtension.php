<?php

namespace Jam\CoreBundle\Twig;

class AppExtension extends \Twig_Extension
{
    private $jamTypes;

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jam_type_to_string', array($this, 'jamTypeToString')),
        );
    }

    public function setJamsType($jamTypes)
    {
        $this->jamTypes = $jamTypes;
    }

    public function jamTypeToString($type)
    {
        return $this->jamTypes[$type];
    }

    public function getName()
    {
        return 'app_extension';
    }
}