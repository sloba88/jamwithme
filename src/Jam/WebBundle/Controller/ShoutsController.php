<?php

namespace Jam\WebBundle\Controller;

use Elastica\Filter\Bool;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Elastica\Util;

class ShoutsController extends Controller
{
    /**
     * @Route("/shouts/find", name="shouts_find", options={"expose"=true})
     * @Template()
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();
        $genres = $request->query->get('genres');
        $instruments = $request->query->get('instruments');

        $finder = $this->container->get('fos_elastica.finder.searches.shout');
        $elasticaQuery = new MatchAll();

        if ($genres!=''){
            $categoryQuery = new Terms('genres.genre.id', explode(",", $genres));
            $elasticaQuery = new Filtered($elasticaQuery, $categoryQuery);
        }

        if ($instruments!=''){
            $categoryQuery = new Terms('instruments.instrument.id', explode(",", $instruments));
            $elasticaQuery = new Filtered($elasticaQuery, $categoryQuery);
        }

        if ($request->query->get('isTeacher')){
            $boolFilter = new Bool();
            $filter1 = new Term();
            $filter1->setTerm('isTeacher', '1');
            $boolFilter->addMust($filter1);
            $elasticaQuery = new Filtered($elasticaQuery, $boolFilter);
        }

        if ($request->query->get('distance') && $me->getLat()){
            $locationFilter = new \Elastica\Filter\GeoDistance(
                'pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                (intval($request->query->get('distance')) ? intval($request->query->get('distance')) : '20') . 'km'
            );
            $elasticaQuery = new Filtered($elasticaQuery, $locationFilter);
        }

        $sortQuery = \Elastica\Query::create($elasticaQuery);
        $sortQuery->addSort(array('createdAt' => array('order' => 'ASC')));

        $shouts = $finder->find($elasticaQuery);

        return $this->formatResponse($shouts);
    }

    /**
     * @Route("/shouts/{username}", name="user_shouts", options={"expose"=true})
     * @Template()
     */
    public function getAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException('No such user.');
        }

        $shouts = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Shout')
            ->findBy(array('creator' => $user));

        return $this->formatResponse($shouts);
    }

    private function formatResponse($results)
    {
        $me = $this->getUser();
        $response = new JsonResponse();
        $musicians_data = array();

        foreach($results AS $s){

            /** @var $m \Jam\UserBundle\Entity\User */
            /** @var $s \Jam\CoreBundle\Entity\Shout */
            $m = $s->getCreator();

            if ($m->getImages()->first()){
                $image = $m->getImages()->first()->getWebPath();
            } else{
                $image = '/images/placeholder-user.jpg';
            }

            $data_array = array(
                'text' => $s->getText(),
                'createdAt' => $s->getCreatedAt()->format('Y-m-d H:i'),
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
