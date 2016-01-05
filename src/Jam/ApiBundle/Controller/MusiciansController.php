<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Jam\CoreBundle\Entity\Search;

class MusiciansController extends FOSRestController
{
    /**
     * @Get("/musicians/find", name="musicians_find")
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if (!$me->getLocation()) {
            $view = $this->view(array(
                'message'    => 'No location set.',
                'success'    => false,
                'data' => array()
            ), 200);

            return $this->handleView($view);
        }

        //save the search at first
        //todo: put it in session
        if ($request->query->get('page') == '' || $request->query->get('page') == '1') {
            $search = new Search();
            $search->setDistance($request->query->get('distance'));
            $search->setGenres($request->query->get('genres'));
            $search->setInstruments($request->query->get('instruments'));
            $search->setIsTeacher($request->query->get('isTeacher'));

            $em->persist($search);
            $em->flush();
            $request->getSession()->set('searchId', $search->getId());
        } else {
            $search = $em->getRepository('JamCoreBundle:Search')->find($request->getSession()->get('searchId'));
        }

        $request->getSession()->save();
        $musicians = $this->get('search.musicians')->getElasticSearchResult($search, $request->query->all());

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
            'searchId'    => $search->getId(),
            'data' => $musicians_data
        ), 200);

        return $this->handleView($view);
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
