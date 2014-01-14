<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Jam;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class JamController extends Controller
{
    /**
     * @Route("/start-jam", name="start_jam")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $jam = new Jam();

        $form = $this->createFormBuilder($jam)
            ->add('name', 'text')
            ->add('members_count', 'text')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
                $creator = $this->container->get('security.context')->getToken()->getUser();
                $jam->setCreator($creator);
            }else{
                throw $this->createNotFoundException('This user does not exist');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Jam created successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array('form' => $form->createView());
    }
}
