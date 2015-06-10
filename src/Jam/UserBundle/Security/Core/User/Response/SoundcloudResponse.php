<?php

namespace Jam\UserBundle\Security\Core\User\Response;

use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;

class SoundcloudResponse extends AbstractUserResponse {
    /**
     * Get the unique user identifier.
     *
     * Note that this is not always common known "username" because of implementation
     * in Symfony2 framework. For more details follow link below.
     * @link https://github.com/symfony/symfony/blob/2.1/src/Symfony/Component/Security/Core/User/UserProviderInterface.php#L20-L28
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->response['username'];
    }

    /**
     * Get the username to display.
     *
     * @return string
     */
    public function getNickname()
    {
        // TODO: Implement getNickname() method.
    }

    /**
     * Get the real name of user.
     *
     * @return null|string
     */
    public function getRealName()
    {
        // TODO: Implement getRealName() method.
    }


    public function getEmail()
    {
        // there is no email in soundcloud user response
        return $this->getUsername();
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return ($this->response['first_name'] !== '' && $this->response['first_name'] !== null) ? $this->response['first_name'] : null;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return ($this->response['last_name'] !== '' && $this->response['last_name'] !== null) ? $this->response['last_name'] : null;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return ($this->response['description'] !== '' && $this->response['description'] !== null) ? $this->response['description'] : null;
    }

    /**
     * @return string|null
     */
    public function getProfilePicture()
    {
        return ($this->response['avatar_url'] !== '' && $this->response['avatar_url'] !== null) ? $this->response['avatar_url'] : null;
    }


}