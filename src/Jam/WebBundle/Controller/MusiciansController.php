<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Route("/musicians/{genre}-{instrument}-{location}", name="musicians_gil")
     * @Template("JamWebBundle:Musicians:index.html.twig")
     */
    public function searchAction()
    {
        return array();
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
