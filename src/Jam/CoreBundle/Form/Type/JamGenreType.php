<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\JamGenreTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class JamGenreType extends AbstractType
{
    protected $genreTransformer;

    public function __construct(JamGenreTransform $genreTransformer)
    {
        $this->genreTransformer = $genreTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->genreTransformer);

        $this->genreTransformer->setMydata($builder->getData());

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Jam\CoreBundle\Entity\JamGenre'
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'jam_genre_type';
    }
}