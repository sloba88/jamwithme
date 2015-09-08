<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstName', 'text', array(
            'required' => false
        ));

        $builder->add('lastName', 'text', array(
            'required' => false
        ));

        $builder->add('email', 'email', array(
            'disabled' => true
        ));

        $builder->add('username', null, array(
            'label' => 'Username *'
        ));

        $builder->add('aboutMe', 'textarea', array(
            'required' => false
        ));

        $builder->add('education', 'textarea', array(
            'required' => false
        ));

        $builder->add('hourlyRate', 'text', array(
            'required' => false,
            'label' => 'Hourly rate &euro;'
        ));

        $builder->add('isVisitor', 'checkbox', array(
            'required' => false,
            'label' => 'Learn an instrument'
        ));

        $builder->add('isJammer', 'checkbox', array(
            'required' => false,
            'label' => 'Jam'
        ));

        $builder->add('isTeacher', 'checkbox', array(
            'required' => false,
            'label' => 'Teach music'
        ));

        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_type',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true
        ));

        $builder->add('genres', 'genre_type', array(
            'required' => false
        ));

        $builder->add('brands', 'brand_type', array(
            'required' => false
        ));

        $builder->add('artists', 'artist_type');

        $builder->add('location', new LocationType());

        $builder->add('gender', 'choice', array(
            'choices'   => array(
            '' => 'Won\'t say',
            '1' => 'Male',
            '2' => 'Female',
        ),
            'expanded' => true,
            'empty_data'  => 0,
            'required' => false
        ));

        $builder->add('commitment', 'choice', array(
            'choices'   => array(
                '0' => 'Not available',
                '1' => '1-2 hours/week',
                '2' => '2-4 hours/week',
                '3' => '4-6 hours/week',
                '4' => 'More than 6 hours/week'
            ),
            'expanded' => false,
            'required' => false
        ));

        $builder->add('birthDate', 'date', array(
            'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'),
            'widget' => 'choice',
            'years' => range(1920, date('Y')),
            'required' => false
        ));

        $builder->add('images', 'collection', array(
            'type' => new ImageType(),
            'label' => false,
        ));

        $builder->add('videos', 'collection', array(
            'type' => 'video_type',
            'allow_add'    => true,
            'delete_empty' => true,
            'allow_delete' => true
        ));

    }

    public function getName()
    {
        return 'jam_user_profile';
    }
}