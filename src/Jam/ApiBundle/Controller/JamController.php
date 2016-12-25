<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Jam\CoreBundle\Entity\Jam;
use FOS\RestBundle\Controller\Annotations\Get;

class JamController extends FOSRestController
{
    /**
     * @Get("/jam/{jamId}/find-matching-musicians", name="jam_find_matching_musicians")
     */
    public function findMatchingMusiciansAction($jamId)
    {
        $jam = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->find($jamId);

        $musicians = $this->get('search.musicians')->getMusiciansByJam($jam);

        $musicians_data = array();
        foreach ($musicians AS $m) {
            $data_array = array(
                'username' => $m->getUsername(),
                'displayName' => $m->getDisplayName()
            );

            array_push($musicians_data, $data_array);
        }

        return array(
            'result' => $musicians_data
        );
    }

    /**
     * @Get("/jam/find-all-matching-musicians", name="jam_find_all_matching_musicians")
     */
    public function getAllMatchingMusicians()
    {
        $jams = $this->getDoctrine()
            ->getRepository('JamCoreBundle:Jam')
            ->findBy(array('status' => 1));

        //TODO: count only unique emails and for every user send only one email about all potential jams
        $totalEmails = 0;
        foreach ($jams AS $jam) {
            $musicians = $this->get('search.musicians')->getMusiciansByJam($jam);

            echo $jam->getName() . ' - ' . count($musicians) . '<br />';

            $totalEmails += count($musicians);
        }

        echo '<br /><br />';
        echo $totalEmails;

        exit;
    }
}
