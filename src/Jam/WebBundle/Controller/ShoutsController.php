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

class ShoutsController extends Controller
{
    /**
     * @Route("/shouts/find", name="shouts_find")
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $searchParams = $request->query->get('search_form');
        $me = $this->container->get('security.context')->getToken()->getUser();

        $finder = $this->container->get('fos_elastica.finder.searches.shout');
        $elasticaQuery = new MatchAll();

        if ($searchParams){

            if (isset($searchParams['distance']) && $me->getLat()){
                $locationFilter = new \Elastica\Filter\GeoDistance(
                    'pin',
                    array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                    (intval($searchParams['distance']) ? intval($searchParams['distance']) : '20') . 'km'
                );
                $elasticaQuery = new \Elastica\Query\Filtered($elasticaQuery, $locationFilter);
            }
        }

        $shouts = $finder->find($elasticaQuery);

        $response = new JsonResponse();
        $musicians_data = array();

        foreach($shouts AS $s){

            $m = $s->getCreator();

            if ($m->getImages()->first()){
                $image = $m->getImages()->first()->getWebPath();
            } else{
                $image = '/images/placeholder-user.jpg';
            }

            $data_array = array(
                'text' => $s->getText(),
                'createdAt' => $s->getCreatedAt(),
                'musician' => array(
                    'username' => $m->getUsername(),
                    'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                    'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                    'image' => $this->get('liip_imagine.cache.manager')->getBrowserPath($image, 'my_thumb'),
                    'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                    'me' => $me == $m->getUsername() ? true : false,
                    'genres' => $m->getGenresNamesArray(),
                    'location' => $m->getLocation()->getAdministrativeAreaLevel3(),
                )
            );

            if ($m->getIsTeacher()){
                $data_array['musician']['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $response->setData(array(
            'status'    => 'success',
            'data' => $musicians_data
        ));

        return $response;
    }
}
