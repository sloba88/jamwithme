<?php

namespace Jam\WebBundle\Controller;

use Elastica\Query\Bool;
use Elastica\Query\Match;
use Elastica\Query\MatchAll;
use Jam\CoreBundle\Entity\Compatibility;
use Jam\CoreBundle\Entity\Search;
use Jam\CoreBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Elastica\Util;
use Symfony\Component\HttpFoundation\Response;

class MusiciansController extends Controller
{
    /**
     * @Route("/musicians", name="musicians")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/musicians/find", name="musicians_find", options={"expose"=true})
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $searchParams = $request->query->get('search_form');
        $me = $this->getUser();
        $response = new JsonResponse();
        $genres = $request->query->get('genres');
        $instruments = $request->query->get('instruments');

        if ($searchParams && isset($searchParams['me'])) {
            //if searching nearby to other user
            $me = $this->getDoctrine()
                ->getRepository('JamUserBundle:User')
                ->find($searchParams['me']);
        }

        if (!$me->getLocation()) {
            $response->setData(array(
                'status'    => 'error',
                'data' => array()
            ));

            return $response;
        }

        $finder = $this->container->get('fos_elastica.finder.searches.user');
        $elasticaQuery = new MatchAll();

        if ($genres!=''){
            $categoryQuery = new \Elastica\Filter\Terms('genres.genre.id', explode(",", $genres));
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $categoryQuery);
        }

        if ($instruments!=''){
            $categoryQuery = new \Elastica\Filter\Terms('instruments.instrument.id', explode(",", $instruments));
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $categoryQuery);
        }

        if ($request->query->get('isTeacher')){
            $boolFilter = new \Elastica\Filter\Bool();
            $filter1 = new \Elastica\Filter\Term();
            $filter1->setTerm('isTeacher', '1');
            $boolFilter->addMust($filter1);
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $boolFilter);
        }

        if ($request->query->get('distance') && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                (intval($request->query->get('distance')) ? intval($request->query->get('distance')) : '20') . 'km'
            );
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $locationFilter);
        }

        $categoryQuery = new \Elastica\Filter\Term(array('username' => $me->getUsername()));
        $elasticaBool = new \Elastica\Filter\BoolNot($categoryQuery);
        $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $elasticaBool);

        $musicians = $finder->find($elasticaQuery);


        $em = $this->getDoctrine()->getManager();
        $ids = array();
        foreach ($musicians AS $m){
            array_push($ids, $m->getId());
            //Check if they have compatibility calculated and sort them by it

            $query = "SELECT compatibility
            FROM JamCoreBundle:Compatibility compatibility
            JOIN JamUserBundle:User musician
            WHEN (compatibility.musician2 = " . $m->getId() . " AND compatibility.musician = " .$me->getId() . " )
            OR (compatibility.musician = " . $m->getId() . " AND compatibility.musician2 = " .$me->getId() . " ) ";

            $res = $this->getDoctrine()->getManager()->createQuery($query)->getResult();

            if (!$res){
                $compatibility = new Compatibility();
                $compatibility->setMusician($me);
                $compatibility->setMusician2($m);
                $compatibility->calculate();

                $em->persist($compatibility);
            }
        }

        $em->flush();

        $musicians_data = array();

        /*
         *
         * DOCTRINE ORDER UGLY HACK
         * //TODO: how to order by compatibility inside elastica??
         *
         */

        if (count($ids) > 0){
            $query = "SELECT musician, compatibility.value
            FROM JamUserBundle:User musician
            JOIN JamCoreBundle:Compatibility compatibility
            WHEN compatibility.musician2 = musician AND compatibility.musician = " .$this->getUser()->getId();

            $query .= " WHERE musician.id IN (" . implode(",", $ids) . ")";
            $query .= " ORDER BY compatibility.value DESC ";

            $musicians = $this->getDoctrine()->getManager()->createQuery($query)->getResult();
        }

        /*
         *
         * DOCTRINE ORDER UGLY HACK
         *
         */


