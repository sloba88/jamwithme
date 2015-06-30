<?php

namespace Jam\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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

        $tokenData = $soundcloudService->getSoundcloudToken($currentRequest->get('code'));

        $securityContext = $this->container->get('security.context');

        $soundcloudUserData = $soundcloudService->getSoundcloudUser($tokenData->access_token);

        $em = $this->getDoctrine()->getManager();

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //connect user
            $user = $this->get('security.context')->getToken()->getUser();
            $user->setSoundcloudAccessToken($tokenData->access_token);
            $user->setSoundcloudId($soundcloudUserData->id);

            $em->flush();

            $redirectUrl = $this->generateUrl('musician_profile', array('username' => $user->getUsername()));
        } else {
            // log in user

            $user = $soundcloudService->setNewSoundcloudUser($soundcloudUserData, $tokenData);
            $em->persist($user);
            $em->flush();

            $loginToken = new UsernamePasswordToken($user, $user->getPlainPassword(), "public", $user->getRoles());
            $securityContext->setToken($loginToken);

            $event = new InteractiveLoginEvent($currentRequest, $loginToken);
            $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);

            $redirectUrl = $this->generateUrl('home');
        }

        return new RedirectResponse($redirectUrl);
    }

}