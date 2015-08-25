<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Jam\CoreBundle\Form\DataTransformer\GenreTransform;
use Jam\CoreBundle\Form\DataTransformer\InstrumentTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchType extends AbstractType
{

    private $entityManager;

    private $genreTransformer;

    private $instrumentTransformer;

    public function __construct(EntityManager $entityManager, GenreTransform $genreTransformer, InstrumentTransform $instrumentTransformer)
    {
        $this->entityManager = $entityManager;
        $this->genreTransformer = $genreTransformer;
        $this->instrumentTransformer = $instrumentTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            $builder->create('genres', 'text', array(
                'required' => false,
                'mapped' => false,
                'attr' => array(
                    'class'=> 'filter-genres',
                    'placeholder' => 'Filter by genres'
                )
            ))->addModelTransformer($this->genreTransformer)
        );

        $builder->add(
            $builder->create('instruments', 'text', array(
                'required' => false,
                'mapped' => false,
                'attr' => array(
                    'class'=> 'filter-instruments',
                    'placeholder' => 'Filter by instruments'
                )
            ))->addModelTransformer($this->instrumentTransformer)
        );

        $builder
            ->add('isTeacher', 'checkbox', array(
                'label' => 'Only people providing lesions'
            ))
            ->add('distance', 'number', array(
                'data' => 4,
                'label' => 'Distance (km)'
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'search_form';
    }
}