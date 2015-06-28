<?php

namespace Jam\UserBundle\Security\Core\User\UserProvider;

use FOS\UserBundle\Model\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Jam\UserBundle\Entity\UserImage;
use Symfony\Component\HttpFoundation\File\File;

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

            $picture = file_get_contents($pictureUrl);
            file_put_contents('/tmp/tmp.jpg', $picture);
            $file = new File('/tmp/tmp.jpg');

            $userImage = new UserImage();
            $userImage->setType(1);
            $userImage->setFile($file);
            $user->addImage($userImage);
            $user->setAvatar($userImage->getPath());
        }

        return $user;
    }

}