<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", options={"expose"=true})
     * @Route("/teachers", name="teachers", options={"expose"=true})
     * @Template
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() == null) {
            return $this->forward('JamUserBundle:Registration:register');
        }
    }

    /**
     * @Route("/invite", name="invite", options={"expose"=true})
     * @Template()
     */
    public function inviteAction(Request $request)
    {
        $totalInvitationsCount = count($this->getDoctrine()
            ->getRepository('JamUserBundle:Invitation')
            ->findBy(
                array(
                    'creator' => $this->getUser(),
                    'accepted' => true
                ))
        );

        return array(
            'totalInvitationsCount' => $totalInvitationsCount
        );
    }

    /**
     * @Route("/terms", name="terms", options={"expose"=true})
     * @Template
     */
    public function termsAction(Request $request)
    {
    }

    /**
     * @Route("/terms-raw", name="terms_raw", options={"expose"=true})
     * @Template
     */
    public function termsRawAction(Request $request)
    {
    }

    /**
     * @Route("/about", name="about", options={"expose"=true})
     * @Template
     */
    public function aboutAction(Request $request)
    {
    }

    /**
     * @Route("/sitemapp.xml", defaults={"_format"="xml"}, name="sitemap", Requirements={"_format" = "xml"})
     * @Template("")
     */
    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $urls = array();

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        // add some urls homepage
        $urls[] = array('loc' => $this->get('router')->generate('home'), 'changefreq' => 'weekly', 'priority' => '1.0');
        $urls[] = array('loc' => $this->get('router')->generate('about'), 'changefreq' => 'weekly', 'priority' => '1.0');

        // multi-lang pages
        //foreach($languages as $lang) {
        //    $urls[] = array('loc' => $this->get('router')->generate('home_contact', array('_locale' => $lang)), 'changefreq' => 'monthly', 'priority' => '0.3');
        //}

        // urls from database
        //$urls[] = array('loc' => $this->get('router')->generate('home_product_overview', array('_locale' => 'en')), 'changefreq' => 'weekly', 'priority' => '0.7');
        // service

        foreach ($em->getRepository('JamUserBundle:User')->findAll() as $user) {
            $urls[] = array('loc' => $this->get('router')->generate('musician_profile',
                array('username' => $user->getUsername())), 'priority' => '0.5');
        }

        foreach ($em->getRepository('JamCoreBundle:Jam')->findAll() as $jam) {
            $urls[] = array('loc' => $this->get('router')->generate('view_jam',
                array('slug' => $jam->getSlug())), 'priority' => '0.5');
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'xml');
        return $this->render(
            'JamWebBundle:Home:sitemap.xml.twig',
            array('urls' => $urls, 'hostname' => $baseurl),
            $response
        );

    }

    /**
     * @Route("/win-a-gift-card-to-music-store", name="chance_to_win", options={"expose"=true})
     * @Template
     */
    public function competeAction(Request $request)
    {
    }

}