<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrandsController extends Controller
{
    /**
     * @Route("/api/brands", name="api_brands", options={"expose"=true})
     * @Template()
     */
    public function getAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $q = $request->query->get('q');

        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                "SELECT b.id, b.name FROM JamCoreBundle:Brand b
                WHERE b.name LIKE :q "
            )->setParameter('q', '%'.$q.'%');

        $res = $query->getResult();

        return new JsonResponse($res);
    }
}