        foreach($musicians AS $music){
            $m = $music[0];
            /* @var $m \Jam\UserBundle\Entity\User */

            if ($m->getLocation()){
                if ($m->getLocation()->getNeighborhood() != ""){
                    $location = $m->getLocation()->getNeighborhood(). ', '.$m->getLocation()->getAdministrativeAreaLevel3();
                }else{
                    $location = $m->getLocation()->getAdministrativeAreaLevel3();
                }
            }else{
                $location = false;
            }

            $instrument = $m->getInstruments()->isEmpty() ? '' : $m->getInstruments()->first()->getInstrument()->getCategory()->getName();

            if ($instrument != ''){
                $icon = file_get_contents ($this->get('kernel')->getRootDir() . "/../web/assets/images/icons-svg/" . $instrument . ".svg");
            }else{
                $icon = '';
            }

            if ($m->getIsTeacher() == true) {
                $teacherIcon = file_get_contents ($this->get('kernel')->getRootDir() . "/../web/assets/images/icons-svg/Teacher.svg");
            }else{
                $teacherIcon = '';
            }

            $data_array = array(
                'username' => $m->getUsername(),
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'me' => $me == $m->getUsername() ? true : false,
                'genres' => $m->getGenresNamesArray(),
                'instrument' => $instrument,
                'icon' => $icon,
                'location' => $location,
                'teacherIcon' => $teacherIcon,
                'compatibility' => $music['value']
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }

    /**
     * @Route("/musicians/similar", name="musicians_similar", options={"expose"=true})
     * @Template()
     */
    public function similarAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $request->query->get('me');
        $distance = 20;
        $response = new JsonResponse();

        if ($me){
            //if its a valid user
            /** @var $me \Jam\UserBundle\Entity\User */
            $me = $this->getDoctrine()
                ->getRepository('JamUserBundle:User')
                ->find($me);
        }

        if (!$me->getLocation()){
            $response->setData(array(
                'status'    => 'error',
                'data' => array()
            ));

            return $response;
        }

        $finder = $this->container->get('fos_elastica.finder.searches.user');
        $boolQuery = new \Elastica\Query\Bool();

        //first take people that have at least something in common than sort them further

        if ($me->getGenres()->count() > 0){
            $ids = array();
            foreach($me->getGenres() AS $g){
                array_push($ids, $g->getId());
            }

            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('genres.id', $ids);
            $boolQuery->addShould($categoryQuery);
        }

        if ($me->getInstruments()->count() > 0){
            $ids = array();
            foreach($me->getInstruments() AS $g){
                array_push($ids, $g->getId());
            }

            $categoryQuery = new \Elastica\Query\Terms();
            $categoryQuery->setTerms('instruments.instrument.id', $ids);
            $boolQuery->addShould($categoryQuery);
        }

        /*
        if ($distance && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                (intval($distance) ? intval($distance) : '20') . 'km'
            );
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $locationFilter);
        }
        */

        /*

        if (isset($searchParams['instruments'])){
            $categoryQuery = new \Elastica\Filter\Terms('instruments.instrument.id', $searchParams['instruments']);
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $categoryQuery);
        }

        if (isset($searchParams['isTeacher'])){
            $boolFilter = new \Elastica\Filter\Bool();
            $filter1 = new \Elastica\Filter\Term();
            $filter1->setTerm('isTeacher', '1');
            $boolFilter->addMust($filter1);
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $boolFilter);
        }

        if (isset($searchParams['distance']) && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                (intval($searchParams['distance']) ? intval($searchParams['distance']) : '20') . 'km'
            );
            $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $locationFilter);
        }
        */

        /* exclude himself in result set */
        $categoryQuery = new \Elastica\Query\Terms();
        $categoryQuery->setTerms('username', array('username' => $me->getUsername()));
        $boolQuery->addMustNot($categoryQuery);

        $musicians = $finder->find($boolQuery);

        $musicians_data = array();

        foreach($musicians AS $m){

            //TODO: NOT FIRST IMAGE ANYMORE
            if ($m->getImages()->first()){
                $image = $m->getImages()->first()->getWebPath();
            } else{
                $image = '/images/placeholder-user.jpg';
            }

            if ($m->getLocation()){
                if ($m->getLocation()->getNeighborhood() != ""){
                    $location = $m->getLocation()->getNeighborhood(). ', '.$m->getLocation()->getAdministrativeAreaLevel3();
                }else{
                    $location = $m->getLocation()->getAdministrativeAreaLevel3();
                }
            }

            $data_array = array(
                'username' => $m->getUsername(),
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'image' => $this->get('liip_imagine.cache.manager')->getBrowserPath($image, 'my_thumb'),
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'me' => $me == $m->getUsername() ? true : false,
                'genres' => $m->getGenresNamesArray(),
                'location' => $location
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }

    public function getUniqueIconsAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $icons = '';
        $unique = array();

        foreach ($user->getInstruments() AS $cat){
            $instrument = $cat->getInstrument()->getCategory()->getName();
            if (!in_array($instrument, $unique)){
                $icons .= file_get_contents ($this->get('kernel')->getRootDir() . "/../web/assets/images/icons-svg/" . $instrument . ".svg");
                array_push($unique, $instrument);
            }
        }

        return new Response($icons);

    }
}
