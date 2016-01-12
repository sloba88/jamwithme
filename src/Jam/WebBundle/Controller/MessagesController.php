<?php

namespace Jam\WebBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;


class MessagesController extends Controller
{
    /**
     * @Route("/messages/", name="inbox")
     * @Template()
     */
    public function messagesAction()
    {
        return array();
    }

    /**
     * When triggered sends an email as a notification to user; Params are fetched via GET params
     * http://33.33.33.100/app_dev.php/send-message-email/?messageText=yo&time=1421020800&userId=86 <---- example url
     *
     * userId (integer)
     * messageText (string)
     * time (timestamp)
     *
     * @Route("/send-message-email/", name="sendMessageEmail")
     */
    public function sendMessageEmailAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $user = $this->getDoctrine()->getRepository('JamUserBundle:User')->find($request->get('userId'));

        if ($user instanceof UserInterface) {

            $messageBody = $this->renderView('JamWebBundle:Email:messageNotification.html.twig', array(
                'time' => (new \DateTime())->setTimestamp($request->get('time')),
                'text' => $request->get('messageText')
            ));

            $message = \Swift_Message::newInstance()
                ->setSubject('User suggestions')
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
