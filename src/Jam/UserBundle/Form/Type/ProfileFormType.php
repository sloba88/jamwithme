<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Form\DataTransformer\InstrumentTransform;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Jam\CoreBundle\Form\Type\InstrumentType;


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
        $builder->add('username');
        $builder->add('aboutMe', 'textarea', array(
            'required' => false
        ));

        $builder->add('isTeacher', 'checkbox', array(
            'label' => 'I am music teacher and I provide private lesions',
            'required' => false
        ));

        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_type',
            'allow_add' => true,
        ));

        $builder->add('genres', 'entity', array(
            'class' => 'JamCoreBundle:Genre',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u');
            },
            'property' => "name",
            'multiple' => true,
            'required' => false
        ));

        $builder->add('brands', 'entity', array(
            'class' => 'JamCoreBundle:Brand',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u');
                },
            'property' => "name",
            'multiple' => true,
            'required' => false
        ));

        $builder->add('artists', 'artist_type');

        $builder->add('location', new LocationType());

        $builder->add('gender', 'choice', array(
            'choices'   => array(
            '1' => 'Male',
            '2' => 'Female',
        ),
            'expanded' => true,
            'empty_data'  => 0,
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
            'allow_add' => true,
            'by_reference' => false,
            'label' => false,
            'allow_delete' => true
        ));

    }

    public function getName()
    {
        return 'jam_user_profile';
    }
}