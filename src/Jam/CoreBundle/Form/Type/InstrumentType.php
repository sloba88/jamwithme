<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Jam\CoreBundle\Form\DataTransformer\InstrumentTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class InstrumentType extends AbstractType
{
    protected $instrumentTransform;

    protected $securityContext;

    public function __construct(InstrumentTransform $instrumentTransform,SecurityContext $securityContext)
    {
        $this->instrumentTransform = $instrumentTransform;
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('instrument', 'entity', array(
                'class' => 'JamCoreBundle:Instrument',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u');
                    },
                'property' => "name",
                'multiple' => false,
                'required' => false,
                'attr' => array(
                    'class'=> 'instrument-select'
                ),
                'label' => 'What do you play?'
            ))
                ->addModelTransformer($this->instrumentTransform)
        );

        $builder->add('skillLevel', 'choice', array(
            'choices'   => array(
                '1'   => 'Beginner',
                '2'   => 'Average',
                '3'   => 'Advanced',
                '4'   => 'Semi-Professional',
                '5'   => 'Professional'
            ),
            'label' => 'How good are you?'
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $user = $this->securityContext->getToken()->getUser();
            $musicianInstrument = $event->getForm()->getData();
            $musicianInstrument->setMusician($user);
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\MusicianInstrument',
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'instrument_type';
    }
}