<?php

namespace Jam\UserBundle\Controller;

use Proxies\__CG__\Jam\UserBundle\Entity\UserImage;
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
        //store to Mongo or Redis maybe to fetch it faster?

        if (strpos($user->getAvatar(),'http') !== false) {
            return $this->redirect($user->getAvatar());
        }else{
            return $this->redirect($this->get('liip_imagine.cache.manager')->getBrowserPath($user->getAvatar(), 'my_thumb'));
        }

    }

    /**
     * @Route("/user/image/add/", name="upload_user_image")
     * @Template()
     */
    public function uploadImageAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        $file = $request->files->get('file');

        $userImage = new UserImage();
        $userImage->setFile($file);
        $user->addImage($userImage);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'files' => array(
                'url' => $userImage->getWebPath(),
                'thumbnailUrl' => $userImage->getWebPath(),
                'name' => $userImage->getPath(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getClientSize(),
                'deleteUrl' => '',
                'deleteType' => 'DELETE'
            )
        ));

        return $response;
    }
}
