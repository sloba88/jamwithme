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
        $possibleMatches = 0;
        $totalMatches = 0;

        $artistIndex = 8;
        $genresIndex = 5;
        $ageIndex = 4;

        /* calculate artists */
        $matchedIndexes = array();
        foreach ($user->getArtists() AS $k1 => $v1){
            foreach ($me->getArtists() AS $k2 => $v2){
                if (in_array($k2, $matchedIndexes)) continue;
                if ($v1->getId() == $v2->getId()){
                    $compatibility += $artistIndex;
                    $totalMatches ++;
                    //if matched skip it next time
                    array_push($matchedIndexes, $k2);
                }else{
                    //$compatibility -= 2;
                }
            }
        }

        $possibleMatches += min($user->getArtists()->count(), $me->getArtists()->count()) * $artistIndex;

        /* calculate genres */
        $matchedIndexes = array();
        foreach ($user->getGenres() AS $k1 => $v1){
            foreach ($me->getGenres() AS $k2 => $v2){
                if (in_array($k2, $matchedIndexes)) continue;
                if ($v1->getId() == $v2->getId()){
                    $compatibility += $genresIndex;
                    $totalMatches ++;
                    //if matched skip it next time
                    array_push($matchedIndexes, $k2);
                }else{
                    //$compatibility -= 2;
                }
            }
        }

        $possibleMatches += min($user->getGenres()->count(), $me->getGenres()->count()) + $genresIndex;

        /* calculate age */
        if ($user->getAge() && $me->getAge()){
            $ageDiff = abs(intval($user->getAge()) - intval($me->getAge()));
            if ($ageDiff < 5){
                $compatibility += $ageIndex;
                $totalMatches ++;
            }
        }

        $possibleMatches += $ageIndex;

        $matchesIndex = (100 / $possibleMatches) * $compatibility;

        return new JsonResponse(intval($matchesIndex));
    }
}
