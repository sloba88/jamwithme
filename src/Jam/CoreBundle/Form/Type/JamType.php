<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamType extends AbstractType
{
    private $jamStatusChoices;

    private $jamTypeChoices;

    public function __construct(array $jamStatusChoices, array $jamTypeChoices)
    {
        $this->jamStatusChoices = $jamStatusChoices;

        $this->jamTypeChoices = $jamTypeChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jam = $builder->getData();

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
            ));

        $builder->add('genres', EntityType::class, array(
            'required' => false,
            'label' => 'Genres',
            'multiple' => true,
            'expanded' => false,
            'class' => 'Jam\CoreBundle\Entity\Genre',
            'choice_label' => 'name'
        ));

        $builder->add('instruments', 'jam_instrument_type', array(
            'required' => true,
            'mapped' => false,
            'label' => 'Looking for',
            'multiple' => true,
            'expanded' => false,
            'allow_extra_fields' => true,
            'jam' => $jam,
            'property' => 'name'
        ));

        $builder->add('members', CollectionType::class, array(
            'type' => 'jam_musician_instrument_type',
            'required' => true,
            'label' => 'Looking for',
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'options' => array(
                'jam' => $jam
            )
        ));

        $builder->add('artists', 'artist_type', array(
            'label' => 'Sounds like'
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