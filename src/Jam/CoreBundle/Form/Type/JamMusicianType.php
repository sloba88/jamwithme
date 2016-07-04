<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamMusicianType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('musician', 'entity', array(
                'class' => 'JamUserBundle:User',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u');
                    },
                'property' => "username"
            ))
            ->add('instrument');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\JamMusician',
        ));
    }

    public function getName()
    {
        return 'jam_musician';
    }
}