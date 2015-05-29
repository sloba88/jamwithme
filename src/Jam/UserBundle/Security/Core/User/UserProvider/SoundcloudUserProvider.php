<?php
namespace Jam\UserBundle\Security\Core\User\UserProvider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Jam\UserBundle\Entity\UserImage;
use FOS\UserBundle\Model\UserInterface;

class SoundcloudUserProvider extends AbstractUserProvider {

    /**
     * @param UserInterface $user
     * @param boolean $firstTimeLogin
     * @param UserResponseInterface $response
     * @return UserInterface
     */
    public function getResourceOwnerData(UserInterface $user, $firstTimeLogin, UserResponseInterface $response)
    {

        $responseData = $response->getResponse();

        if($firstTimeLogin) {

            $user->setUsername($responseData['username']);

            // email not available via soundcloud api
            $user->setEmail($user->getUsername());

            if ($responseData['first_name'] !== '' && $responseData['first_name'] !== null) {
                $user->setFirstName($responseData['first_name']);
            }

            if ($responseData['last_name'] !== '' && $responseData['last_name'] !== null) {
                $user->setLastName($responseData['last_name']);
            }

            if ($responseData['description'] !== '' && $responseData['description'] !== null) {
                $user->setAboutMe($responseData['description']);
            }
        }

        $this->setUserPicture($user, $responseData['avatar_url']);

        return $user;
    }

}