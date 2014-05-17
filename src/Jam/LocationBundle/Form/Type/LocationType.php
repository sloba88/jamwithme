<?php

namespace Jam\LocationBundle\Form\Type;
 
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * LocationType
 */
class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('address', 'text', array(
                                'required' => true,
                                'label' => false,
                                'attr' => array(
                                                'class' => 'form-control',
                                                'autocomplete' => 'off',
                                                'data-lat' => 0,
                                                'data-lng' => 0,
                                                'data-country' => false,
                                                )
                                ))
                ->add('neighborhood', 'hidden', array(
                    'required'      => false,
                    ))
                ->add('locality', 'hidden', array(
                    'required'      => false,
                    ))
                ->add('country', 'hidden', array(
                    'required'      => false
                    ))
                ->add('lat', 'hidden', array(
                    'required'      => false
                    ))
                ->add('lng', 'hidden', array(
                    'required'      => false
                    ));
        
    }
 
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'   => 'Jam\LocationBundle\Entity\Location'
            ))
        ;
    }
 
    public function getName()
    {
        return 'location';
    }
}