<?php

namespace Jam\ApiBundle\Controller;

use Elastica\Filter\Bool;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query\Filtered;
use Elastica\Query\MatchAll;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Jam\CoreBundle\Entity\Shout;
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
     * @Get("/shouts/{username}", name="user_shouts")
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

    private function formatResponse($results, $message = false)
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

        //TODO: put this form in separate file
        $form = $this->createFormBuilder(new Shout())
            ->add('text', 'textarea', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'label.say.something.cool',
                    'maxlength' => 250
                ),
                'required' => true
            ))
            ->add('send', 'submit', array(
                'label' => 'label.send'
            ))
            ->getForm();

        $form->handleRequest($request);
        $responseData = array();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$this->getUser()){
                $responseData['status'] = false;
                $responseData['message'] = $this->get('translator')->trans('exception.you.shall.not.pass');
            }

            $shout = $form->getData();
            $shout->setCreator($this->getUser());
            $em->persist($shout);

            try {
                $em->flush();
                return $this->formatResponse(array($shout), 'You have shouted successfully!');
            }catch (\Exception $e) {
                $responseData['status'] = false;
                $responseData['message'] = $this->get('translator')->trans('exception.you.shall.not.pass');
            }
        } else {
            $responseData['status'] = false;
            $responseData['message'] = 'Form not valid';
        }

        $view = $this->view($responseData, 200);

        return $this->handleView($view);
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