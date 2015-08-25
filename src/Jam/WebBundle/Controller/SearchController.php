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
        $request = $this->get('request_stack')->getMasterRequest();

        $form = $this->get('form.factory')->createNamedBuilder('', 'search_form', null, array(
            'method' => 'GET'
        ))->getForm();

        $form->handleRequest($request);

        return array('form' => $form->createView());
    }
}
