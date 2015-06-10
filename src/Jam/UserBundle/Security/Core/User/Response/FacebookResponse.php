<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 6/6/15
 * Time: 8:03 PM
 */

namespace Jam\UserBundle\Security\Core\User\Response;


use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;

class FacebookResponse extends AbstractUserResponse {
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
        return $this->response['id'];
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

    /**
     * @return string
     */
    public function getEmail()
    {
        return ($this->response['email'] === null || $this->response['email'] === '') ? $this->getUsername() : $this->response['email'];
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->response['name'];
    }

    /**
     * @return mixed|null
     */
    public function getFirstName()
    {
        return $this->getNameComponent('firstName');
    }

    /**
     * @return mixed|null
     */
    public function getLastName()
    {
        return $this->getNameComponent('lastName');
    }

    /**
     * @return string
     */
    public function getCleanUsername()
    {
        return ($this->getNameComponent('username') === null) ? $this->getUsername() : $this->getNameComponent('username');
    }

    public function getGender()
    {
        return ($this->response['gender'] === '' || $this->response['gender'] === null) ? null : $this->response['gender'];
    }

    public function getProfilePicture()
    {
        return ($this->response['picture']['data']['url'] !== null && $this->response['picture']['data']['url'] !== '') ? $this->response['picture']['data']['url'] : null;
    }


    protected function getNameComponent($component)
    {
        $returnName = null;
        if ($this->getFullName() !== '' && $this->getFullName() !== null) {

            $userNameArray = explode(' ', $this->getFullName());

            if (count($userNameArray) === 2) {
                // probably first name and last name
                if ($component === 'firstName') {
                    $returnName = $userNameArray[0];
                } elseif ($component === 'lastName') {
                    $returnName = $userNameArray[1];
                } elseif ($component === 'username') {
                    $returnName = str_replace(" ", ".", strtolower($this->getFullName()));
                }
            }
        }

        return $returnName;
    }




}