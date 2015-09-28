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
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /**
         * @var ShoutCounter
         */
        $shoutCounter = $this->get('shout.counter');
        $shout = new Shout();

        $form = $this->createFormBuilder($shout)
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

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$this->getUser()){
                throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
            }

            $shout->setCreator($this->getUser());
            $em->persist($shout);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.you.have.shouted.successfully.'));

            return $this->redirect($this->generateUrl('home'));
        }

        return array(
            'form' => $form->createView(),
            'shoutCounter' => $shoutCounter
        );
    }

    /**
     * @Route("/teachers", name="teachers", options={"expose"=true})
     * @Template()
     */
    public function teachersAction(Request $request)
    {
        /**
         * @var ShoutCounter
         */
        $shoutCounter = $this->get('shout.counter');
        $shout = new Shout();

        $form = $this->createFormBuilder($shout)
            ->add('text', 'textarea', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'label.say.something.cool',
                    'maxlength' => 250
                )
            ))
            ->add('send', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$this->getUser()){
                throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
            }

            $shout->setCreator($this->getUser());
            $em->persist($shout);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.you.have.shouted.successfully.'));

            return $this->redirect($this->generateUrl('home'));
        }

        return array(
            'form' => $form->createView(),
            'shoutCounter' => $shoutCounter
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
