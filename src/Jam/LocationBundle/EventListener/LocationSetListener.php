<?php

namespace Jam\LocationBundle\EventListener;

use FOS\UserBundle\Doctrine\UserManager;
use Jam\LocationBundle\Entity\Location;
use Jam\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LocationSetListener {

    protected $tokenStorage;

    protected $userManager;

    public function __construct(TokenStorage $tokenStorage, UserManager $userManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = new Response();

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (is_object($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof User) {

                if ($user->getLocation() == null) {
                    $location = $this->geoCheckIP($ip);
                    if ($location) {
                        $location->setIsTemporary(true);
                        $user->setLocation($location);
                        $this->userManager->updateUser($user);
                    } else {
                        //couldn't find user by ip
                    }
                }
            } else {
                //not authenticated
                //get country to set up language

                if ($event->getRequestType() == 1 && !$request->query->get('lang') && !$request->cookies->get('language')) {
                    $location = $this->geoCheckIP($ip);

                    if ($location) {
                        if ($location->getCountry() == 'Finland') {
                            $request->getSession()->set('_locale', 'fi');
                            $request->setLocale($request->getSession()->get('_locale', 'fi'));
                        }
                    } else {
                        $request->getSession()->set('_locale', 'en');
                        $request->setLocale($request->getSession()->get('_locale', 'en'));
                    }

                    $response->headers->setCookie(new Cookie('language', $request->getLocale()));
                }
            }
        }

        return $response;
    }

    public function geoCheckIP($ip)
    {
        //check, if the provided ip is valid
        if(!filter_var($ip, FILTER_VALIDATE_IP))
        {
            //"IP is not valid"
        }

        $ctx = stream_context_create(array('http'=>
            array(
                'method' => 'GET',
                'timeout' => 5,
            )
        ));

        $response = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip, false, $ctx));

        if ($response) {
            $ipInfo[0] = $response['geoplugin_city'];
            $ipInfo[1] = $response['geoplugin_region'];
            $ipInfo[2] = $response['geoplugin_regionName'];
            $ipInfo[3] = $response['geoplugin_countryName'];

            return $this->geoCode(implode(", ", $ipInfo));
        } else {
            return null;
        }
    }

    protected function geoCode($address)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://nominatim.openstreetmap.org/search?q='.urlencode($address).'&format=json&addressdetails=1&accept-language=en');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonOutput = json_decode($response, true);
        if(!$jsonOutput) {
            //TODO exception
            return null;
        }
        else if(count($jsonOutput)<=0) {
            return null;
        }
        else if(count($jsonOutput)>1) {
            return $this->convertToLocationObject($jsonOutput[0]);
        }
        else {
            return $this->convertToLocationObject($jsonOutput[0]);
        }
    }

    protected function convertToLocationObject($result)
    {
        $ret = new Location();

        $ret->setRoute($this->getElement($result['address'], 'road'));
        $ret->setNeighborhood($this->getElement($result['address'], 'suburb'));
        $ret->setAdministrativeAreaLevel3($this->getElement($result['address'], 'city'));
        $ret->setZip($this->getElement($result['address'], 'postcode'));

        $ret->setCountry($this->getElement($result['address'], 'country'));
        $ret->setLat($result['lat']);
        $ret->setLng($result['lon']);
        $ret->setAddress($result['display_name']);
        $ret->setIsTemporary(false);

        return $ret;
    }

    protected function getElement(array $array, $key, $default='')
    {
        if(isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

    public function reverseGeoCode($coords)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://nominatim.openstreetmap.org/reverse?format=json&lat='.$coords[0].'&lon='. $coords[1].'&zoom=18&addressdetails=1&accept-language=en&email=stanic.slobodan88@gmail.com');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonOutput = json_decode($response, true);

        if(!$jsonOutput) {
            //TODO exception
        }
        else if(count($jsonOutput)<=0) {
            return null;
        }
        else if(count($jsonOutput)>1) {
            return $this->convertToLocationObject($jsonOutput);
        }
        else {
            return $this->convertToLocationObject($jsonOutput);
        }
    }

}