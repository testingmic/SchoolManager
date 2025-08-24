<?php
global $userIpaddress;
/**
 * Get the user IP address
 * 
 * @return string
 */
function getUserIpaddress($cacheObject, $payload) {
    // get the ip address
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }

    // if the ip address is localhost or empty, get the external ip address
    if(($ipaddress == '::1') || empty($ipaddress)) {
        try {
            // get the location cache values
            $ipk = create_cache_key('user', 'ipaddress', ['user_id' => $payload['userUUID'] ?? '']);
            $ip = $cacheObject->get($ipk);

            // if the ip address is set, return it
            if(!empty($ip)) return $ip;

            // get the external ip address
            $externalIp = file_get_contents('https://ifconfig.me');

            // Use regex to extract a valid IPv4 address
            if (preg_match('/\b\d{1,3}(?:\.\d{1,3}){3}\b/', $externalIp, $matches)) {
                $ipaddress = $matches[0];
                $cacheObject->save($ipk, $ipaddress, 'user.ipaddress', null, 60 * 10);
            }
        } catch (\Exception $e) {}
    }

    return $ipaddress;
}

/**
 * Manage the user location
 * 
 * @param array $payload
 * @param object $cacheObject
 * 
 * @return array
 */
function manageUserLocation($payload, $cacheObject) {

    global $userIpaddress;
    $userIpaddress = getUserIpaddress($cacheObject, $payload);

    // set the final location to an empty array
    $payload['finalLocation'] = [];
    $payload['ipaddress'] = $userIpaddress;

    if(empty($payload['userUUID']) && empty($payload['fingerprint'])) {
        return $payload;
    }

    // if the userUUID is not set, use the fingerprint
    $payload['userUUID'] = empty($payload['userUUID']) ? $payload['fingerprint'] : $payload['userUUID'];

    if(!empty($payload['longitude']) && strlen($payload['longitude']) == 4) {
        $payload['longitude'] = '';
    }

    if(!empty($payload['latitude']) && strlen($payload['latitude']) == 4) {
        $payload['latitude'] = '';
    }

    $locationFound = false;

    // if the longitude and latitude are not set or if set and no location was found
    if((!empty($payload['longitude']) && !empty($payload['latitude']))) {

        // get the cache key
        $cacheKey = create_cache_key('user', 'location', ['latitude' => $payload['latitude'], 'longitude' => $payload['longitude']]);
        $locationInfo = $cacheObject->get($cacheKey);

        // get the data to use
        $dataToUse = !empty($locationInfo) ? $locationInfo : getLocationByIP($payload['longitude'], $payload['latitude']);

        // set the location loop
        $locationLoop = [
            'location' => [
                'key' => 'results',
                'data' => 'components'
            ],
            'geoapify' => [
                'key' => 'features',
                'data' => 'properties'
            ]
        ];

        // loop through the location loop
        foreach($locationLoop as $key => $value) {

            // get the city
            $theCity = $dataToUse[$value['key']][0][$value['data']]['city'] ?? (
                $dataToUse[$value['key']][0][$value['data']]['town'] ?? (
                    $dataToUse[$value['key']][0][$value['data']]['suburb'] ?? null
                )
            );

            // check if the city is set
            if(!empty($theCity)) {

                $ivalue = $value['data'];

                if(!empty($theCity)) {
                    $usage = $key;
                    $payload['city'] = $theCity;
                    $payload['country'] = $dataToUse[$value['key']][0][$ivalue]['country'] ?? null;
                    $payload['district'] = $dataToUse[$value['key']][0][$ivalue]['county'] ?? $payload['city'];
                    $cacheObject->save($cacheKey, $dataToUse, 'user.location', null, 60 * 60);
                    $locationFound = true;
                    break;
                }
            }
        }

    }

    if(empty($payload['longitude']) && empty($payload['latitude']) || !$locationFound) {

        // get the location cache values
        $cacheKey = create_cache_key('user', 'location', ['user_id' => $payload['userUUID'].$payload['ipaddress']]);
        $locationInfo = $cacheObject->get($cacheKey);

        // get the data to use
        $dataToUse = !empty($locationInfo) ? $locationInfo : getLocationByIP();

        if(!empty($dataToUse)) {
            $usage = 'ipaddress';
            $locs = !empty($dataToUse['loc']) ? explode(',', $dataToUse['loc']) : [$dataToUse['lat'], $dataToUse['lon']];
            $payload['latitude'] = $locs[0];
            $payload['longitude'] = $locs[1];
            $payload['city'] = $dataToUse['city'];
            $payload['country'] = $dataToUse['country'];
            $payload['district'] = $dataToUse['regionName'] ?? $dataToUse['region'];
            $cacheObject->save($cacheKey, $dataToUse, 'user.location', null, 60 * 60);
        }

    }

    $final = [
        'mode' => $usage,
        'city' => $payload['city'] ?? '',
        'district' => $payload['district'] ?? '',
        'country' => $payload['country'] ?? '',
        'latitude' => $payload['latitude'] ?? '',
        'longitude' => $payload['longitude'] ?? '',
    ];

    $payload['finalLocation'] = $final;

    return $payload;
}

/**
 * Get the location by IP
 * 
 * @return array
 */
function getLocationByIP($longitude = null, $latitude = null, $useGeocode = false) {

    global $userIpaddress;

    // get the ipinfo and opencage keys
    // $ipInfoKey = explode(';', configs('ipinfo'));
    $opencageKey = explode(';', configs('opencage'));
    $geocodeKey = explode(';', configs('geocode'));
    $proIpKey = explode(';', configs('proip'));

    // Fetch location data from ipapi.co
    // $url = "https://ipinfo.io/{$userIpaddress}?token=" . trim($ipInfoKey[0]);
    $url = "http://ip-api.com/json/{$userIpaddress}";
    $proIpUrl = "https://pro.ip-api.com/json/{$userIpaddress}?key=" . trim($proIpKey[0]);
    $reverseUrl = "https://api.opencagedata.com/geocode/v1/json?q={$latitude},{$longitude}&pretty=1&key=" . trim($opencageKey[0]);
    $geocodeUrl = "https://api.geoapify.com/v1/geocode/reverse?lat={$latitude}&lon={$longitude}&apiKey=" . trim($geocodeKey[0]);
    $backupUrl = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&addressdetails=1&zoom=10";

    // set the url path
    $urlPath = empty($longitude) && empty($latitude) ? $url : $reverseUrl;

    if($useGeocode) {
        $urlPath = $geocodeUrl;
    }

    // use curl to get the data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlPath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // set the user agent headers
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    // set the timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    // set the connect timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    // execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response !== false) {
        $data = json_decode($response, true);
        $data['api_url'] = $urlPath;
        return $data;
    }
}
?>