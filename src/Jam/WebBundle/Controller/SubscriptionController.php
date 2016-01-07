<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Search;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jam\CoreBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//TODO: move this to API bundle
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
            $response = new Response( json_encode(array(
                'status' => 'error',
                'message' => $this->get('translator')->trans('message.you.have.already.subscribed'))
            ));
        }else{
            $subscription = new Subscription();
            $subscription->setEmail($email);
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscription);
            $em->flush();
            $response = new Response( json_encode(array('status' => 'success')));
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/subscription/search/add", name="subscribe_search_add", options={"expose"=true})
     * @Template()
     */
    public function searchSaveAction(Request $request)
    {
        $search = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Search')
            ->findOneBy(array('id' => $request->getSession()->get('searchId'), 'creator' => $this->getUser()));

        if (!$search) {
            $response = new Response( json_encode(array('success' => false, 'message' => 'No such search' )));
        } else {
            $search->setIsSubscribed(true);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $response = new Response( json_encode(array('success' => true)));
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/subscription/search/remove/{id}", name="subscribe_search_remove", options={"expose"=true})
     * @Template()
     */
    public function searchUnsubscribeAction($id)
    {
        $search = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Search')
            ->findOneBy(array('id' => $id, 'creator' => $this->getUser()));

        if (!$search) {
            $response = array( 'message' => "No such subscription found. Couldn't unsubscribe.");
        } else {
            $search->setIsSubscribed(false);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $response = array( 'message' => "Unsubscribed successfully.");
        }

        return $response;
    }
}
