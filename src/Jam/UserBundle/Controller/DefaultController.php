<?php

namespace Jam\UserBundle\Controller;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Jam\UserBundle\Entity\UserImage;
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

        if ($file=='') throw $this->createNotFoundException('File not sent');

        $userImage = new UserImage();
        $userImage->setFile($file);
        $user->addImage($userImage);

        $image = $this->resizeImage($userImage, $request->request->all());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'files' => array(
                'url' => $this->get('liip_imagine.cache.manager')->getBrowserPath($userImage->getWebPath(), 'my_medium_'.$userImage->getType()),
                'thumbnailUrl' => $userImage->getWebPath(),
                'name' => $userImage->getPath(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getClientSize(),
                'setAvatarUrl' => $this->generateUrl('set_avatar', array('id' => $userImage->getId())),
                'deleteUrl' => '',
                'deleteType' => 'DELETE'
            )
        ));

        return $response;
    }

    private function resizeImage(UserImage $userImage, $dimensions)
    {
        $imagine = new Imagine();
        $image = $imagine->open($userImage->getAbsolutePath());

        if ($image->getSize()->getWidth() < 100 || $image->getSize()->getHeight() < 100){
            $image->resize(new Box(200, 200));
        }

        if ($image->getSize()->getWidth() > 1000){
            $image->resize($image->getSize()->widen(1000));
        }

        if ($image->getSize()->getHeight() > 800){
            $image->resize($image->getSize()->heighten(800));
        }

        $image->save($userImage->getAbsolutePath());

        if ($dimensions['w'][0]!=''){
            $point = new Point($dimensions['x1'][0], $dimensions['y1'][0]);
            $box = new Box($dimensions['w'][0], $dimensions['h'][0]);

            $image->crop($point, $box);
            $image->save($userImage->getAbsolutePath());
        }

        $squareDelimiter = round($image->getSize()->getWidth() / 4);

        //is it square or close to square?
        if (abs($image->getSize()->getWidth() - $image->getSize()->getHeight()) < $squareDelimiter){
            //it is square
            if ($image->getSize()->getWidth()<800){
                //small square
                $userImage->setType(1);
            }else{
                $userImage->setType(2);
            }
        }else{
            //it is rectangle
            $userImage->setType(3);
        }

        $image->save($userImage->getAbsolutePath());

        return $image;
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
