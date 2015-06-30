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
    protected function setUserPicture(UserInterface $user, $pictureUrl)
    {
        if ($pictureUrl !== '' && $pictureUrl !== null ) {

            $test = '/tmp/tmp.jpeg';

            $picture = file_get_contents($pictureUrl);
            file_put_contents($test, $picture);

            $fs = new Filesystem();
            if (!$fs->exists('uploads/avatars/'.$user->getId())){

                try {
                    $fs->mkdir('uploads/avatars/'.$user->getId());
                } catch (IOException $e) {
                    echo "An error occurred while creating your directory at ".$e->getPath();
                }
            }

            $fs->copy($test, 'uploads/avatars/'.$user->getId().'/'.$user->getId().'.jpeg');

            $user->setAvatar($user->getId().'.jpeg');

        }

        return $user;
    }
}