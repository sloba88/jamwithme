<?php

namespace Jam\WebBundle\Controller;

use Elastica\Query\Bool;
use Elastica\Query\Match;
use Elastica\Query\MatchAll;
use Jam\CoreBundle\Entity\Search;
use Jam\CoreBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Elastica\Util;

class MusiciansController extends Controller
{
    /**
     * @Route("/musicians", name="musicians")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->findAll();

        $form = $this->createForm(new SearchType(), null, array(
            'method' => 'GET'
        ));

        $form->handleRequest($request);

        return array('musicians' => $musicians, 'form' => $form->createView());
    }

    /**
     * @Route("/musicians/find", name="musicians_find")
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $searchParams = $request->query->get('search_form');
        $me = $this->container->get('security.context')->getToken()->getUser();

        $finder = $this->container->get('fos_elastica.finder.searches.user');
        $elasticaQuery = new MatchAll();

        if ($searchParams){
            //$search = new Search();
            //$search->setGenres($searchParams['genres']);
            //$search->setCreator($this->container->get('security.context')->getToken()->getUser());
            //$em = $this->getDoctrine()->getManager();
            //$em->persist($search);
            //$em->flush();



            if (isset($searchParams['genres'])){
                $categoryQuery = new \Elastica\Filter\Terms('genres.id', $searchParams['genres']);
                $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $categoryQuery);
            }

            if (isset($searchParams['instruments'])){
                $categoryQuery = new \Elastica\Filter\Terms('instruments.id', $searchParams['instruments']);
                $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $categoryQuery);
            }

            if (isset($searchParams['distance'])){

                $locationFilter = new \Elastica\Filter\GeoDistance(
                    'pin',
                    array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                    (intval($searchParams['distance']) ? intval($searchParams['distance']) : '20') . 'km'
                );
                $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $locationFilter);
            }

            /* exclude myself in result set */
            $categoryQuery = new \Elastica\Filter\Term(array('username' => $me->getUsername()));
            $elasticaBool = new \Elastica\Filter\BoolNot($categoryQuery);
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $elasticaBool);

            $query = \Elastica\Query::create($elasticaQuery);

        }else{
            $query = new \Elastica\Query\MatchAll();
        }

        $musicians = $finder->find($query);

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
               'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
               'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
               'image' => $this->get('liip_imagine.cache.manager')->getBrowserPath($image, 'my_thumb'),
               'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
               'me' => $me == $m->getUsername() ? true : false,
               'genres' => $m->getGenresNamesArray()
            ));
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }
}
