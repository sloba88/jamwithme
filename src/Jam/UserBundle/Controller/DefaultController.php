<?php

namespace Jam\UserBundle\Controller;

use Proxies\__CG__\Jam\UserBundle\Entity\UserImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
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
     * @Route("/m/{username}/avatar/{size}", name="musician_avatar")
     * @Template()
     */
    public function avatarAction($username, $size = 'my_thumb')
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        //make logic to check if it is external image here!
        //store to Mongo or Redis maybe to fetch it faster?

        if (strpos($user->getAvatar(),'http') !== false) {
            return $this->redirect($user->getAvatar());
        }else{
            return $this->redirect($this->get('liip_imagine.cache.manager')->getBrowserPath($user->getAvatar(), $size));
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

    /**
     * @Route("/user/set/avatar/{id}", name="set_avatar")
     * @Template()
     */
    public function setAvatarAction($id)
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        $allImages = $user->getImages();

        foreach($allImages as $image){
            if ($image->getId() == $id){
                $user->setAvatar($image->getPath());
                //from $image->getAbsolutePath();
                //to

                $fs = new Filesystem();
                if (!$fs->exists('uploads/avatars/'.$user->getId())){

                    try {
                        $fs->mkdir('uploads/avatars/'.$user->getId());
                    } catch (IOException $e) {
                        echo "An error occurred while creating your directory at ".$e->getPath();
                    }
                }

                $fs->copy($image->getAbsolutePath(), 'uploads/avatars/'.$user->getId().'/'.$image->getPath());
            }
        }

        //move file also to different folder


        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success'));

        return $response;
    }
}
