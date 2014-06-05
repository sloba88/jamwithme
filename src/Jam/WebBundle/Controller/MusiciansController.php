<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Search;
use Jam\CoreBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class MusiciansController extends Controller
{
    /**
     * @Route("/musicians", name="musicians")
     * @Template()
     */
    public function indexAction()
    {
        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->findAll();

        $form = $this->createForm(new SearchType());

        return array('musicians' => $musicians, 'form' => $form->createView());
    }

    /**
     * @Route("/musicians/find", name="musicians_find")
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $searchParams = $request->query->get('search');

        if ($searchParams){
            //$search = new Search();
            //$search->setGenres($searchParams['genres']);
            //$search->setCreator($this->container->get('security.context')->getToken()->getUser());
            //$em = $this->getDoctrine()->getManager();
            //$em->persist($search);
            //$em->flush();

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery('SELECT u FROM JamUserBundle:User u JOIN u.genres g WHERE g.id IN (:genres) ');
            $query->setParameter('genres', $searchParams['genres']);
            $musicians = $query->getResult();

        }else{

            $musicians = $this->getDoctrine()
                ->getRepository('JamUserBundle:User')
                ->findAll();
        }

        $response = new JsonResponse();
        $musicians_data = array();

        foreach($musicians AS $m){

            if ($m->getImages()->first()){
                $image = $m->getImages()->first()->getWebPath();
            } else{
                $image = '/images/placeholder-user.jpg';
            }

            array_push($musicians_data, array(
               'username' => $m->getUsername(),
               'lat' => $m->getLocation()->getLat(),
               'lng' => $m->getLocation()->getLng(),
               'image' => $this->get('liip_imagine.cache.manager')->getBrowserPath($image, 'my_thumb'),
               'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
               'me' => $this->container->get('security.context')->getToken()->getUser() == $m->getUsername() ? true : false
            ));
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }
}
