<?php

namespace Jam\CoreBundle\Services;

use Symfony\Component\Translation\TranslatorInterface;

class JamTypes {

    private $choices;

    private $translator;

    public function  __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->choices = array (
            1 => $this->translator->trans("label.jam.type.band"),
            2 => $this->translator->trans("label.jam.type.jam.session"),
            3 => $this->translator->trans("label.jam.type.temp.replacement")
        );
    }

    public function getChoices()
    {
        return $this->choices;
    }
}