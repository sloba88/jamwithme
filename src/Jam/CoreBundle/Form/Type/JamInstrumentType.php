<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\JamInstrumentTransform;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamInstrumentType extends AbstractType
{
    protected $instrumentTransform;
    protected $jam;

    public function __construct(JamInstrumentTransform $instrumentTransform)
    {
        $this->instrumentTransform = $instrumentTransform;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->instrumentTransform);

        $this->instrumentTransform->setMydata($options['jam']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Jam\CoreBundle\Entity\Instrument',
            'compound' => false,
            'jam' => null
        ));
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function getName()
    {
        return 'jam_instrument_type';
    }
}