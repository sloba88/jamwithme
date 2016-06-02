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

        //todo: if there are no results check if he already subscribed to this before showing him subscribe button
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

            $avatar = $cacheManager->getBrowserPath($m->getAvatar(), 'medium_thumb');

            $data_array = array(
                'username' => $m->getUsername(),
                'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                'me' => $me == $m->getUsername() ? true : false,
                'genres' => $m->getGenresNamesArray(),
                'instrument' => $instrument,
                'location' => $location,
                'compatibility' => $value,
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
            'finalResults' => count($musicians_data) < 20 ? true: false
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
}
