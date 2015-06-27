<?php
namespace Jam\UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FacebookResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Jam\UserBundle\Entity\UserImage;
use Jam\UserBundle\Security\Core\User\UserProvider\FacebookUserProvider;
use Jam\UserBundle\Security\Core\User\UserProvider\SoundcloudUserProvider;
use Jam\UserBundle\Security\Core\User\UserProvider\UserProviderFactory;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        $service = $response->getResourceOwner()->getName();

        $firstTimeLogin = (null === $user) ? true : false;

        $setter = 'set'.ucfirst($service);
        $setterToken = $setter.'AccessToken';

        if ($firstTimeLogin){

            $setterId = $setter.'Id';

            // create new user here
            $user = $this->userManager->createUser();

            $user->setUsername($username);

            $user->$setterId($user->getUsername());
            $user->$setterToken($response->getAccessToken());

            $user->setPlainPassword($username);
            $user->setEnabled(true);
        }

        $userProvider = UserProviderFactory::create($service);
        $userProvider->getResourceOwnerData($user, $firstTimeLogin, $response);

        $this->userManager->updateUser($user);

        if($firstTimeLogin === false){
            $user = parent::loadUserByOAuthUserResponse($response);
            $user->$setterToken($response->getAccessToken());
        }

        return $user;
    }

}