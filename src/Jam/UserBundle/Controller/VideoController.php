<?php

namespace Jam\UserBundle\Controller;

use Jam\CoreBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class VideoController extends Controller
{
    /**
     * @Route("/video/create", name="video_create", options={"expose"=true})
     * @Template()
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request_stack')->getCurrentRequest();
        $url = $request->get('url');

        if (!$url){
            //throw exception
        }

        $video = new Video();
        $video->setUrl($url);

        if ($video) {
            $em->persist($video);
            $em->flush();
        }

        return new JsonResponse(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.video.added.successfully'),
            'url' => $video->getUrl(),
            'id' => $video->getId()
        ));
    }

    /**
     * @Route("/video/remove/{id}", name="video_remove", options={"expose"=true})
     * @Template()
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $video = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Video')
            ->findOneBy(array('id' => $id, 'creator' => $user));

        if ($video) {
            $em->remove($video);
            $em->flush();
        }

        return new JsonResponse(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.video.removed.successfully')
        ));
    }
}
