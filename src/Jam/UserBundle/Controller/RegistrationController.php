<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jam\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use JMS\TranslationBundle\Annotation\Desc;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends ContainerAware
{
    public function registerAction(Request $request)
    {
        if (true === $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse('/');
        }

        $newInviteCode = $this->container->get('request_stack')->getMasterRequest()->query->get('c');
        $inviteCode = $this->container->get('request_stack')->getMasterRequest()->getSession()->get('inviteCode');

        if (!$inviteCode && $newInviteCode) {
            //user came through invite link, save it to session, he can't alter it later
            $this->container->get('request_stack')->getMasterRequest()->getSession()->set('inviteCode', $newInviteCode);
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                //check for invite code
                $inviteCode = $this->container->get('request_stack')->getMasterRequest()->getSession()->get('inviteCode');

                if ($inviteCode) {
                    //find invitation
                    $em = $this->container->get('doctrine')->getEntityManager();
                    $invitation = $em->getRepository('JamUserBundle:Invitation')->findOneBy(array('code' => $inviteCode, 'accepted' => false));

                    if ($invitation) {
                        $user->setInvitedBy($invitation->getCreator());
                        $invitation->setAccepted(true);
                        $em->persist($invitation);
                        $em->flush();
                    }
                }

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                // send email notification
                //TODO: put this in event

                $message = \Swift_Message::newInstance()
                    ->setSubject('New unconfirmed user joined Jamifind!')
                    ->setFrom('noreply@jamifind.com')
                    ->setTo(array('info@jamifind.com', 'stanic.slobodan88@gmail.com'))
                    ->setBody('New unconfirmed user joined: '.$user->getEmail());

                $this->container->get('mailer')->send($message);

                //send ga data
                /* send data to GA */
                $data = array(
                    'uid'=> $user->getId(),
                    'ec'=> 'authentication',
                    'ea'=> 'unconfirmed registered'
                );
                $this->container->get('happyr.google.analytics.tracker')->send($data, 'event');

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            /** @Desc("The user with %email% does not exist") */
            $msg = $this->container->get('translator')->transChoice('exception.nonexistent.user', $email);
            throw new NotFoundHttpException($msg);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            /** @Desc("The user with confirmation token %token% does not exist") */
            $msg = $this->container->get('translator')->transChoice('exception.user.missing.token', $token);
            throw new NotFoundHttpException($msg);
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException($this->container->get('translator')->trans('exception.this.user.does.not.have.access.to.this.section'));
        }

        $this->container->get('session')->getFlashBag()->set('success', $this->container->get('translator')->trans('message.your.account.is.activated.'));

        return new RedirectResponse($this->container->get('router')->generate('fos_user_setup'));
    }
}
