<?php

namespace Jam\WebBundle\Controller;

use Jam\CoreBundle\Form\Type\InvitationType;
use Jam\UserBundle\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jam\CoreBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    /**
     * @Route("/invitation/create", name="invitation_create", options={"expose"=true})
     * @Template()
     */
    public function createAction(Request $request)
    {
        $invitation = new Invitation();

        $form = $this->get('form.factory')->createNamedBuilder(null, InvitationType::class, $invitation);

        return array('form' => $form->getForm()->createView());
    }
}
