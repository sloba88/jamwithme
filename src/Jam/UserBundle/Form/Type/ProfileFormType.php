<?php

namespace Jam\UserBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\Artist;
use Jam\CoreBundle\Entity\MusicianGear;
use Jam\CoreBundle\Entity\MusicianGenre;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ProfileFormType extends BaseType
{
    private $em;

    private $user;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstName', 'text', array(
            'required' => false,
            'trim' => true,
            'attr' => array(
                'maxlength' => 30
            ),
            'label' => 'label.first.name'
        ));

        $builder->add('lastName', 'text', array(
            'required' => false,
            'attr' => array(
                'maxlength' => 30
            ),
            'label' => 'label.last.name'
        ));

        $builder->add('email', 'email', array(
            'disabled' => true
        ));

        $builder->add('username', null, array(
            'label' => 'label.username',
            'attr' => array(
                'maxlength' => 30
            )
        ));

        $builder->add('aboutMe', 'textarea', array(
            'required' => false,
            'label' => 'label.about.me'
        ));

        $builder->add('education', 'textarea', array(
            'required' => false,
            'label' => 'label.education'
        ));

        $builder->add('hourlyRate', 'text', array(
            'required' => false,
            'label' => 'label.hourly.rate'
        ));

        $builder->add('isVisitor', 'checkbox', array(
            'required' => false,
            'label' => 'label.learn.an.instrument'
        ));

        $builder->add('isJammer', 'checkbox', array(
            'required' => false,
            'label' => 'label.jam'
        ));

        $builder->add('isTeacher', 'checkbox', array(
            'required' => false,
            'label' => 'label.teach.music'
        ));

        $builder->add('instruments', 'collection', array(
            'type' => 'instrument_type',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true
        ))

        ->add('genres', EntityType::class, array(
            'required' => false,
            'class' => 'Jam\CoreBundle\Entity\MusicianGenre',
            'label' => 'label.jam.genres',
            'multiple' => true,
            'choice_value' => 'genre.id',
            'property' => 'genre.name',
            'choices' => $this->user->getGenres(),
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

        $builder->add('gear', EntityType::class, array(
            'label' => 'label.what.gear.do.you.own',
            'class' => 'Jam\CoreBundle\Entity\MusicianGear',
            'multiple' => true,
            'choice_value' => 'name',
            'data' => $this->user->getGear(),
            'choices' => $this->user->getGear(),
            'property' => 'name',
            'required' => false
        ))->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$data || !isset($data['gear'])) {
                return;
            }

            foreach($data['gear'] AS $d) {
                $gear = new MusicianGear();
                $gear->setName($d);

                $this->em->persist($gear);
                $this->em->flush();
            }

            $form->remove('gear');
            $form->add('gear', EntityType::class, array(
                'label' => 'label.what.gear.do.you.own',
                'multiple' => true,
                'class' => 'Jam\CoreBundle\Entity\MusicianGear',
                'choice_value' => 'name',
                'property' => 'name',
                'required' => false
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

        $builder->add('gender', 'choice', array(
            'choices'   => array(
            '' => 'value.won\'t.say',
            '1' => 'value.male',
            '2' => 'value.female',
        ),
            'expanded' => true,
            'empty_data'  => '',
            'required' => false,
            'label' => 'label.gender'
        ));

        $builder->add('commitment', 'choice', array(
            'choices'   => array(
                '0' => 'value.not.available',
                '1' => 'value.1-2.hours/week',
                '2' => 'value.2-4.hours/week',
                '3' => 'value.4-6.hours/week',
                '4' => 'value.more.than.6.hours/week'
            ),
            'expanded' => false,
            'required' => false
        ));

        $builder->add('birthDate', 'date', array(
            'empty_value' => array('year' => 'value.year', 'month' => 'value.month', 'day' => 'value.day'),
            'widget' => 'choice',
            'years' => range(date('Y')-8, 1920),
            'required' => false,
            'label' => 'label.birth.date',
            'attr' => array(
                'class' => 'col-md-3'
            )
        ));

        $builder->add('locale', 'choice', array(
            'choices' => array(
                'en' => 'English',
                'fi' => 'Finnish',
            ),
            'label' => 'Language',
            'required' => false
        ));

    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setUserToken(TokenStorage $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function getName()
    {
        return 'jam_user_profile';
    }
}