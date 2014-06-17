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
     * @Route("/message/send/{username}")
     * @Template()
     */
    public function sendAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $me = $this->container->get('security.context')->getToken()->getUser();

        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('JamMessageBundle:Inbox');

        $myInbox = $repository->findOneByUser($me->getId());

        if ($myInbox){

        }else{
            $myInbox = new Inbox();
            $myInbox->setUser($me->getId());
        }

        $message = new Message();
        $message->setFrom($me->getId());
        $message->setTo($user->getId());
        $message->setMessage("Second message in your inbox!!!");

        $myInbox->addMessage($message);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($myInbox);
        $dm->flush();

        return array('name' => $username);
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
