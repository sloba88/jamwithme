<?php

namespace Jam\UserBundle\Form\Type;

use Jam\CoreBundle\Form\Type\GenreType;
use Jam\CoreBundle\Form\Type\InstrumentType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstName');
        $builder->add('lastName');

        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_select',
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'label' => false,
        ));

        $builder->add('genres', 'collection', array(
            'type' => new GenreType(),
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'label' => false,
        ));
    }

    public function getName()
    {
        return 'jam_user_registration';
    }
}