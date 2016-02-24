<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\GearTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GearType extends AbstractType
{
    protected $gearTransform;

    public function __construct(GearTransform $gearTransform)
    {
        $this->gearTransform = $gearTransform;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->gearTransform);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Jam\CoreBundle\Entity\MusicianGear',
            'required' => false,
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'gear_type';
    }
}