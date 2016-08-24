<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\Artist;
use Jam\CoreBundle\Entity\MusicianGenre;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProfileSetupFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_type',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true
        ));

        $builder->add('genres', EntityType::class, array(
            'required' => false,
            'class' => 'Jam\CoreBundle\Entity\MusicianGenre',
            'label' => 'label.jam.genres',
            'multiple' => true,
            'choice_value' => 'genre.id',
            'property' => 'genre.name',
            'choices' => array(),
        ))->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (!isset($data['genres'])) {
                return;
            }

            $newData = array();
            foreach($data['genres'] AS $d) {
                $genre = $this->em
                    ->getRepository('JamCoreBundle:Genre')
                    ->findOneBy(array('id' => $d));


                if (null !== $genre){
                    $mg = new MusicianGenre();
                    $mg->setGenre($genre);
                    array_push($newData, $mg);
                    $this->em->persist($mg);
                }
            }

            $form->remove('genres');
            $form->add('genres', EntityType::class, array(
                'class' => 'Jam\CoreBundle\Entity\MusicianGenre',
                'multiple' => true,
                'required' => false,
                'choice_value' => 'genre.id',
                'data' => $newData,
                'property' => 'genre.name'
            ));

        });

        $builder->add('artists', EntityType::class, array(
            'label' => 'label.influences',
            'class' => 'Jam\CoreBundle\Entity\Artist',
            'multiple' => true,
            'choice_value' => 'name',
            'property' => 'name',
            'required' => false
        ))->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$data || !array_key_exists('artists', $data)) {
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
                'label' => 'label.influences',
                'multiple' => true,
                'class' => 'Jam\CoreBundle\Entity\Artist',
                'choice_value' => 'name',
                'property' => 'name',
                'required' => false
            ));
        });
        $builder->add('location', new LocationType());

    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function getName()
    {
        return 'jam_user_profile_setup';
    }
}