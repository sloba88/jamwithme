<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Jam\CoreBundle\Form\Type\JamMemberType;

class JamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('members_count', 'text')
            ->add('description')
            ->add('genres', 'entity', array(
                'class' => 'JamCoreBundle:Genre',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u');
                    },
                'property' => "name",
                'multiple' => true
            ))
            ->add('jamMembers', 'collection', array(
                'type' => new JamMemberType(),
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\Jam',
        ));
    }

    public function getName()
    {
        return 'jam';
    }
}