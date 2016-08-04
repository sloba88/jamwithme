<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\Artist;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamType extends AbstractType
{
    private $jamStatusChoices;

    private $jamTypeChoices;

    private $em;

    public function __construct(EntityManager $em, array $jamStatusChoices, array $jamTypeChoices)
    {
        $this->jamStatusChoices = $jamStatusChoices;

        $this->jamTypeChoices = $jamTypeChoices;

        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jam = $builder->getData();

        $instruments = $jam->getInstruments();

        $artists = $jam->getArtists();

        $builder
            ->add('name', 'text', array(
                'label' => 'Title'
            ))
            ->add('description', null, array(
                'attr' => array(
                    'rows' => 4
                )
            ))
            ->add('status', 'choice', array(
                'choices' => $this->jamStatusChoices,
                'attr' => array('placeholder' => 'Select Type')
            ))
            ->add('type', 'choice', array(
                'choices' => $this->jamTypeChoices,
                'attr' => array('placeholder' => 'In which phase is the project?')
            ))
            ->add('location', LocationType::class, array(
                'data' => $jam->getLocation()
            ))
            ->add('genres', EntityType::class, array(
                'required' => false,
                'label' => 'Genres',
                'multiple' => true,
                'class' => 'Jam\CoreBundle\Entity\Genre',
                'choice_label' => 'name'
            ))
            ->add('instruments', 'jam_instrument_type', array(
                'required' => true,
                'mapped' => false,
                'label' => 'Looking for',
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
                'label' => 'Sounds like',
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
                    'label' => 'Sounds like',
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