<?php

namespace Jam\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CompatibilityController extends Controller
{
    /**
     * @Route("/api/compatibility/{id}", name="api_compatibility", options={"expose"=true})
     * @Template()
     */
    public function getAction($id)
    {

        /* @var $user \Jam\UserBundle\Entity\User */
        /* @var $me \Jam\UserBundle\Entity\User */
        $user = $this->getDoctrine()->getManager()->getReference('JamUserBundle:User', $id);
        $me = $this->getUser();

        $compatibility = 0;

        /* calculate artists */
        foreach ($user->getArtists() AS $k1 => $v1){
            foreach ($me->getArtists() AS $k2 => $v2){
                if ($v1 == $v2){
                    $compatibility += 6;
                }else{
                    $compatibility -= 3;
                }
            }
        }

        /* calculate genres */
        foreach ($user->getGenres() AS $k1 => $v1){
            foreach ($me->getGenres() AS $k2 => $v2){
                if ($v1->getId() == $v2->getId()){
                    $compatibility += 5;
                }else{
                    $compatibility -= 2;
                }
            }
        }

        /* calculate age */
        if ($user->getAge() && $me->getAge()){
            $ageDiff = abs(intval($user->getAge()) - intval($me->getAge()));
            if ($ageDiff < 5){
                $compatibility += 5;
            }
        }

        return new JsonResponse($compatibility);
    }
}
