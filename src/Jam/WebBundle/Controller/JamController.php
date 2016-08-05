<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Jam;
use Jam\CoreBundle\Entity\JamInterest;
use Jam\CoreBundle\Entity\JamMusicianInstrument;
use Jam\CoreBundle\Form\Type\JamType;
use Jam\LocationBundle\Entity\Location;
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

        return array(
            'jams' => $jams,
            'headline' => 'Jams'
        );
    }

    /**
     * @Route("/my-jams", name="my_jams")
     * @Template("JamWebBundle:Jam:index.html.twig")
     */
    public function myJamsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT j
                FROM JamCoreBundle:Jam j
                JOIN j.members m
                JOIN m.musician u
                WHERE u = :me
                ORDER BY j.createdAt ASC'
        )->setParameter('me', $this->getUser());

        return array(
            'jams' => $query->getResult(),
            'headline' => 'My Jams'
        );
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
        $this->getUser()->getInstruments()->first() ? $jamMember->setInstrument($this->getUser()->getInstruments()->first()->getInstrument()) : false;
        $jam->addMember($jamMember);

        //pre-set user location
        $userLocation = $this->getUser()->getLocation();
        if ($userLocation) {
            $location = new Location();
            $location->setCountry($userLocation->getCountry());
            $location->setAdministrativeAreaLevel3($userLocation->getAdministrativeAreaLevel3());
            $location->setAddress($userLocation->getAdministrativeAreaLevel3() . ', ' . $userLocation->getCountry());
            $jam->setLocation($location);
        }

        $form = $this->createForm(JamType::class, $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach($jam->getMembers() AS $member) {
                $member->setJam($jam);
            }

            //assign videos
            if ($request->get('video')) {
                foreach($request->get('video') AS $v) {
                    $video = $em->find('JamCoreBundle:Video', $v);
                    $video->setJam($jam);
                }
            }

            $em->persist($jam);
            $em->flush();

            //send invites to members here!

            foreach($jam->getMembers() AS $member) {
                if ($member->getInvitee()) {
                    /* @var $invitation \Jam\UserBundle\Entity\Invitation */
                    $invitation = $member->getInvitee();

                    $messageBody = $this->renderView('JamWebBundle:Email:jamInvitation.html.twig', array(
                        'from' => $this->getUser(),
                        'invitation' => $invitation,
                        'jam' => $jam,
                        'member' => $member
                    ));

                    $message = \Swift_Message::newInstance()
                        ->setSubject("You have been invited to join Jamifind")
                        ->setFrom('noreply@jamifind.com')
                        ->setTo($invitation->getEmail())
                        ->setBody($messageBody, 'text/html');

                    if ($this->get('mailer')->send($message)) {
                        $invitation->setSent(true);
                    }
                }
            }

            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.created.successfully'));

            return $this->redirect($this->generateUrl('jams'));
        }

        return array(
            'form' => $form->createView(),
            'headline' => 'Create a new Jam'
        );
    }

    /**
     * @Route("/jam/edit/{slug}", name="edit_jam")
     * @Template("JamWebBundle:Jam:create.html.twig")
     */
    public function editAction($slug)
    {
        $me = $this->getUser();
        $request = $this->get('request_stack')->getCurrentRequest();

        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findOneBy(array('slug' => $slug, 'creator' => $me));

        if (!$jam) {
            throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));
        }

        $form = $this->createForm(JamType::class, $jam);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($jam);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('message.jam.updated.successfully'));

            return $this->redirect($this->generateUrl('jams'));
        }

        return array(
            'form' => $form->createView(),
            'headline' => 'Edit a Jam'
        );
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

    /**
     * @Route("/jam/{id}/add-to-interest", name="jam_add_interest", options={"expose"=true})
     * @Template()
     */
    public function addInterestAction($id)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->find($id);

        if (!$jam) throw $this->createNotFoundException($this->get('translator')->trans('exception.the.jam.does.not.exist'));

        $interest = new JamInterest();
        $interest->setJam($jam);

        $em = $this->getDoctrine()->getManager();
        $em->persist($interest);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success',
            'message' => 'Jam added to interests successfully'
        ));

        return $response;
    }
}
