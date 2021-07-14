<?php 

class Payment extends Myschoolgh {
    

    private $secret_key = "sk_test_3ceb4c33b4b0ea31cb10ef3b41ef05a673758cee";
    
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

    /**
     * Init Transaction
     * 
     * @return Array
     */
    public function pay(stdClass $params) {

        try {

            // trim all the variables parsed
            $params->amount = substr($params->amount, 0, 6);
            $params->contact = substr($params->contact, 0, 12);
            $params->email = substr($params->email, 0, 60);

            // validate the amount
            if(!preg_match("/^[0-9]+$/", $params->amount)) {
                return ["code" => 203, "result" => "Sorry! Please enter a valid amount."];
            }

            // validate the contact number
            if(!preg_match("/^[0-9+]+$/", $params->contact)) {
                return ["code" => 203, "result" => "Sorry! Please enter a valid contact number."];
            }

            // validate the email address
            if(!filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
                return ["code" => 203, "result" => "Sorry! Please enter a valid email address."];
            }

            // set the client data
            $client = $params->client_data;

            // set the data to return if request was successful
            $data = [
                "data" => [
                    "email" => $params->email,
                    "amount" => $params->amount * 100,
                    "contact" => $params->contact,
                    "subaccount" => $client->client_account,
                    "payment_key" => $this->pk_public_key,
                    "currency" => $client->client_preferences->labels->currency
                ]
            ];

            return $data;

        } catch(PDOException $e) {}

    }

}
?>