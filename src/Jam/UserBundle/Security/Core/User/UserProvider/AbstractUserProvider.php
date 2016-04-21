<?php

namespace Jam\UserBundle\Security\Core\User\UserProvider;

use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

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
    protected function setUserPicture(UserInterface $user, $avatarUrl)
    {

        if ($avatarUrl !== '' && $avatarUrl !== null) {

            //TODO: this is duplicate
            $test = '/tmp/'.$user->getId().'.jpeg';

            $picture = file_get_contents($avatarUrl);
            file_put_contents($test, $picture);

            $fs = new Filesystem();
            $fs->copy($test, 'uploads/avatars/'.$user->getId().'.jpeg');

            $user->setAvatar($user->getId().'.jpeg');
        }

        return $user;
    }
}