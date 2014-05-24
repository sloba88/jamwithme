<?php

namespace Jam\WebBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\Jam;
use Jam\CoreBundle\Form\Type\GenreType;
use Jam\CoreBundle\Form\Type\JamType;
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

        $form = $this->createForm(new JamType());

        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
                $creator = $this->container->get('security.context')->getToken()->getUser();
                $jam->setCreator($creator);
            } else {
                throw $this->createNotFoundException('This user does not exist');
            }

            $jam->addJamMember($creator);

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Jam created successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/jam/edit/{name}", name="edit_jam")
     * @Template()
     */
    public function editAction($name)
    {
        $musician = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request_stack')->getCurrentRequest();

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('name' => $name, 'creator' => $musician));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        $form = $this->createForm(new JamType(), $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Jam updated successfully.');

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

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        return array('jam' => $jam);
    }

    /**
     * @Route("/jam/{name}/join", name="join_jam_request")
     * @Template()
     */
    public function joinJamAction($name)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('name' => $name));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $musician = $this->container->get('security.context')->getToken()->getUser();
            if ($musician->isJamMember($jam)) {
                throw $this->createNotFoundException('You are already in the jam');
            } else {

                if ($musician->isJamMemberRequested($jam)) {
                    $this->get('session')->getFlashBag()->set('error', 'Request already sent.');
                    return $this->redirect($this->generateUrl('view_jam', array('name' => $name, 'id' => $jam->getId())));
                }

                $jam->addMemberRequest($musician);

                $em = $this->getDoctrine()->getManager();
                $em->persist($jam);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', 'Jam request sent successfully.');
            }
        } else {
            throw $this->createNotFoundException('You shall not pass');
        }

        return $this->redirect($this->generateUrl('view_jam', array('name' => $name, 'id' => $jam->getId())));
    }

    /**
     * @Route("/jam/{name}/accept/{user_id}", name="jam_accept")
     * @Template()
     */
    public function jamAcceptAction($name, $user_id)
    {
        $musician = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->find($user_id);

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('name' => $name));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            if ($musician->isJamMember($jam)) {
                throw $this->createNotFoundException('You are already in the jam');
            } else {

                $jam->removeMemberRequest($musician);
                $jam->addMember($musician);

                $em = $this->getDoctrine()->getManager();
                $em->persist($jam);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', 'Jam request accepted successfully.');
            }
        } else {
            throw $this->createNotFoundException('You shall not pass');
        }

        return $this->redirect($this->generateUrl('view_jam', array('name' => $name, 'id' => $jam->getId())));
    }
}
