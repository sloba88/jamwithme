<?php

namespace Jam\WebBundle\Controller;

use Elastica\Query\Bool;
use Elastica\Query\Match;
use Elastica\Query\MatchAll;
use Jam\CoreBundle\Entity\Search;
use Jam\CoreBundle\Entity\Shout;
use Jam\CoreBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        $sortQuery = \Elastica\Query::create($elasticaQuery);
        $sortQuery->addSort(array('createdAt' => array('order' => 'ASC')));

        $shouts = $finder->find($sortQuery);

        $response = new JsonResponse();
        $musicians_data = array();

        foreach($shouts AS $s){

            /** @var $m \Jam\UserBundle\Entity\User */
            $m = $s->getCreator();

            if (!$m->getLocation()){
                //for now don't show shouts without user location
                continue;
            }

            if ($m->getImages()->first()){
                $image = $m->getImages()->first()->getWebPath();
            } else{
                $image = '/images/placeholder-user.jpg';
            }

            $data_array = array(
                'text' => $s->getText(),
                'createdAt' => $s->getCreatedAt()->format('Y-m-d H:i'),
                'id' => $s->getId(),
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

    /**
     * @Route("/shouts/{id}", name="remove_shout", options={"expose"=true})
     * @Method({"DELETE"})
     */
    public function removeShoutAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $shout = $this->getDoctrine()->getRepository('JamCoreBundle:Shout')->find($request->get('id'));
        $responseData = array(
            'success' => false
        );

        if ($shout instanceof Shout) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($shout);
            $em->flush();
            $responseData['success'] = true;
        } else {
            $responseData['message'] = 'Shout not found';
        }

        $response = new JsonResponse();
        $response->setData($responseData);

        return $response;
    }
}
