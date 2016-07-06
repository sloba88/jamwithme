<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvitationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'Email*',
                'required' => true
            ))

            ->add('firstName', 'text', array(
                'required' => false
            ))

            ->add('lastName', 'text', array(
                'required' => false
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\UserBundle\Entity\Invitation',
        ));
    }

    public function getName()
    {
        return 'invitation_type';
    }
}