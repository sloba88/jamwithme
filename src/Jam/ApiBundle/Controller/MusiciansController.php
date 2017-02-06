<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FormEvent;
use Jam\CoreBundle\Entity\Search;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\Form;

class MusiciansController extends FOSRestController
{
    /**
     * @Get("/musicians/find", name="musicians_find")
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();

        if (!$me) {
            return $this->findPublicAction($request);
        }

        $em = $this->getDoctrine()->getManager();
        $alreadySubscribed = false;

        if (!$me->getLocation()) {
            $view = $this->view(array(
                'message'    => 'No location set.',
                'success'    => false,
                'data' => array()
            ), 200);

            return $this->handleView($view);
        }

        //save the search at first
        //todo: put this in event
        if ($request->query->get('page') == '' || $request->query->get('page') == '1') {
            $search = new Search();
            $search->setDistance($request->query->get('distance'));
            $search->setGenres($request->query->get('genres'));
            $search->setInstruments($request->query->get('instruments'));
            $search->setIsTeacher($request->query->get('isTeacher') ? 1 : 0);
            $search->setAdministrativeAreaLevel3($request->query->get('locations'));
            $em->persist($search);
            $em->flush();
            $request->getSession()->set('searchId', $search->getId());
        } else {
            $search = $em->getRepository('JamCoreBundle:Search')->find($request->getSession()->get('searchId'));
        }

        $request->getSession()->save();
        $musicians = $this->get('search.musicians')->getElasticSearchResult($search, $request->query->all());

        //todo: if there are no results check if he already subscribed to this before showing him subscribe button
        //todo: do this in separate controller
        if (count($musicians) == 0) {
            $search = $em->getRepository('JamCoreBundle:Search')->findOneBy(array(
                'creator' => $me,
                'isSubscribed' => true,
                'genres' => $request->query->get('genres') ? $request->query->get('genres') : '',
                'instruments' => $request->query->get('instruments') ? $request->query->get('instruments') : '',
                'isTeacher' => $request->query->get('isTeacher')
            ));

            if (count($search) > 0) {
                $alreadySubscribed = true;
            }
        }

        $musicians_data = array();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        foreach($musicians AS $k=> $mus){
            $m = $mus->getTransformed();
            $score = round($mus->getResult()->getScore(), 2);
            if ($score >= 1.5 ) {
                $compatibility = 'high';
            } else if ($score >= 0.5 ) {
                $compatibility = 'medium';
            } else {
                $compatibility = 'low';
            }

            /* @var $m \Jam\UserBundle\Entity\User */

            $avatar = $cacheManager->getBrowserPath($m->getAvatar(), 'medium_thumb');

            $data_array = array(
                'username' => $m->getUsername(),
                'displayName' => $m->getDisplayName(),
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'me' => $me == $m->getUsername() ? true : false,
                'genres' => $m->getGenresNamesArray(),
                'instrument' => $m->getMainInstrumentAsString(),
                'location' => $m->getDisplayLocation(),
                'compatibility' => $compatibility,
                'avatar' => $avatar
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'alreadySubscribed' => $alreadySubscribed,
            'data' => $musicians_data,
            'finalResults' => count($musicians_data) < 15 ? true: false
        ), 200);

        return $this->handleView($view);
    }

    private function findPublicAction($request)
    {
        $musicians = $this->get('search.musicians')->getElasticSearchPublicResult($request->query->all());

        $musicians_data = array();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        foreach($musicians AS $k=> $m){

            /* @var $m \Jam\UserBundle\Entity\User */

            $avatar = $cacheManager->getBrowserPath($m->getAvatar(), 'medium_thumb');

            $data_array = array(
                'username' => $m->getUsername(),
                'displayName' => $m->getDisplayName(),
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'genres' => $m->getGenresNamesArray(),
                'instrument' => $m->getMainInstrumentAsString(),
                'location' => $m->getDisplayLocation(),
                'avatar' => $avatar
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data,
            'finalResults' => count($musicians_data) < 15 ? true: false
        ), 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/musicians/find-public-map", name="musicians_find_map")
     */
    public function findPublicMapAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if ((!$request->query->get('lat') || !$request->query->get('lng')) && !$request->query->get('city')) {
            $view = $this->view(array(
                'status'    => 'false',
                'message' => 'No location sent.',
            ), 200);
            return $this->handleView($view);
        }

        switch ($request->query->get('city')) {
            case "Helsinki":
                $location['lat'] = '60.1882164';
                $location['lng'] = '24.9212983';
                break;
            case "Espoo":
                $location['lat'] = '60.1964836';
                $location['lng'] = '24.6702005';
                break;
            case "Vantaa":
                $location['lat'] = '60.291704';
                $location['lng'] = '25.0298731';
                break;
            case "Oulu":
                $location['lat'] = '65.0148883';
                $location['lng'] = '25.4699134';
                break;
            case "Turku":
                $location['lat'] = '60.456773';
                $location['lng'] = '22.2506582';
                break;
            case "Tampere":
                $location['lat'] = '61.505809';
                $location['lng'] = '23.7496406';
                break;
            case "Jyväskylä":
                $location['lat'] = '62.2469489';
                $location['lng'] = '25.7357698';
                break;
            case "London":
                $location['lat'] = '51.507799';
                $location['lng'] = '-0.127632';
                break;
            case "Birmingham":
                $location['lat'] = '52.486685';
                $location['lng'] = '-1.890460';
                break;
            default:
                $location['lat'] = $request->query->get('lat');
                $location['lng'] = $request->query->get('lng');
        }

        $musicians = $this->get('search.musicians')->getElasticSearchPublicResultMap($location);

        $musicians_data = array();

        foreach($musicians AS $m){
            /* @var $m \Jam\UserBundle\Entity\User */

            $instrument = $m->getInstruments()->isEmpty() ? '' : $m->getInstruments()->first()->getInstrument()->getCategory()->getName();

            $data_array = array(
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'instrument' => $instrument,
            );

            if ($m->getIsTeacher()){
                $data_array['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data,
            'location' => $location
        ), 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/musicians/similar", name="musicians_similar")
     */
    public function findSimilarAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $username = $request->query->get('username');
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $musicians = $this->get('search.musicians')->getElasticSimilarUsersResult($user);

        $musicians_data = array();

        foreach($musicians AS $m){
            /* @var $m \Jam\UserBundle\Entity\User */

            $data_array = array(
                'username' => $m->getUsername(),
                'firstName' => $m->getFirstName(),
                'lastName' => $m->getLastName()
            );

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data,
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

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS);

        $view = $this->view(array(
            'status'    => 'success',
            'message'   => 'Address successfully saved.',
        ), 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/musicians/current-search", name="musicians_current_search")
     */
    public function getCurrentSearchAction()
    {
        $searchId = 3611;
        $position = 2;
        $userId = 16;

        $position = [
            'limit' => 5,
            'offset' => 0
        ];

        $em = $this->getDoctrine()->getManager();

        $search = $em->getRepository('JamCoreBundle:Search')->find($searchId);

        $musicians = $this->get('search.musicians')->getElasticSearchResult($search, $position);

        $musicians_data = array();

        foreach($musicians AS $mus) {
            $m = $mus->getTransformed();
            /* @var $m \Jam\UserBundle\Entity\User */

            $data_array = array(
                'username' => $m->getUsername(),
                'firstName' => $m->getFirstName(),
                'lastName' => $m->getLastName(),
                'userId' => $m->getId(),
            );

            $userId == $m->getId() ? $data_array['currentUser'] = true : false;

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'data' => $musicians_data,
        ), 200);

        return $this->handleView($view);

    }
}
