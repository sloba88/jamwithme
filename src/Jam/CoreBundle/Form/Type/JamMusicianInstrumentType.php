<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\JamInstrumentTransform;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamMusicianInstrumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('musician', EntityType::class, array(
            'class' => 'Jam\UserBundle\Entity\User',
            'choice_label' => 'username'
        ));

        $builder->add('instrument', EntityType::class, array(
            'class' => 'Jam\CoreBundle\Entity\Instrument',
            'choice_label' => 'name'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
        ));
    }


    public function getName()
    {
        return 'jam_musician_instrument_type';
    }
}