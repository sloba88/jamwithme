<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jam\CoreBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    /**
     * @Route("/subscription/add", name="subscribe_add")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $email = $request->request->get('email');
        if (!$email) throw $this->createNotFoundException('Email required');

        $subscriber = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Subscription')
            ->findOneBy(array('email' => $email));

        if ($subscriber){
            //subscriber exist
            $response = new Response( json_encode(array('status' => 'error', 'message' => 'You have already subscribed.')));
        }else{
            $subscription = new Subscription();
            $subscription->setEmail($email);
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();

            $request->getSession()->set('email', $email);

            $response = new Response( json_encode(array('status' => 'success')));
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/subscription/notify", name="subscribe_notify")
     * @Template()
     */
    public function notifyAction(Request $request)
    {
        $email = $request->getSession()->get('email');

        if ($email){
            //send email to me
            $message = \Swift_Message::newInstance()
                ->setSubject('New subscriber on Jamifind')
                ->setFrom('info@jamifind.com')
                ->setTo('stanic.slobodan88@gmail.com')
                ->addTo('info@jamifind.com')
                ->setBody('New subscriber on jamifind.com with email: '.$email);

            $this->get('mailer')->send($message);

            //send email back to user
            $message = \Swift_Message::newInstance()
                ->setSubject('Welcome to Jamifind')
                ->setFrom('info@jamifind.com')
                ->setTo($email)
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'JamWebBundle:Email:subscribed.html.twig'
                    )
                );

            $this->get('mailer')->send($message);
            $response = new Response( json_encode(array('status' => 'success')));
        }else{
            $response = new Response( json_encode(array('status' => false)));
        }

        $request->getSession()->set('email', false);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
