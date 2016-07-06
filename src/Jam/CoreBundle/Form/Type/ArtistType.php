<?php

namespace Jam\CoreBundle\Form\Type;

use Jam\CoreBundle\Form\DataTransformer\ArtistTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ArtistType extends AbstractType
{
    protected $artistTransformer;

    public function __construct(ArtistTransform $artistTransformer)
    {
        $this->artistTransformer = $artistTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->artistTransformer, true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Jam\CoreBundle\Entity\Artist',
            'required' => false
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getName()
    {
        return 'artist_type';
    }
}