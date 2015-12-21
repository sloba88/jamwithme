<?php

namespace Jam\ApiBundle\Controller;

use Elastica\Filter\Bool;
use Elastica\Filter\BoolNot;
use Elastica\Filter\Ids;
use Elastica\Filter\Nested;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;

class MusiciansController extends FOSRestController
{
    /**
     * @Get("/musicians/find", name="musicians_find")
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $request->getSession()->save();
        $me = $this->getUser();
        $page = $request->query->get('page') == '' ? 1 : intval($request->query->get('page'));
        $perPage = 20;
        $distance = intval($request->query->get('distance'));

        if ($distance > 20) {
            $distance = 20;
        }

        $genres = $request->query->get('genres');
        $instruments = $request->query->get('instruments');

        if (!$me->getLocation()) {
            $view = $this->view(array(
                'message'    => 'No location set.',
                'success'    => false,
                'data' => array()
            ), 200);

            return $this->handleView($view);
        }

        $finder = $this->container->get('fos_elastica.finder.searches.compatibility');
        $elasticaQuery = new MatchAll();

        if ($genres != ''){
            $elasticaQuery = $this->addToNestedFilter(new Terms('musician2.genres.genre.id', explode(",", $genres)), $elasticaQuery);
        }

        if ($instruments != ''){
            $elasticaQuery = $this->addToNestedFilter(new Terms('musician2.instruments.instrument.id', explode(",", $genres)), $elasticaQuery);
        }

        if ($request->query->get('isTeacher')){
            $boolFilter = new Bool();
            $filter1 = new Term();
            $filter1->setTerm('musician2.isTeacher', '1');
            $boolFilter->addMust($filter1);

            $nested = new Nested();
            $nested->setPath("musician2");
            $nested->setFilter($boolFilter);

            $elasticaQuery = new Filtered($elasticaQuery, $nested);
        }

        if ($request->query->get('distance') && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'musician2.pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '20') . 'km'
            );

            $nested = new Nested();
            $nested->setPath("musician2");
            $nested->setFilter($locationFilter);

            $elasticaQuery = new Filtered($elasticaQuery, $nested);
        }

        //kick me out of result set
        $idsFilter = new Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaBool = new BoolNot($idsFilter);
        $elasticaQuery = new Filtered($elasticaQuery, $elasticaBool);

        //show my compatibilities
        $boolFilter = new Bool();
        $filter1 = new Term();
        $filter1->setTerm('musician.id', $me->getId());
        $boolFilter->addMust($filter1);
        $elasticaQuery = new Filtered($elasticaQuery, $boolFilter);

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);
        $query->addSort(array('musician2.isJammer' => array('order' => 'desc'), 'value' => array('order' => 'desc')));

        $musicians = $finder->find($query);

        $musicians_data = array();

        foreach($musicians AS $mus){
            $m = $mus->getMusician2();
            $value = $mus->getValue();
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
                'compatibility' => $value
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data
        ), 200);

        return $this->handleView($view);
    }

    private function addToNestedFilter($categoryQuery, $elasticaQuery)
    {
        $nested = new Nested();
        $nested->setPath("musician2");
        $nested->setFilter($categoryQuery);

        return new Filtered($elasticaQuery, $nested);
    }

    /**
     * @Post("/musician/location", name="api_set_musician_location")
     */
    public function setLocationAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();

        $coords = explode(',', $request->request->get('coords'));

        //set user location from coordinates
        $location = $this->get('jam.location_set')->reverseGeoCode($coords);
        $me->setLocation($location);
        $this->get('fos_user.user_manager')->updateUser($me);

        $view = $this->view(array(
            'status'    => 'success',
            'message'   => 'Address successfully saved.',
        ), 200);

        return $this->handleView($view);
    }
}
