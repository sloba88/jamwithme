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
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/m/{username}", name="musician_profile")
     * @Template("JamUserBundle:Profile:show.html.twig")
     */
    public function indexAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user){
            $this->container->get('session')->getFlashBag()->set('info', $this->get('translator')->trans('exception.user.not.found'));
            return new RedirectResponse($this->generateUrl('home'));
        }

        $soundcloudService = $this->get('soundcloud_connector');
        $tracks = $soundcloudService->getUserTracks($user);

        return array(
            'user' => $user,
            'userTracks' => $tracks,
            'page' => array(
                'title' => $user->getDisplayName() . ' | ' . $user->getDisplayLocation(). ' - '. $this->get('translator')->trans('text.title'),
                'description' => $user->getMainInstrumentAsCSV() || $user->getGenresAsCSV() ? $user->getMainInstrumentAsCSV() . ' | ' . $user->getGenresAsCSV() : false
            )
        );
    }

    /**
     * @Route("/m/{username}/avatar/{size}", name="musician_avatar", options={"expose"=true})
     * @Template()
     */
    public function avatarAction($username, $size = 'my_thumb')
    {
        $userManager = $this->get('fos_user.user_manager');

        if (is_numeric($username)) {
            //its id
            $user = $userManager->findUserBy(array('id'=>$username));
        } else {
            $user = $userManager->findUserByUsername($username);
        }

        $imagine = $this->container->get('liip_imagine.controller');

        if (is_null($user)) {
            return $imagine
                ->filterAction(
                    $this->get('request_stack')->getCurrentRequest(),         // http request
                    'uploads/placeholder-user.png',      // original image you want to apply a filter to
                    $size             // filter defined in config.yml
                );
        }

        /** @var RedirectResponse */
        return $imagine
            ->filterAction(
                $this->get('request_stack')->getCurrentRequest(),         // http request
                $user->getAvatar(),      // original image you want to apply a filter to
                $size             // filter defined in config.yml
            );
    }

    /**
     * @Route("/default-avatar/{size}", name="default_avatar", options={"expose"=true})
     * @Template()
     */
    public function defaultAvatarAction($size = 'my_thumb')
    {
        $imagine = $this->container->get('liip_imagine.controller');

        /** @var RedirectResponse */
        return $imagine
            ->filterAction(
                $this->get('request_stack')->getCurrentRequest(),         // http request
                'uploads/placeholder-user.png',      // original image you want to apply a filter to
                $size             // filter defined in config.yml
            );

    }

    /**
     * @Route("/users", name="users_find", options={"expose"=true})
     * @Template()
     */
    public function findAction()
    {
        //todo: move this to api
        $request = $this->get('request_stack')->getCurrentRequest();
        $q = $request->query->get('q');

        if (!$q){
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        $finder = $this->container->get('fos_elastica.finder.searches.user');
        $results = $finder->find($q . '*');

        $data = array();
        foreach ($results AS $k=>$r) {
            /** @var $r User */
            $data[$k]['id'] = $r->getId();
            $data[$k]['username'] = $r->getUsername();
            $data[$k]['avatar'] = $r->getAvatar();
            $data[$k]['fullName'] = $r->getFullName();
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/user/image/add/", name="upload_user_image", options={"expose"=true})
     * @Template()
     */
    public function uploadImageAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $response = new JsonResponse();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }else{
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        if ($user->getImages()->count() > 20) {
            //images limit reached
            $response->setData(array(
                'success' => false,
                'message' => 'Image limit reached'
            ));
            $response->setStatusCode(500);
            return $response;
        }

        $file = $request->files->get('file');

        if ($file=='') throw $this->createNotFoundException($this->get('translator')->trans('exception.file.not.sent'));

        $userImage = new UserImage();
        $userImage->setFile($file);
        $userImage->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($userImage);
        $em->flush();

        $userImage = $this->resizeImage($userImage, $request->request->all());
        $em->flush();

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'photo',
            'ea'=> 'added'
        );
        $this->get('happyr.google.analytics.tracker')->send($data, 'event');

        $response->setData(array(
            'files' => array(
                'thumbnailUrl' => $this->get('liip_imagine.cache.manager')->getBrowserPath($userImage->getWebPath(), 'my_medium_'.$userImage->getType()),
                'url' => '/' . $userImage->getWebPath(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getClientSize(),
                'setAvatarUrl' => $this->generateUrl('set_avatar', array('id' => $userImage->getId())),
                'deleteType' => 'DELETE',
                'id' => $userImage->getId(),
                'imageType' => $userImage->getType()
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
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }else{
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        $userImage = $this->getDoctrine()
            ->getRepository('JamUserBundle:UserImage')
            ->findOneBy(array(
                'user' => $this->getUser(),
                'id' => $id
            ));

        if (!$userImage) throw $this->createNotFoundException($this->get('translator')->trans('exception.there.is.no.image.with.that.id'));

        $em = $this->getDoctrine()->getManager();
        $em->remove($userImage);
        $em->persist($user);
        $em->flush();

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'photo',
            'ea'=> 'removed'
        );
        $this->get('happyr.google.analytics.tracker')->send($data, 'event');

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.image.removed.successfully')
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

        if (isset($dimensions['w']) && $dimensions['w'][0]!=''){
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

        return $userImage;
    }

    /**
     * @Route("/user/set/avatar/{id}", name="set_avatar", options={"expose"=true})
     * @Template()
     */
    public function setAvatarAction($id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }else{
            throw $this->createNotFoundException($this->get('translator')->trans('exception.you.shall.not.pass'));
        }

        $image = $this->getDoctrine()
            ->getRepository('JamUserBundle:UserImage')
            ->findOneBy(array(
                'user' => $this->getUser(),
                'id' => $id
            ));

        if ($image) {
            $user->setAvatar($image->getFilename());

            $fs = new Filesystem();
            $fs->copy($image->getAbsolutePath(), 'uploads/avatars/'.$image->getFilename());
        }


        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        /* send data to GA */
        $data = array(
            'uid'=> $user->getId(),
            'ec'=> 'profile',
            'ea'=> 'avatar set'
        );
        $this->get('happyr.google.analytics.tracker')->send($data, 'event');

        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success',
            'message' => $this->get('translator')->trans('message.avatar.changed.successfully.')
        ));

        return $response;
    }

    /**
     * @Route("reset-email", name="reset_email")
     */
    public function resetEmailAction()
    {
        //currently forbid changing of emails and enable this only when user doesn't have valid email set for some reason

        if (filter_var($this->getUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $redirect = new RedirectResponse($this->generateUrl('home'));
            return $redirect;
        }

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

                $logger = $this->get('logger');
                $logger->info('User '.$user->getUsername() . ' changed email to '. $email);

                $this->container->get('session')->getFlashBag()->set('success', 'Email changed successfully. ');

                $redirect = new RedirectResponse($this->generateUrl('home'));
                return $redirect;
            } else {
                $form->addError(new FormError('This email is already taken by another user.'));
            }
        }

        return $this->render('JamUserBundle:Profile:reset_email.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("search-test-cron", name="search_test_cron")
     */
    public function searchCronTestAction()
    {
        $searchSubscriber = $this->get('search.subscriber.cron');

        $emailsSent = $searchSubscriber->execute();

        return new JsonResponse(array('success' => true, 'emails_sent' => $emailsSent));
    }
}
