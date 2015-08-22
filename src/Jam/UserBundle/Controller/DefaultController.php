<?php

namespace Jam\UserBundle\Controller;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Jam\UserBundle\Entity\User;
use Jam\UserBundle\Entity\UserImage;
use Jam\UserBundle\Form\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

        $soundcloudService = $this->get('soundcloud_connector');
        $tracks = $soundcloudService->getUserTracks($user);

        return array(
            'user' => $user,
            'userTracks' => json_encode($tracks)
        );
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

        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        return $this->redirect($cacheManager->getBrowserPath($user->getAvatar(), $size));
    }

    /**
     * @Route("/default-avatar/{size}", name="default_avatar", options={"expose"=true})
     * @Template()
     */
    public function defaultAvatarAction($size = 'my_thumb')
    {
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        return $this->redirect($cacheManager->getBrowserPath('assets/images/placeholder-user.jpg', $size));
    }

    /**
     * @Route("/users", name="users_find")
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $q = $request->query->get('q');

        if (!$q){
            throw $this->createNotFoundException('You shall not pass');
        }

        $users = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT user.id, user.username, user.avatar FROM JamUserBundle:User user WHERE user.username LIKE :q OR user.firstName LIKE :q OR user.lastName LIKE :q ')
            ->setParameter('q', '%'.$q.'%')
            ->setMaxResults(8)
            ->getResult();

        $response = new JsonResponse();
        $response->setData($users);

        return $response;
    }

    /**
     * @Route("/user/image/add/", name="upload_user_image", options={"expose"=true})
     * @Template()
     */
    public function uploadImageAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        if ($user->getImages()->count() > 19) {
            //images limit reached
            return false;
        }

        $file = $request->files->get('file');

        if ($file=='') throw $this->createNotFoundException('File not sent');

        $userImage = new UserImage();
        $userImage->setFile($file);
        $user->addImage($userImage);

        $this->resizeImage($userImage, $request->request->all());

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
                'deleteType' => 'DELETE',
                'id' => $userImage->getId()
            )
        ));

        return $response;
    }

    /**
     * @Route("/user/image/remove/{id}", name="remove_user_image", options={"expose"=true})
     * @Template()
     */
    public function removeImageAction($id)
    {
        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }else{
            throw $this->createNotFoundException('You shall not pass');
        }

        $userImage = $this->getDoctrine()
            ->getRepository('JamUserBundle:UserImage')
            ->find($id);

        if (!$userImage) throw $this->createNotFoundException('There is no image with that id');

        $userImage->setUser(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userImage);
        $em->persist($user);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success',
            'message' => 'Image removed successfully.'
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
            if ($image->getSize()->getWidth() > $image->getSize()->getHeight()){
                $userImage->setType(3);
            }else{
                $userImage->setType(4);
            }
        }

        $image->save($userImage->getAbsolutePath());

        return $image;
    }

    /**
     * @Route("/user/set/avatar/{id}", name="set_avatar", options={"expose"=true})
     * @Template()
     */
    public function setAvatarAction($id)
    {
        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
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
            'status' => 'success',
            'message' => 'Avatar changed successfully.'
        ));

        return $response;
    }

    /**
     * @Route("reset-email", name="reset_email")
     */
    public function resetEmailAction()
    {
        $user = new User();
        $form = $this->createForm(new EmailType(), $user);

        $request = $this->get('request_stack')->getCurrentRequest();

        if ($request->getMethod() === 'POST') {

            $email = $request->get('email');

            $isUnique = $this->getDoctrine()->getRepository('JamUserBundle:User')->isEmailUnique($email);

            if ($isUnique && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $user->setEmail($email);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $redirect = new RedirectResponse($this->generateUrl('home'));
                return $redirect;
            }
        }

        return $this->render('JamUserBundle:Profile:reset_email.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
