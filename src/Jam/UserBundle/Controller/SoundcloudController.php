<?php

namespace Jam\UserBundle\Controller;


use Jam\CoreBundle\Entity\SoundcloudTrack;
use Jam\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SoundcloudController extends Controller {

    /**
     * @Route("/soundcloud/connect", name="connect_soundcloud")
     */
    public function soundcloudConnectAction()
    {
        $soundcloudService = $this->get('soundcloud_connector');
        $connectUrl = $soundcloudService->getSouncloudConnectUrl();
        $redirectResponse = new RedirectResponse($connectUrl);
        return $redirectResponse;
    }

    /**
     * @Route("/soundcloud/token", name="soundcloud_token")
     */
    public function recieveSouncloudCode()
    {
        $currentRequest = $this->container->get('request_stack')->getCurrentRequest();
        $soundcloudService = $this->get('soundcloud_connector');
        $securityContext = $this->container->get('security.context');
        $em = $this->getDoctrine()->getManager();

        if ($currentRequest->get('error')!='') {
            $this->get('session')->getFlashBag()->set('info', $currentRequest->get('error_description'));
            return new RedirectResponse($this->generateUrl('home'));
        }

        $tokenData = $soundcloudService->getSoundcloudToken($currentRequest->get('code'));
        $soundcloudUserData = $soundcloudService->getSoundcloudUser($tokenData->access_token);

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //connect user if already has jamifind account and logged in to the app
            $user = $this->get('security.context')->getToken()->getUser();
            $user->setSoundcloudAccessToken($tokenData->access_token);
            $user->setSoundcloudId($soundcloudUserData->id);

            $em->flush();
            $redirectUrl = $this->generateUrl('musician_profile', array('username' => $user->getUsername()));
        } else {
            // log in user

            $user = $this->getDoctrine()->getRepository('JamUserBundle:User')->findOneBy(array('soundcloud_id' => $soundcloudUserData->id));

            if ($user instanceof User === false) {
                $user = $soundcloudService->setNewSoundcloudUser($soundcloudUserData, $tokenData);
            }

            $loginToken = new UsernamePasswordToken($user, $user->getPlainPassword(), "public", $user->getRoles());
            $securityContext->setToken($loginToken);

            $event = new InteractiveLoginEvent($currentRequest, $loginToken);
            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

            $redirectUrl = $this->generateUrl('home');
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @Route("/soundcloud-track/create", name="soundcloud_track_create", options={"expose"=true})
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

        $sc_track = new SoundcloudTrack();
        $sc_track->setUrl($url);

        $em->persist($sc_track);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.sc_track.added.successfully'),
            'url' => $sc_track->getUrl(),
            'id' => $sc_track->getId()
        ));
    }

    /**
     * @Route("/oundcloud-track/remove/{id}", name="soundcloud_track_remove", options={"expose"=true})
     * @Template()
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $sc_track = $this->getDoctrine()
            ->getRepository('JamCoreBundle:SoundcloudTrack')
            ->findOneBy(array('id' => $id, 'creator' => $user));

        if ($sc_track) {
            $em->remove($sc_track);
            $em->flush();
        }

        return new JsonResponse(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.sc_track.removed.successfully')
        ));
    }

}