<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\Artist;
use Jam\CoreBundle\Services\JamStages;
use Jam\CoreBundle\Services\JamStatuses;
use Jam\CoreBundle\Services\JamTypes;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class JamType extends AbstractType
{
    private $jamStageChoices;

    private $jamTypeChoices;

    private $jamStatusChoices;

    private $em;


    public function __construct(EntityManager $em, JamTypes $jamTypeChoices, JamStages $jamStageChoices, JamStatuses $jamStatusChoices)
    {
        $this->jamStageChoices = $jamStageChoices;

        $this->jamTypeChoices = $jamTypeChoices;

        $this->jamStatusChoices = $jamStatusChoices;

        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jam = $builder->getData();

        $instruments = $jam->getInstruments();

        $artists = $jam->getArtists();

        $builder
            ->add('name', 'text', array(
                'label' => 'label.jam.name'
            ))
            ->add('description', null, array(
                'label' => 'label.jam.description',
                'attr' => array(
                    'rows' => 4
                )
            ))
            ->add('stage', 'choice', array(
                'label' => 'label.jam.stage',
                'choices' => $this->jamStageChoices->getChoices()
            ))
            ->add('type', 'choice', array(
                'label' => 'label.jam.type',
                'choices' => $this->jamTypeChoices->getChoices()
            ))
            ->add('status', 'choice', array(
                'label' => 'label.jam.status',
                'choices' => $this->jamStatusChoices->getChoices()
            ))
            ->add('location', LocationType::class, array(
                'data' => $jam->getLocation()
            ))
            ->add('genres', EntityType::class, array(
                'required' => false,
                'label' => 'label.jam.genres',
                'multiple' => true,
                'class' => 'Jam\CoreBundle\Entity\Genre',
                'choice_label' => 'name'
            ))
            ->add('instruments', 'jam_instrument_type', array(
                'required' => true,
                'mapped' => false,
                'label' => 'label.jam.looking.for',
                'multiple' => true,
                'expanded' => false,
                'allow_extra_fields' => true,
                'jam' => $jam,
                'property' => 'name',
                'data' => $instruments
            ))
            ->add('members', CollectionType::class, array(
                'type' => 'jam_musician_instrument_type',
                'required' => true,
                'label' => 'Looking for',
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'options' => array(
                    'jam' => $jam
                )
            ))
            ->add('artists', EntityType::class, array(
                'label' => 'label.jam.sounds.like',
                'class' => 'Jam\CoreBundle\Entity\Artist',
                'multiple' => true,
                'choice_value' => 'name',
                'data' => $artists,
                'choices' => $artists,
                'property' => 'name',
                'required' => false
            ))

            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data) {
                    return;
                }

                foreach($data['artists'] AS $d) {
                    $artist = $this->em
                        ->getRepository('JamCoreBundle:Artist')
                        ->findOneBy(array('name' => $d));

                    if (null === $artist){
                        $artist = new Artist();
                        $artist->setName($d);

                        $this->em->persist($artist);
                        $this->em->flush();
                    }
                }

                $form->remove('artists');
                $form->add('artists', EntityType::class, array(
                    'label' => 'label.sounds.like',
                    'multiple' => true,
                    'class' => 'Jam\CoreBundle\Entity\Artist',
                    'choice_value' => 'name',
                    'property' => 'name',
                    'required' => false
                ));
            })

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