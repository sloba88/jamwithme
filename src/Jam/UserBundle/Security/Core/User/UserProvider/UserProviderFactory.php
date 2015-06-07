<?php
namespace Jam\UserBundle\Security\Core\User\UserProvider;


class UserProviderFactory
{

    /**
     * @param $userProviderName
     * @return AbstractUserProvider
     * @throws \Exception
     */
    public static function create($userProviderName)
    {
        $userProvider = null;
        switch($userProviderName)
        {
            case 'facebook':
                $userProvider = new FacebookUserProvider();
                break;
            case 'soundcloud':
                $userProvider = new SoundcloudUserProvider();
                break;
            default:
                throw new \Exception('No defined resource owner user provider');
        }

        return $userProvider;
    }

}