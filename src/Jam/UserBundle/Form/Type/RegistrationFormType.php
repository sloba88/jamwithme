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
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'fos_user.password.mismatch',
            'first_options' => array(
                'attr' => array(
                    'placeholder' => 'label.password'
                )
            ),
            'second_options' => array(
                'attr' => array(
                    'placeholder' => 'label.repeat.password'
                )
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

        $builder->add('acceptedTerms', 'checkbox', array(
            'required' => true,
            'label' => 'label.terms.checkbox'
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