<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\MusicianInstrument;
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

    public function __construct(InstrumentTransform $instrumentTransform, SecurityContext $securityContext)
    {
        $this->instrumentTransform = $instrumentTransform;
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('instrument', 'hidden', array(
                'attr' => array(
                    'class'=> 'instrument-select'
                )
            ))->addModelTransformer($this->instrumentTransform)
        );

        $builder->add('skillLevel', 'hidden', array(
            'attr' => array(
                'class'=> 'skill-select'
            )
        ));

        $builder->add('wouldLearn', 'checkbox', array(
            'attr' => array(
                'class'=> 'would-learn'
            )
        ));

        $builder->add('musician', 'hidden');

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var MusicianInstrument $musicianInstrument */
            $musicianInstrument = $event->getData();
            $user = $this->securityContext->getToken()->getUser();

            if ($musicianInstrument->getInstrument() != null) {
                $musicianInstrument->setMusician($user);

                $event->setData($musicianInstrument);
            }else {
                $event->setData(null);
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\MusicianInstrument',
            'required' => false
        ));
    }

    public function getName()
    {
        return 'instrument_type';
    }
}