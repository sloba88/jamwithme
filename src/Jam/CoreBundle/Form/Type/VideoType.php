<?php
namespace Jam\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class VideoType extends AbstractType
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'text', array(
            'attr' => array(
                'placeholder' => 'URL'
            ),
            'label' => false
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $user = $this->securityContext->getToken()->getUser();
            $musicianVideo = $event->getForm()->getData();
            $musicianVideo->setCreator($user);
            $musicianVideo->setUrl($musicianVideo->getUrl());
            /*
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $musicianVideo->getUrl(), $match)) {
                $video_id = $match[1];
                $musicianVideo->setUrl('//www.youtube.com/embed/'.$video_id);
            }
            */

        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jam\CoreBundle\Entity\Video',
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'video_type';
    }
}