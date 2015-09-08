<?php

namespace Jam\LocationBundle\EventListener;

use FOS\UserBundle\Doctrine\UserManager;
use Jam\LocationBundle\Entity\Location;
use Jam\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LocationSetListener {

    protected $tokenStorage;

    protected $userManager;

    public function __construct(TokenStorage $tokenStorage, UserManager $userManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
    }

    public function onKernelRequest()
    {
        if (is_object($this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof User) {

                if ($user->getLocation() == null) {
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    $location = $this->geoCheckIP($ip);
                    $user->setLocation($location);
                    $this->userManager->updateUser($user);
                }
            }
        }

        //return $response;
    }

    public function geoCheckIP($ip)
    {
        //check, if the provided ip is valid
        if(!filter_var($ip, FILTER_VALIDATE_IP))
        {
            //"IP is not valid"
        }

        //contact ip-server
        $response=@file_get_contents('http://www.netip.de/search?query='.$ip);
        if (empty($response))
        {
            //Error contacting Geo-IP-Server
        }

        //Array containing all regex-patterns necessary to extract ip-geoinfo from page
        $patterns=array();
        $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
        //$patterns["country"] = '#Country: (.*?)&nbsp;#i';
        $patterns["state"] = '#State/Region: (.*?)<br#i';
        $patterns["town"] = '#City: (.*?)<br#i';

        //Array where results will be stored
        $ipInfo=array();

        //check response from ipserver for above patterns
        foreach ($patterns as $key => $pattern)
        {
            //store the result in array
            $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : '';
        }

        return $this->geoCode(implode(", ", $ipInfo));
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

        return $ret;
    }

    protected function getElement(array $array, $key, $default='')
    {
        if(isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

}