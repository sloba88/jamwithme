<?php
namespace Jam\UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Jam\UserBundle\Entity\UserImage;
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

        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setPassword($username);

            $user->setEnabled(true);

            if ($response->getEmail()){
                $user->setEmail($response->getEmail());
            }else{
                $user->setEmail($username);
            }

            if ($response->getProfilePicture()){
                $photo = new UserImage();
                $photo->setPath($response->getProfilePicture());
                $user->addExternalImage($photo);
            }

            $allFields = $response->getResponse();

            if (isset($allFields['gender'])){
                if ($allFields['gender']=='male'){
                    $user->setGender('1');
                }else if($allFields['gender']=='female'){
                    $user->setGender('2');
                }
            }

            if (isset($allFields['name'])){
                $cleanUsername = str_replace(" ", ".", $allFields['name']);
                $user->setUsername($cleanUsername);
            }else{
                $user->setUsername($username);
            }

            $this->userManager->updateUser($user);
            return $user;
        }else{
            if ($response->getProfilePicture()){
                $photo = new UserImage();
                $photo->setPath($response->getProfilePicture());
                $user->addExternalImage($photo);
                $this->userManager->updateUser($user);
            }
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

}