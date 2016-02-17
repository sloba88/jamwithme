<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\UserBundle\Model\UserInterface;
use Jam\CoreBundle\Entity\EmailNotification;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;

class MessagesController extends FOSRestController
{
    /**
     * When triggered sends an email as a notification to user; Params are fetched via POST params
     *
     * @Post("/send-message-email", name="sendMessageEmail")
     * @RequestParam(name="userId", requirements="\d+", description="user id to send the message to", strict=true, nullable=false)
     * @RequestParam(name="type", requirements="[a-z-A-Z]+", description="message type to send to user", strict=true, nullable=false)
     * @RequestParam(name="text", description="message text short", strict=true, nullable=false)
     * @RequestParam(name="time", requirements="\d+", description="time of the message", strict=true, nullable=false)
     */
    public function sendMessageEmailAction(ParamFetcher $paramFetcher)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $user = $this->getDoctrine()->getRepository('JamUserBundle:User')->find($paramFetcher->get('userId'));
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();

        if ($user instanceof UserInterface) {

            $emailNotification = $em->getRepository('JamCoreBundle:EmailNotification')
                ->findOneBy(array(
                    'type' => $paramFetcher->get('type'),
                    'reciever' => $paramFetcher->get('userId')
                ), array('id' => 'DESC'));

            if ($emailNotification) {

                //spam protection one hour
                $today = new \DateTime('now');
                $diff = $today->getTimestamp() - $emailNotification->getSentAt()->getTimestamp();

                if ($diff / 60 / 60  > 1) {
                    $this->sendEmailAndCreateNotification($user, $paramFetcher);
                } else {
                    //do nothing
                    $response->setData(array(
                        'success' => true,
                        'message' => 'Email already sent recently, spamming protection.',
                    ));

                    return $response;
                }

            }else {
                $this->sendEmailAndCreateNotification($user, $paramFetcher);
            }

        } else {
            throw new \Exception('User not found');
        }

        $response->setData(array(
            'success' => true,
        ));

        return $response;
    }

    private function sendEmailAndCreateNotification($user, $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();

        $emailNotification = new EmailNotification();
        $emailNotification->setType($paramFetcher->get('type'));
        $emailNotification->setReciever($user);

        $em->persist($emailNotification);
        $em->flush();

        $messageBody = $this->renderView('JamWebBundle:Email:'. $paramFetcher->get('type') .'.html.twig', array(
            'time' => (new \DateTime())->setTimestamp($paramFetcher->get('time')),
            'text' => $paramFetcher->get('text')
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject("You just got new message on Jamifind")
            ->setFrom('noreply@jamifind.com')
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');

        try {
            $this->get('mailer')->send($message);
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }
}
