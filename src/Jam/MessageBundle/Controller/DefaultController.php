<?php

namespace Jam\MessageBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Jam\MessageBundle\Document\Conversation;
use Jam\MessageBundle\Document\Inbox;
use Jam\MessageBundle\Document\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        if (!$user) throw $this->createNotFoundException('This user does not exist');

        $me = $this->container->get('security.context')->getToken()->getUser();
        $dm = $this->get('doctrine_mongodb')->getManager();

        if ($me->getId() == $user->getId()) throw $this->createNotFoundException("You can't message yourself.");

        $inboxRepository = $dm->getRepository('JamMessageBundle:Inbox');

        $myInbox    = $inboxRepository->findOneByUser($me->getId());
        $userInbox  = $inboxRepository->findOneByUser($user->getId());

        $message = new Message();
        $message->setFrom($me->getId());
        $message->setTo($user->getId());
        $message->setMessage($this->get('request')->request->get('message'));

        if (!$myInbox){
            $myInbox = new Inbox();
            $myInbox->setUser($me->getId());
        }

        $myConversation = $dm->createQueryBuilder('JamMessageBundle:Inbox')
            ->field('user')->equals($me->getId())
            ->field('conversations.user')->equals($user->getId())
            ->getQuery()
            ->getSingleResult();

        if(!$myConversation){
            $myConversation = new Conversation();
            $myConversation->setUser($user->getId());
            $myInbox->addConversation($myConversation);
        }

        foreach($myInbox->getConversations() AS $c){
            $userId = is_object($c->getUser()) == true ? $c->getUser()->getId(): $c->getUser();
            $c->setUser($userId);
            if ($userId == $user->getId()){
                $c->setUser($user->getId());
                $c->addMessage($message);
            }
        }

        if (!$userInbox){
            $userInbox = new Inbox();
            $userInbox->setUser($user->getId());
        }

        $userConversation = $dm->createQueryBuilder('JamMessageBundle:Inbox')
            ->field('user')->equals($user->getId())
            ->field('conversations.user')->equals($me->getId())
            ->getQuery()
            ->getSingleResult();

        if (!$userConversation){
            $userConversation = new Conversation();
            $userConversation->setUser($me->getId());
            $userInbox->addConversation($userConversation);
        }

        foreach($userInbox->getConversations() AS $c){
            $userId = is_object($c->getUser()) == true ? $c->getUser()->getId(): $c->getUser();
            $c->setUser($userId);
            if ($userId == $me->getId()){
                $c->setUser($me->getId());
                $c->addMessage($message);
            }
        }

        $dm->persist($myInbox);
        $dm->persist($userInbox);
        $dm->flush();

        $response = array();
        $response['status'] = 'success';

        return new JsonResponse($response);
    }

    /**
     * @Route("/messages/", name="inbox")
     * @Template()
     */
    public function conversationsAction()
    {
        $me = $this->container->get('security.context')->getToken()->getUser();

        $repository = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('JamMessageBundle:Inbox');

        $inbox = $repository->findOneByUser($me->getId());

        if ($inbox){
            $conversations = $inbox->getConversations();
        }else{
            $conversations = array ();
        }

        //var_dump($messages);

        return array('conversations' => $conversations);
    }

    /**
     * @Route("/messages/{username}", name="messages")
     * @Template()
     */
    public function messagesAction($username)
    {
        $me = $this->container->get('security.context')->getToken()->getUser();
        $messages = array ();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $inbox = $this->get('doctrine_mongodb')
            ->getManager()
            ->createQueryBuilder('JamMessageBundle:Inbox')
            ->field('conversations.user')->equals($user->getId())
            ->getQuery()
            ->execute();

        $inbox = $inbox->getNext();

        if ($inbox){
            $conv = $inbox->getConversations();

            foreach($conv AS $c){
                if ($user->getId() == $c->getUser()->getId()){
                    $messages = array_merge($messages, $c->getMessages()->toArray());
                }

            }
        }

        return array('messages' => $messages);
    }
}
