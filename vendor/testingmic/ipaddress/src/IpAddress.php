<?php

namespace Testingmic;

use Testingmic\Methods\IpAddressDatabase;
use Testingmic\Methods\IpAddressNetwork;

class IpAddress
{
    /**
     * PDO SQLite3 database instance
     *
     * @var IpAddressDatabase
    **/
    private $oDBInstance;
    
    /**
     * Network tools class instance
     *
     * @var IpAddressNetwork
    **/
    private $oNetwork;

    /**
     * Database name
     *
     * @var string
    **/
    private $database = 'Geoip.db.sqlite';
    
    /**
     * Class Constructor
     *
     * @param string $database
     */
    public function __construct()
    {
        $this->oDBInstance = new IpAddressDatabase($this->database);
        $this->oNetwork = new IpAddressNetwork();
        return $this;
    }

    /**
     * Retrieve country code from given IP address
     *
     * @param string|null $ipAddress
     * @return array
     */
    public function resolve($ipAddress= null)
    {
        $ipAddress || $ipAddress = $this->oNetwork->getIPAddress();
        if (!empty($ipAddress) &&   $this->oNetwork->isIpAddress($ipAddress)) {
            $ipVersion = $this->oNetwork->ipVersion($ipAddress);
            $start = $this->oNetwork->ip2Integer($ipAddress);
            return $this->oDBInstance->fetch($start);
        }
        return false;
    }

    /**
     * @param mixed|null $ipAddress
     * @return bool
     */
    public function isReservedAddress($ipAddress = null)
    {
        $ipAddress || $ipAddress = $this->oNetwork->getIPAddress();
        $countryCode = $this->resolve($ipAddress);
        return !$countryCode || strcasecmp($countryCode['country_code'], 'ZZ') == 0 ;
    }

    /**
     * Update existing records
     *
     * @param int $start
     * @param int $end
     * @param string $country_code
     * @param string $country_name
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @param string $continent
     * @return bool
     */
    public function updateExistingRecords($start, $end, $country_code, $country_name, $city, $latitude, $longitude, $continent)
    {
        return $this->oDBInstance->updateExistingRecords($start, $end, $country_code, $country_name, $city, $latitude, $longitude, $continent);
    }

}
