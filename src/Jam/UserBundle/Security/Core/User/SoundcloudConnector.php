<?php

namespace Jam\UserBundle\Security\Core\User;

use Guzzle\Service\Client;
use Jam\UserBundle\Entity\User;
use Jam\UserBundle\Entity\UserImage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SoundcloudConnector {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $soundcloudApiUrl;

    /**
     * @var string
     */
    private $soundcloudClientId;

    /**
     * @var string
     */
    private $soundcloudClientSecret;

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function setSoundcloudData($soundcloudConnectData)
    {
        $this->soundcloudApiUrl = $soundcloudConnectData[0];
        $this->soundcloudClientId = $soundcloudConnectData[1];
        $this->soundcloudClientSecret = $soundcloudConnectData[2];
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Get soundcloud connect url that returns code for retrieving soundcloud token
     *
     * @return string
     */
    public function getSouncloudConnectUrl()
    {
        $redirectUrl = $this->router->generate('soundcloud_token', array(), true);

        return $this->soundcloudApiUrl.'connect?'.http_build_query(array(
            'client_id' => $this->soundcloudClientId,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'scope' => 'non-expiring'
        ));
    }

    /**
     * Get soundcloud token by soundcloud code
     *
     * @param string $code
     * @return \stdClass
     */
    public function getSoundcloudToken($code)
    {
        $this->client->setBaseUrl($this->soundcloudApiUrl);

        $tokenRedirectUrl = $this->router->generate('soundcloud_token', array(), true);

        try {
            $tokenRequest = $this->client->post('oauth2/token', null, array(
                'grant_type' => 'authorization_code',
                'client_id' => $this->soundcloudClientId,
                'client_secret' => $this->soundcloudClientSecret,
                'redirect_uri' => $tokenRedirectUrl,
                'code' => $code
            ));

            $tokenResponse = $tokenRequest->send();
            $tokenData = json_decode($tokenResponse->getBody(true));

        } catch(\Exception $exception) {
            throw new $exception;
        }

        return $tokenData;
    }

    /**
     * Get soundcloud user data via soundcloud api
     *
     * @param string $token
     * @return \stdClass
     * @throws \Exception
     */
    public function getSoundcloudUser($token)
    {
        $soundcloudUserReq = $this->client->get('me.json?oauth_token='.$token);

        $soundcloudUserResponse = $soundcloudUserReq->send();
        $soundcloudUserData = json_decode($soundcloudUserResponse->getBody(true));

        if (!is_object($soundcloudUserData)) {
            throw new \Exception('Unable to retrieve soundcloud user data');
        }

        return $soundcloudUserData;
    }

    /**
     * Create a new user add data from soundcloud response and return it
     *
     * @param \stdClass $soundcloudUserData
     * @param \stdClass $soundcloudTokenData
     * @return User
     */
    public function setNewSoundcloudUser(\stdClass $soundcloudUserData, \stdClass $soundcloudTokenData)
    {
        $user = new User();

        $user->setSoundcloudAccessToken($soundcloudTokenData->access_token);
        $user->setSoundcloudId($soundcloudUserData->id);
        $user->setUsername($soundcloudUserData->username);
        $user->setPlainPassword($user->getUsername());
        $user->setEnabled(true);

        $user->setEmail($user->getUsername());

        if ($soundcloudUserData->first_name !== '' && $soundcloudUserData->first_name !== null) {
            $user->setFirstName($soundcloudUserData->first_name);
        }

        if ($soundcloudUserData->last_name !== '' && $soundcloudUserData->last_name !== null) {
            $user->setLastName($soundcloudUserData->last_name);
        }

        if ($soundcloudUserData->description !== '' && $soundcloudUserData->description !== null) {
            $user->setAboutMe($soundcloudUserData->description);
        }

        if ($soundcloudUserData->avatar_url !== '' && $soundcloudUserData->avatar_url !== null) {
            $userPicture = new UserImage();
            $userPicture->setPath($soundcloudUserData->avatar_url);
            $user->addExternalImage($userPicture);
        }

        return $user;
    }

}