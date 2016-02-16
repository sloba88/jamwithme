<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;

class MessagesController extends FOSRestController
{
    /**
     * When triggered sends an email as a notification to user; Params are fetched via GET params
     * http://33.33.33.100/app_dev.php/send-message-email/?messageType=yo&time=1421020800&userId=86 <---- example url
     *
     * userId (integer)
     * messageType (string)
     * time (timestamp)
     *
     * @Post("/send-message-email", name="sendMessageEmail")
     * @RequestParam(name="userId", requirements="\d+", description="user id to send the message to", strict=true, nullable=false)
     * @RequestParam(name="messageType", requirements="[a-z-A-Z]+", description="message type to send to user", strict=true, nullable=false)
     * @RequestParam(name="messageText", description="message text short", strict=true, nullable=false)
     * @RequestParam(name="time", requirements="\d+", description="time of the message", strict=true, nullable=false)
     */
    public function sendMessageEmailAction(ParamFetcher $paramFetcher)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $user = $this->getDoctrine()->getRepository('JamUserBundle:User')->find($paramFetcher->get('userId'));

        if ($user instanceof UserInterface) {

            $messageBody = $this->render('JamWebBundle:Email:'. $paramFetcher->get('messageType') .'.html.twig', array(
                'time' => (new \DateTime())->setTimestamp($paramFetcher->get('time')),
                'text' => $paramFetcher->get('messageText')
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

        } else {
            throw new \Exception('User not found');
        }

        $response = new JsonResponse();
        $response->setData(array(
            'success' => true,
        ));

        return $response;
    }
}
