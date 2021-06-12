<?php 

class Payment extends Myschoolgh {
    

    private $secret_key = "sk_test_3ceb4c33b4b0ea31cb10ef3b41ef05a673758cee";
    private $public_key = "pk_test_0b00163f9532f2e6b27819fa20127b8bd4e2c260";
    
    public function __construct()
    {
        $this->default_email = "emmallob14@gmail.com";

        $this->url["init"] = "https://api.paystack.co/transaction/initialize";
        $this->url["verify"] = "https://api.paystack.co/transaction/verify"; // reference code
        $this->url["list"] = "https://api.paystack.co/transaction"; // transaction id
        $this->url["timeline"] = "https://api.paystack.co/transaction/timeline";
        $this->url["total"] = "https://api.paystack.co/transaction/totals";
        $this->url["export"] = "https://api.paystack.co/transaction/export";

    }

    /**
     * Initialize Payment URL
     * 
     * @param String $params->email
     * @param String $params->amount
     * 
     * @return Array
     */
    public function init(stdClass $params) {

        // set the route
        $params->route = "init";
        
        // set the field parameters
        $fields = [
            "email" => $params->email ?? $this->default_email,
            "amount" => $params->amount ?? 1,
            "callback_url" => "{$this->baseUrl}pay_smstopup"
        ];
        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt_array(
            $ch, 
            array(
                CURLOPT_URL => $this->url[$params->route],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CAINFO => dirname(__FILE__)."\cacert.pem",
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $fields_string,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$this->secret_key}",
                    "Cache-Control: no-cache",
                ]
            )
        );
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = json_decode(curl_exec($ch));
        return [
            "data" => $result
        ];
    }
    /**
     * Initialize Payment URL
     * 
     * @param String $params->email
     * @param String $params->amount
     * 
     * @return Array
     */
    public function get(stdClass $params) {
        
        //open connection
        $ch = curl_init();

        // append the route to the url
        $route = !empty($params->reference) ? "/{$params->reference}" : null;
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url[$params->route].$route,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CAINFO => dirname(__FILE__)."\cacert.pem",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$this->secret_key}",
                "Content-Type: application/json",
                "Cache-Control: no-cache",
            ]
        ));

        //execute post
        $result = json_decode(curl_exec($ch));
        return [
            "data" => $result
        ];
    }
}
?>