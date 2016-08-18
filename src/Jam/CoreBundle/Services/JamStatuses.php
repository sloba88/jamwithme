<?php

namespace Jam\CoreBundle\Services;

use Symfony\Component\Translation\TranslatorInterface;

class JamStatuses {

    private $choices;

    private $translator;

    public function  __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->choices = array (
            1 => $this->translator->trans("label.jam.status.active"),
            2 => $this->translator->trans("label.jam.status.obsolite"),
            3 => $this->translator->trans("label.jam.status.success")
        );
    }

    public function getChoices()
    {
        return $this->choices;
    }
}