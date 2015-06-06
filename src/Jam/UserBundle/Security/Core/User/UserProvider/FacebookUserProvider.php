<?php


namespace Jam\UserBundle\Security\Core\User\UserProvider;

use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;


class FacebookUserProvider extends AbstractUserProvider {

    public function getResourceOwnerData(UserInterface $user, $firstTimeLogin, UserResponseInterface $response)
    {
        if($firstTimeLogin){

            $user->setEmail($response->getEmail());
            $user->setGender($response->getGender());

            //$this->setUserName($user, $responseData);
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());
            $user->setUsername($response->getCleanUsername());
        }

        $this->setUserPicture($user, $response->getProfilePicture());

        return $user;
    }
}