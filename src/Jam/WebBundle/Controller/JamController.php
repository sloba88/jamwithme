<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Jam;
use Jam\CoreBundle\Entity\JamMusicianInstrument;
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
            ->findBy(array(), array('id' => 'DESC'));

        return array('jams' => $jams);
    }

    /**
     * @Route("/start-jam", name="start_jam")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $jam = new Jam();

        $jamMember = new JamMusicianInstrument();
        $jamMember->setJam($jam);
        $jamMember->setMusician($this->getUser());

        $jam->addMember($jamMember);

        $form = $this->createForm(JamType::class, $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.created.successfully'));

            return $this->redirect($this->generateUrl('jams'));
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

        if (!$jam) {
            throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));
        }

        $form = $this->createForm(new JamType(), $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            foreach($jam->getJamMembers() as $j){
                $j->setJam($jam);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.updated.successfully'));

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

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

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

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $musician = $this->container->get('security.context')->getToken()->getUser();
            if ($musician->isJamMember($jam)) {
                throw $this->createNotFoundException($this->get('translator')->trans('excpetion.you.are.already.in.the.jam'));
            } else {

                if ($musician->isJamMemberRequested($jam)) {
                    $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans('message.request.already.sent'));
                    return $this->redirect($this->generateUrl('view_jam', array('slug' => $slug)));
                }

                $jam->addMemberRequest($musician);

                $em = $this->getDoctrine()->getManager();
                $em->persist($jam);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.request.sent.successfully'));
            }
        } else {
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
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

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            if ($musician->isJamMember($jam)) {
                throw $this->createNotFoundException($this->get('translator')->trans('excpetion.you.are.already.in.the.jam'));
            } else {

                $jam->removeMemberRequest($musician);
                $jam->addMember($musician);

                $em = $this->getDoctrine()->getManager();
                $em->persist($jam);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.request.accepted.successfully'));
            }
        } else {
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
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

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        }else{
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        if(!$user->isJamMember($jam)){
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.are.not.this.jam.member.'));
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
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug));

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

        if(!$user->isJamMember($jam)){
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.are.not.this.jam.member.'));
        }

        $jamImage = $this->getDoctrine()
            ->getRepository('JamCoreBundle:JamImage')
            ->find($id);

        if(!$jamImage){
            throw $this->createNotFoundException($this->get('translator')->trans('exception.image.not.found'));
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
