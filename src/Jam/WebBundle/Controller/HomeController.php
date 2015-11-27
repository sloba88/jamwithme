<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Shout;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Jam\CoreBundle\Services\ShoutCounter;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", options={"expose"=true})
     * @Route("/teachers", name="teachers", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        //TODO: put this form in separate file
        $form = $this->createFormBuilder(new Shout())
        ->add('text', 'textarea', array(
            'label' => false,
            'attr' => array(
                'placeholder' => 'label.say.something.cool',
                'maxlength' => 250
            )
        ))
        ->add('send', 'submit', array(
            'label' => 'label.send'
        ))
        ->getForm();

        return array(
            'form' => $form->createView()
        );
    }
}
