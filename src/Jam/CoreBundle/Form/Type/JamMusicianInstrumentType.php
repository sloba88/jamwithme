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
            'choice_label' => 'username',
            'attr' => array(
                'data-placeholder' => 'Find user by name or email'
            )
        ))

        ->add('instrument', EntityType::class, array(
            'class' => 'Jam\CoreBundle\Entity\Instrument',
            'choice_label' => 'name',
            'attr' => array(
                'data-placeholder' => 'What is he/she playing?'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'jam' => null
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'class' => 'Jam\CoreBundle\Entity\JamMusicianInstrument',
            'jam' => null
        ));
    }


    public function getName()
    {
        return 'jam_musician_instrument_type';
    }
}