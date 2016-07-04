<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\JamInstrumentTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamInstrumentType extends AbstractType
{
    protected $instrumentTransform;

    public function __construct(JamInstrumentTransform $instrumentTransform)
    {
        $this->instrumentTransform = $instrumentTransform;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->instrumentTransform);

        $this->instrumentTransform->setMydata($builder->getData());

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Jam\CoreBundle\Entity\JamInstrument'
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'jam_instrument_type';
    }
}