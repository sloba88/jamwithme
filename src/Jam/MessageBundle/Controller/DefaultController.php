<?php

namespace Jam\MessageBundle\Controller;

use Jam\MessageBundle\Document\Inbox;
use Jam\MessageBundle\Document\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/message/send/{username}", name="send_message")
     * @Template()
     */
    public function sendAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);
        $me = $this->container->get('security.context')->getToken()->getUser();
        $request = $this->get('request_stack')->getCurrentRequest();

        $message = new Message();

        $form = $this->createFormBuilder($message)
            ->add('message', 'textarea')
            ->add('send', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $repository = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('JamMessageBundle:Inbox');

            $myInbox = $repository->findOneByUser($me->getId());

            if ($myInbox){

            }else{
                $myInbox = new Inbox();
                $myInbox->setUser($me->getId());
            }

            $message->setFrom($me->getId());
            $message->setTo($user->getId());
            $message->setMessage($data->getMessage());

            $myInbox->addMessage($message);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($myInbox);
            $dm->flush();

            return $this->redirect($this->generateUrl('send_message', array('username' => $user->getUsername())));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/messages/", name="messages")
     * @Template()
     */
    public function myAction()
    {
        $me = $this->container->get('security.context')->getToken()->getUser();

        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('JamMessageBundle:Inbox');

        $messages = $repository->findOneByUser($me->getId())->getMessages();

        //var_dump($messages);

        return array('messages' => $messages);
    }
}
