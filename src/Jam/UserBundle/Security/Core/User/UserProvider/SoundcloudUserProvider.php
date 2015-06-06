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
        if($firstTimeLogin) {

            $user->setUsername($response->getUsername());

            // email not available via soundcloud api
            $user->setEmail($response->getEmail());
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());
            $user->setAboutMe($response->getDescription());
        }

        $this->setUserPicture($user, $response->getProfilePicture());

        return $user;
    }

}