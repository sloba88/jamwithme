<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Search;
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
        $form = $request->query->all();

        $search = new Search();

        $search->setIsSubscribed(true);
        $search->setCreator($this->getUser());

        if ($form['isTeacher'] == 'on'){
            $search->setIsTeacher(TRUE);
        } else{
            $search->setIsTeacher(FALSE);
        }

        if (isset($form['distance'])){
            $search->setDistance($form['distance']);
        }

        if (isset($form['genres'])){
            $search->setGenres(json_encode($form['genres']));
        }

        if (isset($form['instruments'])){
            $search->setInstruments(json_encode($form['instruments']));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($search);
        $em->flush();
        $response = new Response( json_encode(array('status' => 'success')));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
