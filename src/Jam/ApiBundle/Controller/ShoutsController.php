<?php

namespace Jam\ApiBundle\Controller;

use Elastica\Query;
use Elastica\Query\Term;
use Elastica\Query\Terms;
use Elastica\Query\MatchAll;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Jam\CoreBundle\Entity\Shout;
use Jam\CoreBundle\Form\Type\ShoutType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShoutsController extends FOSRestController
{
    /**
     * @Get("/shouts/find", name="shouts_find")
     */
    public function findAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $me = $this->getUser();
        $genres = $request->query->get('genres');
        $instruments = $request->query->get('instruments');

        $perPage = 10;
        $page = $request->query->get('page') == '' ? 1 : intval($request->query->get('page'));

        if ($request->query->get('distance') > 50) {
            $distance = 50;
        } else {
            $distance = $request->query->get('distance');
        }

        $finder = $this->container->get('fos_elastica.finder.searches.shout');
        $q = new Query();
        $elasticaQuery = new Query\BoolQuery($q);
        $elasticaQuery->addMust(new MatchAll());

        if ($genres!=''){
            $categoryQuery = new Terms('genres.genre.id', explode(",", $genres));
            $elasticaQuery->addMust($categoryQuery);
        }

        if ($instruments!=''){
            $categoryQuery = new Terms('instruments.instrument.id', explode(",", $instruments));
            $elasticaQuery->addMust($categoryQuery);
        }

        if ($request->query->get('isTeacher')){
            $filter1 = new Term();
            $filter1->setTerm('isTeacher', '1');
            $elasticaQuery->addMust($filter1);
        }

        if ($request->query->get('distance') && $me->getLat()){
            $locationFilter = new \Elastica\Query\GeoDistance(
                'creator.pin',
                array('lat' => floatval($me->getLat()), 'lon' => floatval($me->getLon())),
                ($distance ? $distance : '100') . 'km'
            );

            $elasticaQuery->addMust($locationFilter);
        }

        $query = new \Elastica\Query();
        $query->setQuery($elasticaQuery);
        $query->setSize($perPage);
        $query->setFrom(($page - 1) * $perPage);
        $query->addSort(array('createdAt' => array('order' => 'desc')));

        $shouts = $finder->find($query);

        return $this->formatResponse($shouts);
    }

    /**
     * @Get("/shouts/{username}", name="user_shouts")
     */
    public function getAction($username)
    {
        //TODO: put pagination to this, it is fine up to 100 shouts to have it without
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

    private function formatResponse($results, $message = false)
    {
        $me = $this->getUser();
        $response = new JsonResponse();
        $musicians_data = array();

        foreach($results AS $s){

            /** @var $m \Jam\UserBundle\Entity\User */
            /** @var $s \Jam\CoreBundle\Entity\Shout */
            $m = $s->getCreator();

            $location = '';

            if ($m->getLocation()->getAdministrativeAreaLevel3()) {
                if ($m->getLocation()->getNeighborhood()) {
                    $location = $m->getLocation()->getNeighborhood() . ', ' . $m->getLocation()->getAdministrativeAreaLevel3();
                } else {
                    $location = $m->getLocation()->getAdministrativeAreaLevel3();
                }
            }

            $data_array = array(
                'text' => $s->getTextFrontend(),
                'createdAt' => $s->getCreatedAtAgo(),
                'id' => $s->getId(),
                'musician' => array(
                    'username' => $m->getUsername(),
                    'displayName' => $m->getDisplayName(),
                    'lat' => $m->getLocation() ? $m->getLocation()->getLat() : '',
                    'lng' => $m->getLocation() ? $m->getLocation()->getLng() : '',
                    'image' => $this->get('liip_imagine.cache.manager')->getBrowserPath($m->getAvatar(), 'my_thumb'),
                    'url' => $this->generateUrl('musician_profile', array('username' => $m->getUsername())),
                    'me' => $me == $m->getUsername() ? true : false,
                    'genres' => $m->getGenresNamesArray(),
                    'location' => $location,
                )
            );

            if ($m->getIsTeacher()){
                $data_array['musician']['teacher'] = true;
            }

            array_push($musicians_data, $data_array);
        }

        $view = $this->view(array(
            'status'    => 'success',
            'message'   => $message,
            'data' => $musicians_data,
        ), 200);

        return $this->handleView($view);
    }

    /**
     * @Delete("/shouts/{id}", name="remove_shout")
     */
    public function removeShoutAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $shout = $this->getDoctrine()->getRepository('JamCoreBundle:Shout')->find($request->get('id'));
        $responseData = array(
            'status' => false
        );

        if ($shout instanceof Shout) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($shout);
            $em->flush();
            $responseData['status'] = 'success';
            $responseData['message'] = 'Shout removed successfully.';
        } else {
            $responseData['message'] = 'Shout not found';
        }

        $view = $this->view($responseData, 200);

        return $this->handleView($view);
    }

    /**
     * @Post("/shout/add", name="create_shout")
     */
    public function createShoutAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if ($this->get('shout.counter')->getSecondsDifference() > 0) {
            $responseData['status'] = 'success';
            $responseData['message'] = 'Form not valid';

            $response = new JsonResponse();
            $response->setData($responseData);

            return $response;
        }

        $shout = new Shout();
        $form = $this->createForm(ShoutType::class, $shout);

        $form->handleRequest($request);
        $responseData = array();

        //$shout->setText($this->cleaner($shout->getText()));
        $shout->setText($shout->getText());

        $validator = $this->get('validator');
        $errors = $validator->validate($shout);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();

            if (!$this->getUser()){
                $responseData['status'] = false;
                $responseData['message'] = $this->get('translator')->trans('exception.you.shall.not.pass');
            }

            $em->persist($shout);
            $em->flush();

            /* send data to GA */
            $data = array(
                'uid'=> $this->getUser()->getId(),
                'ec'=> 'shout',
                'ea'=> 'created'
            );
            $this->get('happyr.google.analytics.tracker')->send($data, 'event');

            return $this->formatResponse(array($shout), 'You have shouted successfully!');

        } else {
            $responseData['status'] = false;
            $responseData['message'] = 'Form not valid. Keep in mind that links are not permitted here.';
        }

        $view = $this->view($responseData, 200);

        return $this->handleView($view);
    }

    private function cleaner($url) {

        $U = explode(' ',$url);

        $W =array();
        foreach ($U as $k => $u) {
            if (stristr($u, 'http')) {
                unset($U[$k]);
                return $this->cleaner(implode(' ', $U));
            }

            if (stristr($u, ".")) { //only preg_match if there is a dot
                if ($this->containsTLD($u) === true) {
                    unset($U[$k]);
                    return $this->cleaner(implode(' ', $U));
                }

            }
        }
        return implode(' ',$U);
    }

    private function containsTLD($string) {
      preg_match(
        "/(AC($|\/)|\.AD($|\/)|\.AE($|\/)|\.AERO($|\/)|\.AF($|\/)|\.AG($|\/)|\.AI($|\/)|\.AL($|\/)|\.AM($|\/)|\.AN($|\/)|\.AO($|\/)|\.AQ($|\/)|\.AR($|\/)|\.ARPA($|\/)|\.AS($|\/)|\.ASIA($|\/)|\.AT($|\/)|\.AU($|\/)|\.AW($|\/)|\.AX($|\/)|\.AZ($|\/)|\.BA($|\/)|\.BB($|\/)|\.BD($|\/)|\.BE($|\/)|\.BF($|\/)|\.BG($|\/)|\.BH($|\/)|\.BI($|\/)|\.BIZ($|\/)|\.BJ($|\/)|\.BM($|\/)|\.BN($|\/)|\.BO($|\/)|\.BR($|\/)|\.BS($|\/)|\.BT($|\/)|\.BV($|\/)|\.BW($|\/)|\.BY($|\/)|\.BZ($|\/)|\.CA($|\/)|\.CAT($|\/)|\.CC($|\/)|\.CD($|\/)|\.CF($|\/)|\.CG($|\/)|\.CH($|\/)|\.CI($|\/)|\.CK($|\/)|\.CL($|\/)|\.CM($|\/)|\.CN($|\/)|\.CO($|\/)|\.COM($|\/)|\.COOP($|\/)|\.CR($|\/)|\.CU($|\/)|\.CV($|\/)|\.CX($|\/)|\.CY($|\/)|\.CZ($|\/)|\.DE($|\/)|\.DJ($|\/)|\.DK($|\/)|\.DM($|\/)|\.DO($|\/)|\.DZ($|\/)|\.EC($|\/)|\.EDU($|\/)|\.EE($|\/)|\.EG($|\/)|\.ER($|\/)|\.ES($|\/)|\.ET($|\/)|\.EU($|\/)|\.FI($|\/)|\.FJ($|\/)|\.FK($|\/)|\.FM($|\/)|\.FO($|\/)|\.FR($|\/)|\.GA($|\/)|\.GB($|\/)|\.GD($|\/)|\.GE($|\/)|\.GF($|\/)|\.GG($|\/)|\.GH($|\/)|\.GI($|\/)|\.GL($|\/)|\.GM($|\/)|\.GN($|\/)|\.GOV($|\/)|\.GP($|\/)|\.GQ($|\/)|\.GR($|\/)|\.GS($|\/)|\.GT($|\/)|\.GU($|\/)|\.GW($|\/)|\.GY($|\/)|\.HK($|\/)|\.HM($|\/)|\.HN($|\/)|\.HR($|\/)|\.HT($|\/)|\.HU($|\/)|\.ID($|\/)|\.IE($|\/)|\.IL($|\/)|\.IM($|\/)|\.IN($|\/)|\.INFO($|\/)|\.INT($|\/)|\.IO($|\/)|\.IQ($|\/)|\.IR($|\/)|\.IS($|\/)|\.IT($|\/)|\.JE($|\/)|\.JM($|\/)|\.JO($|\/)|\.JOBS($|\/)|\.JP($|\/)|\.KE($|\/)|\.KG($|\/)|\.KH($|\/)|\.KI($|\/)|\.KM($|\/)|\.KN($|\/)|\.KP($|\/)|\.KR($|\/)|\.KW($|\/)|\.KY($|\/)|\.KZ($|\/)|\.LA($|\/)|\.LB($|\/)|\.LC($|\/)|\.LI($|\/)|\.LK($|\/)|\.LR($|\/)|\.LS($|\/)|\.LT($|\/)|\.LU($|\/)|\.LV($|\/)|\.LY($|\/)|\.MA($|\/)|\.MC($|\/)|\.MD($|\/)|\.ME($|\/)|\.MG($|\/)|\.MH($|\/)|\.MIL($|\/)|\.MK($|\/)|\.ML($|\/)|\.MM($|\/)|\.MN($|\/)|\.MO($|\/)|\.MOBI($|\/)|\.MP($|\/)|\.MQ($|\/)|\.MR($|\/)|\.MS($|\/)|\.MT($|\/)|\.MU($|\/)|\.MUSEUM($|\/)|\.MV($|\/)|\.MW($|\/)|\.MX($|\/)|\.MY($|\/)|\.MZ($|\/)|\.NA($|\/)|\.NAME($|\/)|\.NC($|\/)|\.NE($|\/)|\.NET($|\/)|\.NF($|\/)|\.NG($|\/)|\.NI($|\/)|\.NL($|\/)|\.NO($|\/)|\.NP($|\/)|\.NR($|\/)|\.NU($|\/)|\.NZ($|\/)|\.OM($|\/)|\.ORG($|\/)|\.PA($|\/)|\.PE($|\/)|\.PF($|\/)|\.PG($|\/)|\.PH($|\/)|\.PK($|\/)|\.PL($|\/)|\.PM($|\/)|\.PN($|\/)|\.PR($|\/)|\.PRO($|\/)|\.PS($|\/)|\.PT($|\/)|\.PW($|\/)|\.PY($|\/)|\.QA($|\/)|\.RE($|\/)|\.RO($|\/)|\.RS($|\/)|\.RU($|\/)|\.RW($|\/)|\.SA($|\/)|\.SB($|\/)|\.SC($|\/)|\.SD($|\/)|\.SE($|\/)|\.SG($|\/)|\.SH($|\/)|\.SI($|\/)|\.SJ($|\/)|\.SK($|\/)|\.SL($|\/)|\.SM($|\/)|\.SN($|\/)|\.SO($|\/)|\.SR($|\/)|\.ST($|\/)|\.SU($|\/)|\.SV($|\/)|\.SY($|\/)|\.SZ($|\/)|\.TC($|\/)|\.TD($|\/)|\.TEL($|\/)|\.TF($|\/)|\.TG($|\/)|\.TH($|\/)|\.TJ($|\/)|\.TK($|\/)|\.TL($|\/)|\.TM($|\/)|\.TN($|\/)|\.TO($|\/)|\.TP($|\/)|\.TR($|\/)|\.TRAVEL($|\/)|\.TT($|\/)|\.TV($|\/)|\.TW($|\/)|\.TZ($|\/)|\.UA($|\/)|\.UG($|\/)|\.UK($|\/)|\.US($|\/)|\.UY($|\/)|\.UZ($|\/)|\.VA($|\/)|\.VC($|\/)|\.VE($|\/)|\.VG($|\/)|\.VI($|\/)|\.VN($|\/)|\.VU($|\/)|\.WF($|\/)|\.WS($|\/)|\.XN--0ZWM56D($|\/)|\.XN--11B5BS3A9AJ6G($|\/)|\.XN--80AKHBYKNJ4F($|\/)|\.XN--9T4B11YI5A($|\/)|\.XN--DEBA0AD($|\/)|\.XN--G6W251D($|\/)|\.XN--HGBK6AJ7F53BBA($|\/)|\.XN--HLCJ6AYA9ESC7A($|\/)|\.XN--JXALPDLP($|\/)|\.XN--KGBECHTV($|\/)|\.XN--ZCKZAH($|\/)|\.YE($|\/)|\.YT($|\/)|\.YU($|\/)|\.ZA($|\/)|\.ZM($|\/)|\.ZW)/i",
        $string,
        $M);
      $has_tld = (count($M) > 0) ? true : false;
      return $has_tld;
    }


    /**
     * @Get("/shout/can", name="can_shout")
     */
    public function canShoutAction()
    {
        $view = $this->view(array(
            'status'    => 'success',
            'data' => $this->get('shout.counter')->getSecondsDifference()
        ), 200);

        return $this->handleView($view);
    }

}
