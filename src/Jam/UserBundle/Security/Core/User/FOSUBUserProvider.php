<?php
namespace Jam\UserBundle\Security\Core\User;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FacebookResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Jam\UserBundle\Entity\UserImage;
use Jam\UserBundle\Security\Core\User\UserProvider\FacebookUserProvider;
use Jam\UserBundle\Security\Core\User\UserProvider\SoundcloudUserProvider;
use Jam\UserBundle\Security\Core\User\UserProvider\UserProviderFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     * @param array $properties Property mapping.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserManagerInterface $userManager, array $properties, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack)
    {
        $this->userManager     = $userManager;
        $this->properties      = array_merge($this->properties, $properties);
        $this->accessor    = PropertyAccess::createPropertyAccessor();
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

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
        //just for testing
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $request = $this->requestStack->getCurrentRequest();
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, new Response()));


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