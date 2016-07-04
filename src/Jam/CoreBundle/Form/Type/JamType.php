<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Jam\LocationBundle\Form\Type\LocationType;
use Symfony\Component\Form\AbstractType;
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
                'attr' => array('placeholder' => 'In which phase are you in?')
            ))

            ->add('location', new LocationType());

        $builder->add('genres', 'jam_genre_type', array(
            'required' => false,
            'label' => 'Genres',
            'empty_value' => false,
            'data' => $jam
        ));

        $builder->add('instruments', 'jam_instrument_type', array(
            'label' => 'What are you missing?',
            'required' => true,
            'data' => $jam
        ));

        $builder->add('artists', 'artist_type', array(
            'label' => 'Sounds like',
            'empty_value' => false
        ));

        $builder->add('musicians', 'collection', array(
                'type' => new JamMusicianType(),
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