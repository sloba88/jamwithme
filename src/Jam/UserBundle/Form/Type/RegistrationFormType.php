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

        $builder->add('email', 'email', array(
            'attr' => array(
                'placeholder' => 'label.email'
            )
        ));
        $builder->add('username', 'text', array(
            'attr' => array(
                'placeholder' => 'label.username'
            )
        ));
        $builder->add('plainPassword', 'password', array(
            'attr' => array(
                'placeholder' => 'label.password',
                'pattern' => ".{8,}",
                'title' => 'Minimum 8 letters please',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'data-trigger' => 'focus'
            )
        ));
        $builder->add('isVisitor', 'checkbox', array(
            'required' => false,
            'label' => 'label.learn.an.instrument'
        ));
        $builder->add('isJammer', 'checkbox', array(
            'required' => false,
            'label' => 'label.jam',
            'data' => true
        ));
        $builder->add('isTeacher', 'checkbox', array(
            'required' => false,
            'label' => 'label.teach.music'
        ));

        $builder->add('locale', 'hidden', array(
            'required' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "Jam\UserBundle\Entity\User"
        ));
    }

    public function getName()
    {
        return 'jam_user_registration';
    }
}