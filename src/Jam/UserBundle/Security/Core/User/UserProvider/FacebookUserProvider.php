<?php


namespace Jam\UserBundle\Security\Core\User\UserProvider;

use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;


class FacebookUserProvider extends AbstractUserProvider {

    public function getResourceOwnerData(UserInterface $user, $firstTimeLogin, UserResponseInterface $response)
    {
        $responseData = $response->getResponse();

        if($firstTimeLogin){

            if($responseData['email'] !== '' && $responseData['email'] !== null){
                $user->setEmail($responseData['email']);
            } else {
                $user->setEmail($user->getUsername());
            }

            if($responseData['gender'] !== '' && $responseData['gender'] !== null){
                $user->setGender($responseData['gender']);
            }

            $this->setUserName($user, $responseData);
        }

        $this->setUserPicture($user, $responseData['picture']['data']['url']);

        return $user;
    }

    private function setUserName(UserInterface $user, array $responseData)
    {

        if($responseData['name'] !== '' && $responseData['name'] !== null){

            $userNameArray = explode(' ', $responseData['name']);

            if(count($userNameArray) === 2){
                // probably first name and last name
                $user->setFirstName($userNameArray[0]);
                $user->setLastName($userNameArray[1]);

                $cleanUsername = str_replace(" ", ".", strtolower($responseData['name']));
                $user->setUsername($cleanUsername);
            }
        }
    }

}