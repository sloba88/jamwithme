<?php

namespace Jam\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/m/{username}", name="musician_profile")
     * @Template()
     */
    public function indexAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        return array('user' => $user);
    }

    /**
     * @Route("/m/{username}/avatar", name="musician_avatar")
     * @Template()
     */
    public function avatarAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        //make logic to check if it is external image here!

        if (strpos($user->getAvatar(),'http') !== false) {
            return $this->redirect($user->getAvatar());
        }else{
            return $this->redirect($this->get('liip_imagine.cache.manager')->getBrowserPath($user->getAvatar(), 'my_thumb'));
        }

    }
}
