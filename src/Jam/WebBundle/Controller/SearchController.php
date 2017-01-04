<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route(name="search")
     * @Template()
     */
    public function filterAction()
    {
        $masterRequest = $this->get('request_stack')->getMasterRequest();

        return array(
            'route' => $masterRequest->get('_route'),
            'searchParams' => $masterRequest->attributes->all()
        );
    }
}
