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

    public function unserialize_php($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
}
