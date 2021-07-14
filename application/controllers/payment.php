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

            // global variable
            global $session;

            // trim all the variables parsed
            $params->amount = substr($params->amount, 0, 6);
            $params->contact = substr($params->contact, 0, 12);
            $params->email = substr($params->email, 0, 60);

            // validate the param variable
            if(!isset($params->param) || (isset($params->param) && !is_array($params->param))) {
                return ["code" => 203, "result" => "Sorry! Param variable is required and must be an array."];
            }

            // validate the amount
            if(!preg_match("/^[0-9.]+$/", $params->amount)) {
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

            // param information
            $param = $params->param;

            // load information
            $load_param = (object) [];

            // if both items were not parsed
            if(!isset($param["student_id"]) && !isset($param["checkout_url"])) {
                return ["code" => 203, "result" => "Missing Parameter! student_id and/or checkout_url is required."];
            }
            
            // append the student id
            if(isset($param["student_id"])) {
                $load_param->student_id = $param["student_id"];
            }

            // append the checkout id
            if(isset($param["checkout_url"])) {
                $load_param->checkout_url = $param["checkout_url"];
            }

            // append the client id to it
            $load_param->clientId = $params->clientId;
            $load_param->client_data = $params->client_data;

            // create a new object
            $paymentObj = load_class("fees", "controllers", $load_param);

            // load the payment information
            $pay_info = $paymentObj->confirm_student_payment_record($load_param);

            // payment information
            if(empty($pay_info)) {
                return ["code" => 203, "data" => "Sorry! The checkout url parsed is either incorrect or has expired."];
            }

            // get the payment information
            $isMultiple = (bool) (count($pay_info) == 1);

            // get the payment form
            $payInit = $pay_info[0];

            // get the balance payable
            $balance = 0;

            // loop through the payment info to get the balance payable
            foreach($pay_info as $pay) {
                $balance += $pay->balance;
            }

            // ensure the amount to be paid is not more than the balance
            if($params->amount > $balance) {
                return ["code" => 203, "data" => "Sorry! The amount to be paid must not exceed the oustanding balance."];
            }

            // set the client data
            $client = $params->client_data;

            // set a new payment reference
            $session->reference_id = empty($session->reference_id) ? random_string("alnum", 14) : $session->reference_id;

            // set the data to return if request was successful
            $data = [
                "data" => [
                    "email" => $params->email,
                    "amount" => $params->amount * 100,
                    "contact" => $params->contact,
                    "subaccount" => $client->client_account,
                    "payment_key" => $this->pk_public_key,
                    "reference" => $session->reference_id,
                    "currency" => $client->client_preferences->labels->currency
                ]
            ];

            return $data;

        } catch(PDOException $e) {}

    }

}
?>