<?php

namespace Jam\WebBundle\Controller;

use Elastica\Filter\Bool;
use Elastica\Filter\BoolNot;
use Elastica\Filter\Ids;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use Jam\CoreBundle\Entity\Compatibility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $request->getSession()->save();
        $me = $this->getUser();
        $response = new JsonResponse();
        $genres = $request->query->get('genres');
        $instruments = $request->query->get('instruments');

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

        $idsFilter = new Ids();
        $idsFilter->setIds(array($me->getId()));
        $elasticaBool = new BoolNot($idsFilter);
        $elasticaQuery = new Filtered($elasticaQuery, $elasticaBool);


        $musicians = $finder->find($elasticaQuery);


        $em = $this->getDoctrine()->getManager();
        $ids = array();

        $em->createQuery('DELETE FROM JamCoreBundle:Compatibility')->execute();

        foreach ($musicians AS $m){
            array_push($ids, $m->getId());
            //Check if they have compatibility calculated and sort them by it

            /* TODO: For now clear every time
            $query = "SELECT compatibility
            FROM JamCoreBundle:Compatibility compatibility
            JOIN JamUserBundle:User musician
            WHEN (compatibility.musician2 = " . $m->getId() . " AND compatibility.musician = " .$me->getId() . " )
            OR (compatibility.musician = " . $m->getId() . " AND compatibility.musician2 = " .$me->getId() . " ) ";

            $res = $this->getDoctrine()->getManager()->createQuery($query)->getResult();

            if (!$res){
            */
                $compatibility = new Compatibility();
                $compatibility->setMusician($me);
                $compatibility->setMusician2($m);
                $compatibility->calculate();

                $em->persist($compatibility);
            //}
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
            WHEN (compatibility.musician2 = musician AND compatibility.musician = " .$this->getUser()->getId() . " )
            OR (compatibility.musician = musician AND compatibility.musician2 = " .$this->getUser()->getId() . " ) ";

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
