<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\UserBundle\Model\UserInterface;
use Jam\UserBundle\Entity\Invitation;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;

class InviteController extends FOSRestController
{
    /**
     * @Post("/send-invite-emails", name="send_invite_emails")
     * @RequestParam(map=true, name="emails", description="user emails to send the invitations to", nullable=false)
     */
    public function sendInviteEmailsEmailAction(ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();

        if ($this->getUser() instanceof UserInterface) {

            foreach ($paramFetcher->get('emails') AS $email) {
                $invitation = new Invitation();
                $invitation->setEmail($email);

                $messageBody = $this->renderView('JamWebBundle:Email:invitation.html.twig', array(
                    'from' => $this->getUser(),
                    'invitation' => $invitation
                ));

                $message = \Swift_Message::newInstance()
                    ->setSubject("You have been invited to join Jamifind")
                    ->setFrom('noreply@jamifind.com')
                    ->setSender('Jamifind - bringing musicians together')
                    ->setTo($email)
                    ->setBody($messageBody, 'text/html');

                if ($this->get('mailer')->send($message)) {
                    $invitation->setSent(true);
                }

                $em->persist($invitation);
            }

            $em->flush();

            $response->setData(array(
                'status' => 'success',
                'message' => 'Invitations sent successfully.',
            ));

            return $response;
        }
    }
}
