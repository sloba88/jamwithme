<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Jam\CoreBundle\Form\Type\JamMemberType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('genres', 'entity', array(
                'class' => 'JamCoreBundle:Genre',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u');
                    },
                'property' => "name",
                'multiple' => true
            ))
            ->add('search', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\Search',
        ));
    }

    public function getName()
    {
        return 'search';
    }
}