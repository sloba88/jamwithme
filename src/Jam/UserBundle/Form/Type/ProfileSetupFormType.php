<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class ProfileSetupFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_type',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true
        ));

        $builder->add('genres', 'genre_type', array(
            'required' => false
        ));


        $builder->add('artists', 'artist_type');

        $builder->add('location', new LocationType());

    }

    public function getName()
    {
        return 'jam_user_profile_setup';
    }
}