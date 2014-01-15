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
     * @Route("/jams", name="jams")
     * @Template()
     */
    public function indexAction()
    {
        $jams = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findAll();

        return array('jams' => $jams);
    }

    /**
     * @Route("/start-jam", name="start_jam")
     * @Template()
     */
    public function createAction(Request $request)
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

    /**
     * @Route("/jam/{name}.{id}", name="view_jam")
     * @Template()
     */
    public function viewAction($name)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('name' => $name));

        if(!$jam) throw $this->createNotFoundException('The jam does not exist');

        return array('jam' => $jam);
    }
}
