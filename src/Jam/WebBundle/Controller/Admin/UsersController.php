<?php

namespace Jam\WebBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /**
     * @Route("/users-map", name="admin_users_map")
     * @Template
     */
    public function mapAction(Request $request)
    {

    }

    /**
     * @Route("/users-list", name="admin_users_list")
     * @Template
     */
    public function listAction(Request $request)
    {
        $onlyTeachers = $request->query->get('onlyTeachers') ? 1 : 0;
        $location = $request->query->get('location');

        $musicians = $this->getDoctrine()
            ->getRepository('JamUserBundle:User')
            ->createQueryBuilder('m')
            ->leftJoin('m.location', 'location')
            ->where('m.isTeacher = ' .$onlyTeachers);

        if ($location) {
            $musicians = $musicians
                ->andWhere('location.administrative_area_level_3 = ?1')
                ->setParameter(1, $location);
        }

        $musicians = $musicians->getQuery()
            ->getResult();

        $repository = $this->getDoctrine()
            ->getRepository('JamUserBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.isTeacher = 1')
            ->getQuery();

        $teachersCount = $query->getSingleScalarResult();

        return array(
            'musicians' => $musicians,
            'teachersCount' => $teachersCount
        );
    }
}
