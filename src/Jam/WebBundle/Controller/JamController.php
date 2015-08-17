<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Jam;
use Jam\CoreBundle\Entity\JamImage;
use Jam\CoreBundle\Entity\JamMember;
use Jam\CoreBundle\Form\Type\JamType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $form = $this->createForm(new JamType(), $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
                $creator = $this->container->get('security.context')->getToken()->getUser();
                $jam->setCreator($creator);
            } else {
                throw $this->createNotFoundException('This user does not exist');
            }

            $jamMember = new JamMember();
            $jamMember->setJam($jam);
            $jamMember->setMember($creator);

            $jam->addJamMember($jamMember);

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Jam created successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/jam/edit/{slug}", name="edit_jam")
     * @Template()
     */
    public function editAction($slug)
    {
        $musician = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request_stack')->getCurrentRequest();

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug, 'creator' => $musician));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        $form = $this->createForm(new JamType(), $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            foreach($jam->getJamMembers() as $j){
                $j->setJam($jam);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', 'Jam updated successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/jam/{slug}", name="view_jam")
     * @Template()
     */
    public function viewAction($slug)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        return array('jam' => $jam);
    }

    /**
     * @Route("/jam/{slug}/join", name="join_jam_request")
     * @Template()
     */
    public function joinJamAction($slug)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $musician = $this->container->get('security.context')->getToken()->getUser();
            if ($musician->isJamMember($jam)) {
                throw $this->createNotFoundException('You are already in the jam');
            } else {

                if ($musician->isJamMemberRequested($jam)) {
                    $this->get('session')->getFlashBag()->set('error', 'Request already sent.');
                    return $this->redirect($this->generateUrl('view_jam', array('slug' => $slug)));
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

        return $this->redirect($this->generateUrl('view_jam', array('slug' => $slug)));
    }

    /**
     * @Route("/jam/{slug}/accept/{user_id}", name="jam_accept")
     * @Template()
     */
    public function jamAcceptAction($slug, $user_id)
    {
        $musician = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->find($user_id);

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

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

        return $this->redirect($this->generateUrl('view_jam', array('slug' => $slug)));
    }

    /**
     * @Route("/jam/image/add/{slug}", name="upload_jam_image")
     * @Template()
     */
    public function uploadImageAction($slug)
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        if(!$user->isJamMember($jam)){
            throw $this->createNotFoundException('You are not this jam member.');
        }

        $file = $request->files->get('file');

        $jamImage = new JamImage();
        $jamImage->setFile($file);
        $jam->addImage($jamImage);

        $em = $this->getDoctrine()->getManager();
        $em->persist($jam);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'files' => array(
                'url' => $jamImage->getWebPath(),
                'thumbnailUrl' => $jamImage->getWebPath(),
                'name' => $jamImage->getPath(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getClientSize(),
                'deleteUrl' => '',
                'deleteType' => 'DELETE'
            )
        ));

        return $response;
    }

    /**
     * @Route("/jam/{slug}/image/remove/{id}", name="remove_image")
     * @Template()
     */
    public function removeImageAction($slug, $id)
    {
        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

        if (!$jam) throw $this->createNotFoundException('The jam does not exist');

        if(!$user->isJamMember($jam)){
            throw $this->createNotFoundException('You are not this jam member.');
        }

        $jamImage = $this->getDoctrine()
            ->getRepository('JamCoreBundle:JamImage')
            ->find($id);

        if(!$jamImage){
            throw $this->createNotFoundException('Image not found.');
        }

        $jam->removeImage($jamImage);

        $em = $this->getDoctrine()->getManager();
        $em->persist($jam);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success'
        ));

        return $response;
    }
}
