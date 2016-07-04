<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\JamGenreTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JamStatusType extends AbstractType
{
    private $jamStatusChoices;

    public function __construct(array $jamStatusChoices)
    {
        $this->jamStatusChoices = $jamStatusChoices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->jamStatusChoices,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}