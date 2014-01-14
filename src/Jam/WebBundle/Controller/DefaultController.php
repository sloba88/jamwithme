<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jam\CoreBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/holding", name="holding")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $subscription = new Subscription();

        $form = $this->createFormBuilder($subscription)
            ->add('email', 'email')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'You have subscribed successfully.');

            return $this->redirect($this->generateUrl('holding'));
        }

        return array('form' => $form->createView());
    }
}
