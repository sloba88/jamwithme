<?php

namespace Jam\MessageBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Jam\MessageBundle\Document\Conversation;
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
        $dm = $this->get('doctrine_mongodb')->getManager();

        $message = new Message();

        $form = $this->createFormBuilder($message)
            ->add('message', 'textarea')
            ->add('send', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $inboxRepository = $dm->getRepository('JamMessageBundle:Inbox');

            $myInbox    = $inboxRepository->findOneByUser($me->getId());
            $userInbox  = $inboxRepository->findOneByUser($user->getId());

            $message->setFrom($me->getId());
            $message->setTo($user->getId());
            $message->setMessage($data->getMessage());


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

            return $this->redirect($this->generateUrl('send_message', array('username' => $user->getUsername())));
        }

        return array('form' => $form->createView());
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
                $messages = array_merge($messages, $c->getMessages()->toArray());
            }
        }

        return array('messages' => $messages);
    }
}
