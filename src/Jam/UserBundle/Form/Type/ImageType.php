<?php

namespace Jam\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
            'label' => false,
            'required' => false,
            'image_path' => 'webPath',
            'attr' => array(
                'accept' => "image/*"
            )
        ));

        $builder->add('is_default', 'radio', array(
            'required' => false,
            'label' => 'label.make.this.primary',
            'attr'  => array(
                'class' => 'make-primary-image'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Jam\UserBundle\Entity\UserImage'
            ))
        ;
    }

    public function getName()
    {
        return 'image';
    }
}
