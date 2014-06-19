<?php

namespace Jam\MessageBundle\Controller;

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

        $message = new Message();

        $form = $this->createFormBuilder($message)
            ->add('message', 'textarea')
            ->add('send', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            $inboxRepository = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('JamMessageBundle:Inbox');

            $conversationRepository = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('JamMessageBundle:Conversation');

            $myInbox    = $inboxRepository->findOneByUser($me->getId());
            $userInbox  = $inboxRepository->findOneByUser($user->getId());

            $myConversation   = $conversationRepository->findOneByUser($user->getId());
            $userConversation = $conversationRepository->findOneByUser($me->getId());

            if (!$myInbox){
                $myInbox = new Inbox();
                $myInbox->setUser($me->getId());
            }

            if (!$userInbox){
                $userInbox = new Inbox();
                $userInbox->setUser($user->getId());
            }

            if (!$myConversation){
                $myConversation = new Conversation();
                $myConversation->setUser($user->getId());
                $myInbox->addConversation($myConversation);
            }

            if (!$userConversation){
                $userConversation = new Conversation();
                $userConversation->setUser($me->getId());
                $userInbox->addConversation($userConversation);
            }

            $message->setFrom($me->getId());
            $message->setTo($user->getId());
            $message->setMessage($data->getMessage());

            $myConversation->addMessage($message);
            $userConversation->addMessage($message);

            $dm = $this->get('doctrine_mongodb')->getManager();
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
