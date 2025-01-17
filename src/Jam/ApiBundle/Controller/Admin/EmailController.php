<?php

namespace Jam\ApiBundle\Controller\Admin;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

class EmailController extends FOSRestController
{
    /**
     * @Get("/low-profile-fulfillment-email", name="api_notify_profile_fulfillment")
     */
    public function getAllLowProfileAndSendEmails()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        foreach($users AS $user) {
            $compatibility = $user->getProfileFulfilment();
            if ($compatibility < 80) {
                echo $user->getEmail() . ', ';
            }
        }
        exit;
    }
}
