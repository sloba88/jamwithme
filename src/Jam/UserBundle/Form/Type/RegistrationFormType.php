<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstName');
        $builder->add('lastName');
        $builder->add('email');
        $builder->add('username');
        $builder->add('aboutMe');

        $builder->add('instruments', 'entity', array(
            'class' => 'JamCoreBundle:Instrument',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u');
            },
            'property' => "name",
            'multiple' => true
        ));

        $builder->add('genres', 'entity', array(
            'class' => 'JamCoreBundle:Genre',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u');
            },
            'property' => "name",
            'multiple' => true
        ));

//        $builder->add(
//            $builder->create('artists', 'text', array(
//                'required' => false
//            ))
//                ->addViewTransformer($this->artistTransform)
//        );

        $builder->add('artists', 'artist_type');

        $builder->add('location', new LocationType());

        $builder->add('gender', 'choice', array(
            'choices'   => array(
            '1' => 'Male',
            '2' => 'Female',
        ),
            'expanded' => true,
            'empty_data'  => 0
        ));

        $builder->add('birthDate', 'date', array(
            'empty_value' => array('year' => 'Year', 'month' => 'Month', 'day' => 'Day'),
            'widget' => 'choice',
            'years' => range(1920, date('Y'))
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
        return 'jam_user_registration';
    }
}