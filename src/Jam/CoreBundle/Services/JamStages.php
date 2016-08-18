<?php

namespace Jam\CoreBundle\Services;


use Jam\CoreBundle\Entity\Shout;
use Symfony\Component\Translation\TranslatorInterface;

class JamStages {

    private $choices;

    private $translator;

    public function  __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->choices = array (
            1 => $this->translator->trans("label.jam.stage.idea"),
            2 => $this->translator->trans("label.jam.stage.idea.with.material"),
            3 => $this->translator->trans("label.jam.stage.established.band")
        );
    }

    public function getChoices()
    {
        return $this->choices;
    }
}