<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Jam\CoreBundle\Form\DataTransformer\InstrumentTransform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstrumentType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    private $choices;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        // Build our choices array from the database
        $groups = $em->getRepository('JamCoreBundle:Instrument')->findAll();
        foreach ($groups as $group)
        {
            // choices[key] = label
            $this->choices[$group->getId()] = $group->getName();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new InstrumentTransform($this->em);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            "choices" => $this->choices,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'instrument_select';
    }
}