<?php 
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Payment extends Myschoolgh {

    private $pk_secret_key = "";
    public $url;
    
    public function __construct() {

        global $myschoolgh, $session;

        $this->db = $myschoolgh;
        $this->session = $session;

        $this->url["init"] = "https://api.paystack.co/transaction/initialize";
        $this->url["verify"] = "https://api.paystack.co/transaction/verify"; // reference code
        $this->url["list"] = "https://api.paystack.co/transaction"; // transaction id
        $this->url["timeline"] = "https://api.paystack.co/transaction/timeline";
        $this->url["total"] = "https://api.paystack.co/transaction/totals";
        $this->url["export"] = "https://api.paystack.co/transaction/export";

    }

    /**
     * Convert Amount to Words
     * 
     * @param Float     $params->amount
     *
     * @return Array
     **/
    public function convert_amount(stdClass $params) {

        // amount value
        $amount = [];

        // convert the amount to an array
        $params->amount = $this->stringToArray($params->amount);

        // loop through the amount list
        if(is_array($params->amount)) {

            // loop through the array list
            foreach($params->amount as $_amount) {
                // ensure the amount is valid numeric integer
                if(!preg_match("/^[0-9.]+$/", $_amount)) {
                    $amount[$_amount] = "NIL";
                } else {
                    // return the conversion
                    $amount[$_amount] = $this->amount_to_words($_amount);
                }
            }
        }

        // return the conversion
        return $amount;

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
            "email" => $params->email ?? $this->default_pay_email,
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
                    "Authorization: Bearer {$this->pk_secret_key}",
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
                "Authorization: Bearer {$this->pk_secret_key}",
                "Content-Type: application/json",
                "Cache-Control: no-cache",
            ]
        ));

        //execute post
        $data = curl_exec($ch);

        $result = json_decode($data);
        
        return ["data" => $result];

    }

    /**
     * Init Transaction
     * 
     * @param   Array   $params->param        [clientId, student_id, checkout_url]
     * @param   String  $params->email
     * @param   String  $params->contact
     * @param   Float   $params->amount
     * 
     * @return Array
     */
    public function pay(stdClass $params) {

        try {

            // global variable
            global $session, $defaultClientData;

            // set the client data
            $client = $params->client_data;

            // if the client subaccount is empty then end the query
            if(empty($client->client_account)) {
                return ["code" => 203, "data" => "Sorry! {$client->client_name} have not yet subscribed to use the e-Payment Module."];
            }

            // trim all the variables parsed
            $params->amount = substr($params->amount, 0, 6);
            $params->contact = substr($params->contact, 0, 13);
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
            if(isset($params->contact) && !preg_match("/^[0-9+]+$/", $params->contact)) {
                return ["code" => 203, "result" => "Sorry! Please enter a valid contact number."];
            }

            // validate the email address
            if(!filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
                return ["code" => 203, "result" => "Sorry! Please enter a valid email address."];
            }

            // param information
            $params->email = strtolower($params->email);
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

            // set the data
            $_data = [];
            $_data["clientId"] = $params->clientId;
            $_data["subaccount"] = $client->client_account;
            $_data["amount"] = $params->amount;
            $_data["userId"] = $payInit->student_id;
            $_data["student_id"] = $payInit->student_id;
            $_data["checkout_url"] = $param["checkout_url"] ?? "";
            $_data["userName"] = $payInit->student_details->student_name ?? null;
            $_data["academic_term"] = $payInit->academic_term;
            $_data["academic_year"] = $payInit->academic_year;
            $_data["contact_number"] = $params->contact ?? null;
            $_data["email_address"] = $params->email ?? null;
            
            // convert back to object
            $_data = (object) $_data;

            // create a new transaction id
            $transaction_id = empty($session->self_pay_reference_id) ? "MSG" . random_string("numeric", 15) : $session->self_pay_reference_id;
            $session->self_pay_reference_id = $transaction_id;

            // set a new payment reference
            $session->user_contact = $params->contact ?? null;

            // confirm if the transaction id already exists
            if(!empty($this->pushQuery("id", "transaction_logs", "transaction_id='{$transaction_id}' AND state='Pending' LIMIT 1"))) {
                // log the information
                $stmt = $this->db->prepare("UPDATE transaction_logs 
                    SET amount = ?, transaction_data = ? WHERE transaction_id = ? LIMIT 1");
                $stmt->execute([$params->amount, json_encode($_data), $transaction_id]);
            } else {
                // log the information
                $stmt = $this->db->prepare("INSERT INTO transaction_logs SET 
                    client_id = ?, transaction_id = ?, reference_id = ?, endpoint = ?, amount = ?, 
                    transaction_data = ?, state = ?, created_by = '{$params->userId}'");
                $stmt->execute([$params->clientId, $transaction_id, $transaction_id, "fees", $params->amount, 
                    json_encode($_data), "Pending"
                ]);
            }

            // set the data to return if request was successful
            $data = [
                "data" => [
                    "email" => !empty($params->email) ? $params->email : $params->default_pay_email,
                    "amount" => $params->amount * 100,
                    "contact" => $params->contact ?? null,
                    "subaccount" => $client->client_account,
                    "payment_key" => $this->pk_public_key,
                    "reference" => $session->self_pay_reference_id,
                    "currency" => $client->client_preferences->labels->currency
                ]
            ];

            return $data;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Verify Transaction
     * 
     * @param   Array   $params->param          [clientId, student_id, checkout_url]
     * @param   String  $params->reference_id
     * @param   String  $params->transaction_id
     * 
     * @return Array
     */
    public function verify(stdClass $params) {

        try {

            // begin transaction
            $this->db->beginTransaction();
            
            // global variable
            global $session;
            
            // validate the param variable
            if(!isset($params->param) || (isset($params->param) && !is_array($params->param))) {
                return ["code" => 203, "result" => "Missing Parameter! Param variable is required and must be an array."];
            }

            // params
            $param = $params->param;

            // if both items were not parsed
            if(!isset($param["student_id"]) && !isset($param["checkout_url"])) {
                return ["code" => 203, "result" => "Missing Parameter! student_id and/or checkout_url is required."];
            }

            // confirm the reference_id exists
            if(empty($session->self_pay_reference_id)) {
                return ["code" => 203, "result" => "Sorry! Payment validation unsuccessful."];
            }

            // academic year
            $clientPref = $params->client_data->client_preferences;
            $academic_year = $clientPref->academics->academic_year;
            $academic_term = $clientPref->academics->academic_term;

            // payment parameter
            $data = (object) ["reference" => $params->reference_id, "route" => "verify"];

            // confirm the payment
            $payment_check = empty($params->paystack_data) ? $this->get($data) : $params->paystack_data;

            // check
            if(empty($payment_check["data"])) {
                return ["code" => 203, "data" => "Sorry! We could not validate the transaction."];
            }

            // if payment status is true
            if($payment_check["data"]->status === true) {

                // confirm the reference_id
                if($session->self_pay_reference_id !== $payment_check["data"]->data->reference) {
                    return ["code" => 203, "result" => "Sorry! Payment validation unsuccessful."];
                }

                // end query if the $params->subaccount was not parsed for verification
                if(empty($params->subaccount)) {
                    return ["code" => 203, "data" => "Sorry! We could not validate this transaction."];
                }

                // if the subaccount was parsed
                if(isset($payment_check["data"]->data->subaccount)) {
                    // if the client subaccount is empty then end the query
                    if($payment_check["data"]->data->subaccount->subaccount_code !== $params->subaccount) {
                        return ["code" => 203, "data" => "Sorry! We could not validate this transaction."];
                    }
                }

                // set the amount 
                $amount = $payment_check["data"]->data->amount / 100;

                // get the metadata url
                $meta_data = $payment_check["data"]->data->metadata->referrer;
                
                // clean metadata
                $clean_meta = str_ireplace(["http://", "https://", "localhost/myschoolgh"], ["", "", "app.myschoolgh.com"], $meta_data);

                // split the referrer information
                $split = explode("/", $clean_meta);
                
                // count the partitions
                $count = count($split);
                $student_param = (object) ["clientId" => $params->clientId];

                // get the items from the back
                $last = $split[$count-1];

                // get the last parameter
                if(isset($split[5]) && ($last == "checkout")) {
                    // set the client id and the checkout url
                    $student_param->clientId = $split[2];
                    $student_param->checkout_url = $split[4];
                }
                // if the checkout url was not parsed
                elseif(!isset($split[5]) && isset($split[4])) {
                    // set the client id and the student id
                    $student_param->clientId = $split[2];
                    $student_param->student_id = $split[4];
                } elseif(isset($split[5]) && ($split[4] == "fees")) {
                    $student_param->clientId = $split[3];
                    $student_param->student_id = $split[5];
                }

                // append the client id to it
                $student_param->client_data = $params->client_data;

                // create a new object
                $paymentObj = load_class("fees", "controllers", $student_param);

                // get the student payment information
                $student_param->clean_payment_info = true;
                $paymentRecord = $paymentObj->confirm_student_payment_record($student_param);

                /** If no allocation record was found */
                if(empty($paymentRecord)) {
                    return ["code" => 203, "data" => "Sorry! An invalid checkout url was parsed for processing."];
                }

                // confirm if the data parsed is an array
                if(is_array($paymentRecord)) {

                    // initials    
                    $amount_due = 0;
                    $total_amount_paid = 0;
                    $balance = 0;

                    $fees_list = [];
                    $amount_paid = [];
                    $paying = $amount;

                    // loop through the allocations list
                    foreach($paymentRecord as $fee) {

                        // add up to the values
                        $balance += $fee->balance;
                        $amount_due += $fee->amount_due;
                        $total_amount_paid += $fee->amount_paid;

                        // algorithm to get the items being paid for
                        if($paying > 0) {
                            if(!$fee->exempted) {
                                if(($fee->balance < $paying) || ($fee->balance == $paying)) {
                                    $paying = $paying - $fee->balance;
                                    $fees_list[$fee->category_id] = 0;
                                    // if the paid status is not equal to one
                                    if($fee->balance != 0.00) {
                                        $amount_paid[$fee->category_id] = $fee->balance;
                                    }
                                } elseif($fee->balance > $paying) {
                                    $n_value = $fee->balance - $paying;
                                    $amount_paid[$fee->category_id] = $paying;
                                    $fees_list[$fee->category_id] = $n_value;
                                    $paying = 0;
                                } else {
                                    $n_value = $fee->balance - $paying;
                                    $amount_paid[$fee->category_id] = $paying;
                                    $fees_list[$fee->category_id] = $n_value;
                                    $paying -= $fee->balance; 
                                }
                            }
                        }
                    }

                    /* Outstanding balance calculator */
                    $outstandingBalance = $balance - $amount;
                    $totalPayment = $total_amount_paid + $amount;
                    
                    // set the paid status
                    $paid_status = ((round($totalPayment) === round($outstandingBalance)) || (round($totalPayment) > round($outstandingBalance))) ? 1 : 2;

                } else {
                    /* Outstanding balance calculator */
                    $outstandingBalance = $paymentRecord->balance - $amount;
                    $totalPayment = $paymentRecord->amount_paid + $amount;

                    // set the paid status
                    $paid_status = ((round($totalPayment) === round($paymentRecord->amount_due)) || (round($totalPayment) > round($paymentRecord->amount_due))) ? 1 : 2;
                }

                /* Confirm if the user has any credits */
                if($outstandingBalance < 0) {
                    $creditBalance = $outstandingBalance * -1;
                    $outstandingBalance = 0;
                }

                // get the currency
                $append_sql = "";
                $payment_method = "MoMo_Card";
                $currency = $payment_check["data"]->data->currency;
                $email_address = $payment_check["data"]->data->customer->email;

                // if the payment method is momo or card payment
                $append_sql .= ", paidin_by='{$email_address}', paidin_contact='".($session->user_contact ?? null)."'";

                // append the $this->session->e_payment_transaction_id
                $append_sql .= ", reference_id='{$params->reference_id}'";

                // count the number of rows found
                if(isset($student_param->checkout_url) && count($paymentRecord) == 1) {
                    $paymentRecord = $paymentRecord[0];
                }

                /* Record the payment made by the user */
                if(!is_array($paymentRecord)) {

                    // generate a unique id for the payment record
                    $payment_id = $uniqueId = random_string("alnum", RANDOM_STRING);
                    $counter = $this->append_zeros(($this->itemsCount("fees_collection", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                    $receiptId = $params->client_data->client_preferences->labels->receipt_label.$counter;
                    $receiptId = strtoupper($receiptId);
                    
                    // log the payment record
                    $stmt = $this->db->prepare("INSERT INTO fees_collection
                        SET payment_id = ?, client_id = ?, item_id = ?, student_id = ?, department_id = ?, class_id = ?, 
                        category_id = ?, amount = ?, created_by = ?, academic_year = ?, academic_term = ?, 
                        description = ?, currency = ?, receipt_id = ?, payment_method = ? {$append_sql}
                    ");
                    $stmt->execute([
                        $payment_id, $params->clientId, $uniqueId, $paymentRecord->student_id, 
                        $paymentRecord->department_id ?? null, $paymentRecord->class_id, $paymentRecord->category_id, 
                        $amount, $paymentRecord->student_id, 
                        $paymentRecord->academic_year, $paymentRecord->academic_term, 
                        "Self Service Fees Payment", $currency, $receiptId, $payment_method
                    ]);
                    /* Update the user payment record */
                    $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
                        last_payment_date = now(), last_payment_id = '{$uniqueId}' ".($paid_status ? ", paid_status='{$paid_status}'" : "")."
                        WHERE checkout_url = ? AND client_id = ? LIMIT 1
                    ");
                    $stmt->execute([$totalPayment, $outstandingBalance, $student_param->checkout_url, $params->clientId]);

                    /* Record the user activity log */
                    $this->userLogs("fees_payment", $student_param->checkout_url, null, "Received an amount of <strong>{$amount}</strong> as Payment for <strong>{$paymentRecord->category_name}</strong> from <strong>{$paymentRecord->student_details["student_name"]}</strong>. Outstanding Balance is <strong>{$outstandingBalance}</strong>", $params->userId);
                    
                    // set the student name
                    $student_name = $paymentRecord->student_details["student_name"];
                    $student_id = $paymentRecord->student_id;

                } else {

                    // generate a new payment_id
                    $payment_id = random_string("alnum", RANDOM_STRING);

                    // get the student name
                    $student = $this->pushQuery("name AS student_name", "users", "item_id = '{$student_param->student_id}' AND user_type='student' AND client_id = '{$params->clientId}' LIMIT 1");
                    $student_name = !empty($student) ? $student[0]->student_name : "Unknown";
                    $student_id = $student_param->student_id;

                    // loop through the payment record
                    foreach($paymentRecord as $record) {

                        // loop through the items which were paid for
                        if(isset($amount_paid[$record->category_id])) {

                            // get the total amount paid
                            $total_paid = $amount_paid[$record->category_id];
                            $total_balance = ($record->balance - $total_paid);
                            $totalPayment = ($record->amount_paid + $total_paid);

                            // set the paid status
                            $paid_status = ((round($totalPayment) === round($record->amount_due)) || (round($totalPayment) > round($record->amount_due))) ? 1 : 2;

                            // generate a unique id for the payment record
                            $uniqueId = random_string("alnum", RANDOM_STRING);
                            $counter = $this->append_zeros(($this->itemsCount("fees_collection", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                            $receiptId = $clientPref->labels->receipt_label.$counter;
                            $receiptId = strtoupper($receiptId);

                            // insert the new record into the database
                            $stmt = $this->db->prepare("INSERT INTO fees_collection
                                SET client_id = ?, item_id = ?, student_id = ?, payment_id = ?, department_id = ?, class_id = ?, 
                                category_id = ?, amount = ?, created_by = ?, academic_year = ?, academic_term = ?, 
                                description = ?, currency = ?, receipt_id = ?, payment_method = ? {$append_sql}
                            ");
                            $stmt->execute([
                                $params->clientId, $uniqueId, $record->student_id, $payment_id,
                                $record->department_id ?? null, $record->class_id, $record->category_id, 
                                $total_paid, $params->userId, $record->academic_year, $record->academic_term, 
                                $params->description ?? null, $currency, $receiptId, $payment_method
                            ]);

                            /* Update the user payment record */
                            $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
                                last_payment_date = now(), last_payment_id = '{$uniqueId}' ".($paid_status ? ", 
                                paid_status='{$paid_status}'" : "")." WHERE checkout_url = ? AND client_id = ? LIMIT 1
                            ");
                            $stmt->execute([($record->amount_paid + $total_paid), $total_balance, $record->checkout_url, $params->clientId]);

                            /* Record the user activity log */
                            $this->userLogs("fees_payment", $record->checkout_url, null, "Received an amount of <strong>{$total_paid}</strong> as Payment for <strong>{$record->category_name}</strong> from <strong>{$student_name}</strong>. Outstanding Balance is <strong>{$total_balance}</strong>", $params->userId);
                            
                            // set a new parameter for the checkout and category id
                            $params->checkout_url = $record->checkout_url;
                        }
                    }

                }

                /* Update the student credit balance */
                if(isset($creditBalance)) {
                    // update the user data
                    $this->db->query("UPDATE users SET account_balance = (account_balance + $creditBalance) WHERE item_id = '{$params->student_id}' AND client_id = '{$params->clientId}' LIMIT 1");
                }

                // log the data in the statement account
                $check_account = $this->pushQuery("item_id, balance", "accounts", "client_id='{$student_param->clientId}' AND status='1' AND default_account='1' LIMIT 1");

                // if the account is not empty
                if(!empty($check_account)) {

                    // get the account unique id
                    $account_id = $check_account[0]->item_id;
                    
                    // log the transaction record
                    $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                        item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                        reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                        description = ?, academic_year = ?, academic_term = ?, balance = ?, state = 'Approved', validated_date = now()
                    ");
                    $stmt->execute([
                        $payment_id, $student_param->clientId, $account_id, "fees", "Deposit", $params->reference_id, 
                        $amount, $student_id, date("Y-m-d"), $payment_method, "Fees Payment - for <strong>{$student_name}</strong>",
                        $academic_year, $academic_term, ($check_account[0]->balance + $amount)
                    ]);

                    // add up to the expense
                    $this->db->query("UPDATE accounts SET total_credit = (total_credit + {$amount}), balance = (balance + {$amount}) WHERE item_id = '{$account_id}' LIMIT 1");

                }

                // if the cpayment_url is not empty
                if(!empty($session->cpayment_url)) {
                    // set the checkout url
                    $checkout_url = "pay/{$params->clientId}/fees/{$student_id}";
                    // update the status of the status
                    $this->db->query("UPDATE payment_urls SET status='1' WHERE client_id='{$params->clientId}' AND short_url='{$session->cpayment_url}' LIMIT 1");
                    $this->db->query("UPDATE payment_urls SET status='1' WHERE client_id='{$params->clientId}' AND checkout_url='{$checkout_url}' LIMIT 10");
                }

                // if the contact number is not empty
                if(preg_match("/^[0-9+]+$/", $session->user_contact)) {
                    
                    // append the message
                    $message = "Hello {$student_name},\nFees Payment was successfully processed.\nAmount Paid: {$currency} {$amount}\nBalance: {$currency} {$outstandingBalance}\n";
                    
                    // calculate the message text count
                    $chars = strlen($message);
                    $message_count = ceil($chars / $this->sms_text_count);
                    
                    // get the sms balance
                    $balance = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$student_param->clientId}' LIMIT 1");
                    $balance = $balance[0]->sms_balance ?? 0;

                    // return error if the balance is less than the message to send
                    if($balance > $message_count) {

                        //execute post
                        $result = $this->send_mnotify_sms([$session->user_contact], $message);
                        
                        // if the sms was successful
                        if(!empty($result)) {
                            // reduce the SMS balance
                            $this->db->query("UPDATE smsemail_balance SET sms_balance = (sms_balance - {$message_count}), sms_sent = (sms_sent + {$message_count}) WHERE client_id = '{$student_param->clientId}' LIMIT 1");
                        }
                    }

                }

                // log the transaction information
                $this->db->query("UPDATE transaction_logs 
                    SET payment_data='".json_encode($payment_check["data"])."', state='Processed' 
                    WHERE reference_id='{$params->reference_id}' LIMIT 1
                ");

                // unset the reference id
                $session->remove(["self_pay_reference_id", "user_contact", "reference_id", "cpayment_url"]);

                // commit the statment
                $this->db->commit();

                // return the success message
                return [
                    "code" => 200,
                    "data" => "Fees payment successful."
                ];

            } else {
                return ["code" => 203, "data" => "Sorry! We could not validate the transaction."];
            }

        } catch(PDOException $e) {
            $this->db->rollBack();
            return ["code" => 203, "data" => "Sorry! An unexpected error occurred while processing the request."];
        }

    }



    /**
     * Check the payment request status from paystack
     * 
     * @return Array
     */
    public function epay_validate(stdClass $params) {

        // end query is the session access_denied_log is not empty
        if(empty($this->session->self_pay_reference_id)) {
            // return permission denied
            return ["code" => 203, "data" => "Payment request cancelled."];
        }

        // set the transaction id
        $tid = $this->session->self_pay_reference_id;

        // get the request log
        $log = $this->pushQuery("*", "transaction_logs", "transaction_id='{$tid}' LIMIT 1");

        // confirm if a record already exists
        if(empty($log)) {
            return ["code" => 203, "data" => "Payment request cancelled."];
        }

        // get the status
        if($log[0]->state == "Processed") {
            return ["code" => 203, "data" => "Payment request already processed."];   
        }

        // set the parameters
        $data = (object) [
            "route" => "verify",
            "reference" => $log[0]->reference_id
        ];

        // confirm the payment
        $payment_check = $this->get($data);

        // if payment status is true
        if(!empty($payment_check["data"]) && isset($payment_check["data"]->status) && ($payment_check["data"]->status === true)) {
            // convert the data to object
            $_param = json_decode($log[0]->transaction_data);
            $_param->client_data = $params->client_data;
            $_param->paystack_data = $payment_check;
            $_param->transaction_id = $tid;
            $_param->userId = $params->userId ?? $_param->student_id;
            $_param->reference_id = $log[0]->reference_id;
            $_param->param["student_id"] = $_param->student_id;
            $_param->param["checkout_url"] = $_param->checkout_url;

            // process the payment
            return $this->verify($_param);
        } else {
            return ["code" => 203, "data" => "Payment request cancelled."];
        }

    }
}
?>