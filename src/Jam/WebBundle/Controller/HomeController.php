<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Shout;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $shout = new Shout();

        $form = $this->createFormBuilder($shout)
            ->add('text', 'textarea')
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $me = $this->container->get('security.context')->getToken()->getUser();

            if (!$me){
                throw $this->createNotFoundException('You shall not pass');
            }

            $shout->setCreator($me);
            $em->persist($shout);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'You have shouted successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array('form' => $form->createView());
    }
}
