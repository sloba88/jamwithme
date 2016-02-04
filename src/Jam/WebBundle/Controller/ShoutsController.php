<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Entity\Shout;
use Jam\CoreBundle\Form\Type\ShoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ShoutsController extends Controller
{
    /**
     * @Route("/shouts", name="shouts")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Template()
     */
    public function shoutsAction()
    {
        $form = $this->createForm(ShoutType::class, new Shout());

        return array(
            'form' => $form->createView()
        );
    }
}
