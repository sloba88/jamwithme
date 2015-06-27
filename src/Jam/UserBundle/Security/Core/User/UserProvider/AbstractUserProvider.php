<?php

namespace Jam\UserBundle\Security\Core\User\UserProvider;


use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Jam\UserBundle\Entity\UserImage;


abstract class AbstractUserProvider {

    /**
     * @param UserInterface $user
     * @param $firstTimeLogin
     * @param UserResponseInterface $response
     * @return UserInterface
     */
    public abstract function getResourceOwnerData(UserInterface $user, $firstTimeLogin, UserResponseInterface $response);

    /**
     * @param UserInterface $user
     * @param string $pictureUrl
     * @return UserInterface
     */
    protected function setUserPicture(UserInterface $user, $pictureUrl)
    {
        if ($pictureUrl !== '' && $pictureUrl !== null ) {
            $user->setAvatar($pictureUrl);
        }

        return $user;
    }

}