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

        $builder->add('email');
        $builder->add('username');
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'fos_user.password.mismatch'
        ));
        $builder->add('isVisitor', 'checkbox', array(
            'required' => false,
            'label' => 'Learn an instrument'
        ));
        $builder->add('isJammer', 'checkbox', array(
            'required' => false,
            'label' => 'Jam',
            'data' => true
        ));
        $builder->add('isTeacher', 'checkbox', array(
            'required' => false,
            'label' => 'Teach music'
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