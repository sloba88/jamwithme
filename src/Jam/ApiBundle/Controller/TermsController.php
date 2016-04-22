<?php

namespace Jam\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;

class TermsController extends FOSRestController
{
    /**
     * @Get("/accept-terms", name="accept-terms", options={"expose"=true})
     */
    public function acceptTermsAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');

        if ($this->getUser() !== null) {
            $this->getUser()->setAcceptedTerms(true);
            $userManager->updateUser($this->getUser());

            return $this->redirectToRoute('home');
        }
    }
}
