<?php

class Arrears extends Myschoolgh {

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
	}
    
    /**
     * List the fees arrears
     * 
     * @param String        $params->clientId
     * @param String        $params->student_id
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        try {

            $params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;
            $populate = false;

            if(isset($params->data)) {
                $params->student_id = $params->data["student_id"] ?? null;
                $populate = true;
            }

            $academic_term = $params->academics->academic_term ?? $params->academic_term;
            $academic_year = $params->academics->academic_year ?? $params->academic_year;

            $filters = 1;
            $filters .= !empty($params->student_id) ? " AND a.student_id IN {$this->inList($params->student_id)}" : "";
            $filters .= !empty($params->clientId) ? " AND a.client_id IN {$this->inList($params->clientId)}" : "";
            $filters .= !empty($params->class_id) ? " AND c.id IN {$this->inList($params->class_id)}" : "";
        
            // prepare and execute the statement
            $stmt = $this->db->prepare("SELECT 
                    a.student_id, u.id as _student_id, u.name AS student_name, a.arrears_details, a.arrears_category, 
                    a.fees_category_log, a.arrears_total, c.id AS class_id, c.name AS class_name,
                    (
                        SELECT CONCAT(
                            b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.guardian_id,'NULL')
                        ) FROM users b WHERE b.item_id = a.student_id LIMIT 1
                    ) AS student_info,
                    (
                        SELECT sum(b.balance) FROM fees_payments b 
                        WHERE b.student_id = a.student_id AND b.academic_term = '{$academic_term}'
                            AND b.academic_year = '{$academic_year}' AND b.exempted = '0'
                    ) AS debt
                FROM users_arrears a
                LEFT JOIN users u ON u.item_id = a.student_id
                LEFT JOIN classes c ON c.id = u.class_id
                WHERE {$filters} ORDER BY student_name ASC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the student id
                $result->student_info = (object) $this->stringToArray($result->student_info, "|", ["unique_id", "user_id", "name", "image", "phone_number", "guardian_id"]);

                // convert the created by string into an object
                $result->arrears_details = json_decode($result->arrears_details, true);
                $result->arrears_category = json_decode($result->arrears_category, true);
                $result->fees_category_log = json_decode($result->fees_category_log, true);

                // clean the category
                $result->students_fees_category_array = filter_fees_category($result->fees_category_log);
                $result->debt = (float) $result->debt;
                $result->arrears_total = (float) $result->arrears_total;

                // append to the array lis
                $data[] = $result;
            }

            // format the data before submission
            if($populate && !empty($data)) {

                $arrears_details = $data[0]->arrears_details;
                $fees_category_log = $data[0]->fees_category_log;
                
                // set the table head
                $student_fees_arrears = "<tr>";
                $student_fees_arrears .= "<td colspan='3' style='padding:0px'>";
                $student_fees_arrears .= "<table class='table table-md'>";
                $students_fees_category_array = filter_fees_category($fees_category_log);

                // loop through the arrears details
                foreach($arrears_details as $year => $categories) {
                    // clean the year term
                    $split = explode("...", $year);
                    
                    // set the academic year header
                    $student_fees_arrears .= "<thead>";

                    $student_fees_arrears .= "<tr class='font-20'><td><strong>Academic Year: </strong>".str_ireplace("_", "/", $split[0])."</td>";
                    $student_fees_arrears .= "<td><strong>Academic Term: </strong> {$split[1]}</td></tr>";
                    $student_fees_arrears .= "<tr><th>DESCRIPTION</th><th>BALANCE</th></tr>";
                    $student_fees_arrears .= "</thead>";
                    $student_fees_arrears .= "<tbody>";
                    $total = 0;
                    // loop through the items for each academic year
                    foreach($categories as $cat => $value) {
                        // add the sum
                        $total += $value;
                        $category_name = $students_fees_category_array[$cat]["name"] ?? null;
                        // display the category name and the value
                        $student_fees_arrears .= "<tr><td>{$category_name}</td><td>{$value}</td></tr>";
                    }
                    $student_fees_arrears .= "
                        <tr><td></td>
                            <td class='font-20 font-bold'>
                                <div class='mb-2'>".number_format($total, 2)."</div>
                                <button onclick='return load(\"arrears/{$data[0]->student_id}\")' class='btn text-uppercase btn-sm btn-outline-success'><i class='fa fa-money-bill-alt'></i> Pay Arrears</button>
                            </td>
                        </tr>";
                    $student_fees_arrears .= "</tbody>";
                }
                $student_fees_arrears .= "</table>";
                $student_fees_arrears .= "</td>";
                $student_fees_arrears .= "</tr>";

                $data = $student_fees_arrears;

            }
            
			return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            // return an unexpected error notice
            return $this->unexpected_error;
        }
    }


    /**
     * Make payment for the fees
     * 
     * @param String        $params->checkout_url
     * @param Float         $params->amount
     * 
     * @return Array
     */
	public function make_payment(stdClass $params) {

        try {

            global $defaultUser, $clientPrefs;
            
            // get the preference of the client
            $preference = $clientPrefs->labels;
            
            // begin transaction
            $this->db->beginTransaction();

            /** Validate the amount */
            if(!$params->amount) {
                return ["code" => 203, "data" => "Sorry! The amount cannot be empty."];
            }
            
            // ensure the amount is a valid integer
            if(!preg_match("/^[0-9.]+$/", $params->amount)) {
                return ["code" => 203, "data" => "Sorry! The amount must be a valid numeric integer."];
            }

            /** Get the checkout details */
            $params->remove_exempted_fees = true;
            $params->clean_payment_info = true;
            $arrearsRecord = $this->pushQuery("
                arrears_details, arrears_category, fees_category_log, arrears_total, u.name AS student_name, u.class_id", 
                "users_arrears a LEFT JOIN users u ON u.item_id = a.student_id",
                "a.student_id='{$params->student_id}' AND a.client_id='{$params->clientId}' LIMIT 1"
            );
            
            /** If no allocation record was found */
            if(empty($arrearsRecord)) {
                return ["code" => 203, "data" => "Sorry! This student has no outstanding arrears record to effect this payment on."];
            }

            /** Validate email address */
            if(!empty($params->email_address) && !filter_var($params->email_address, FILTER_VALIDATE_EMAIL)) {
                return ["code" => 203, "data" => "Sorry! A valid email address is required."];
            }

            // get the first array key
            $fees_owing = [];
            $arrearsRecord = $arrearsRecord[0];

            // convert the arrears record into an array list
            $arrears_details = json_decode($arrearsRecord->arrears_details, true);
            $arrears_category = json_decode($arrearsRecord->arrears_category, true);
            $fees_category_log = json_decode($arrearsRecord->fees_category_log, true);

            // ensure the amount to be paid is not more than the arrears owned
            if(round($params->amount) > round($arrearsRecord->arrears_total)) {
                return ["code" => 203, "data" => "Sorry! You cannot pay more than the outstanding balance."];
            }

            // initials
            $balance = $arrearsRecord->arrears_total;

            $arrears_list = [];
            $amount_paid = [];
            $paying = $params->amount;

            // loop through the fees record
            foreach($arrears_details as $year => $categories) {
                $split = explode("...", $year);
                $clean_year = str_ireplace("_", "/",$split[0]);
                $clean_term = $split[1];

                foreach($categories as $category_id => $value) {
                    
                    // algorithm to get the items being paid for
                    if($paying > 0) {
                        if(($value < $paying) || ($value == $paying)) {
                            $paying = $paying - $value;
                            // if the paid status is not equal to one
                            if($value != 0.00) {
                                $amount_paid[$clean_year][$clean_term][$category_id] = $value;
                            }
                        } elseif($value > $paying) {
                            $n_value = $value - $paying;
                            $amount_paid[$clean_year][$clean_term][$category_id] = $paying;
                            $arrears_list[$year][$category_id] = $n_value;
                            $paying = 0;
                        } else {
                            $n_value = $value - $paying;
                            $amount_paid[$clean_year][$clean_term][$category_id] = $paying;
                            $arrears_list[$year][$category_id] = $n_value;
                            $paying -= $value; 
                        }
                    } else {
                        $arrears_list[$year][$category_id] = $value;
                    }
                }

            }

            // generate the payment id
            $uniqueId = $payment_id = random_string('alnum', 15);

            /** Run this section if the record is not empty */
            if(!empty($amount_paid)) {
                // log history
                $log_history = [];
                
                /** Subtract the Amount Paid from the Previous Term Records */
                foreach($amount_paid as $year => $acc_year) {
                    foreach($acc_year as $term => $acc_term) {
                        foreach($acc_term as $cat_id => $cat_amount) {
                            /** Append to the Log History */
                            $log_history[] = [
                                "amount_paid" => $cat_amount,
                                "category_id" => $cat_id,
                                "academic_term" => $term,
                                "academic_year" => $year,
                                "student_id" => $params->student_id
                            ];
                            /** Set the Query String */
                            $this->db->query("UPDATE fees_payments SET amount_paid = (amount_paid + {$cat_amount}),
                                balance = (balance - {$cat_amount}) WHERE category_id = '{$cat_id}' 
                                AND academic_year = '{$year}' AND academic_term = '{$term}' AND 
                                student_id = '{$params->student_id}' AND client_id = '{$params->clientId}' LIMIT 1
                            ");
                        }
                    }
                }

                // previous record
                $previous = ["details" => $arrears_details, "category" => $arrears_category];

                // Insert the record
                $log = $this->db->prepare("INSERT INTO users_arrears_log SET client_id = ?, student_id = ?, payment_id = ?, log_history = ?, previous_log = ?");
                $log->execute([$params->clientId, $params->student_id, $payment_id, json_encode($log_history), json_encode($previous)]);
            }

            // get the currency
            $params->payment_method = isset($params->payment_method) ? ucfirst($params->payment_method) : "Cash";
            $currency = $clientPrefs->labels->currency ?? null;

            // set this to boolean
            $append_sql = (bool) ($params->payment_method === "Cheque");

            // ensure that the bank_id and the cheque number are not empty
            if(!empty($append_sql) && (empty($params->bank_id) || empty($params->cheque_number))) {
                return ["code" => 203, "data" => "Sorry! The bank name and cheque number cannot be empty."];
            }

            // append additional sql
            $append_sql = !empty($append_sql) ? ", cheque_security='".($params->cheque_security ?? null)."', cheque_bank='{$params->bank_id}', cheque_number='{$params->cheque_number}'" : null;
            
            // if the payment method is momo or card payment
            $append_sql .= ", paidin_by='".($params->email_address ?? null)."', paidin_contact='".($params->contact_number ?? null)."'";

            // get the student name
            $student = $this->pushQuery("name AS student_name", "users", "item_id = '{$params->student_id}' AND user_type='student' AND client_id = '{$params->clientId}' LIMIT 1");
            $student_name = !empty($student) ? $student[0]->student_name : "Unknown";

            // set a new arrears category array
            $arrears_cat_arr = [];
            foreach($arrears_list as $key => $value) {
                foreach($value as $ikey => $ivalue) {
                    $arrears_cat_arr[$ikey] = isset($arrears_cat_arr[$ikey]) ? ($arrears_cat_arr[$ikey] + $ivalue) : $ivalue;
                }
            }

            // get the total arrears balance
            $arrears_total = array_sum($arrears_cat_arr);

            // update the student fees arrears record
            $update_query = $this->db->prepare("UPDATE users_arrears SET arrears_details = ?, arrears_category = ?, arrears_total = ?, last_updated = now() WHERE student_id = ? AND client_id = ? LIMIT 1");
            $update_query->execute([json_encode($arrears_list), json_encode($arrears_cat_arr), $arrears_total, $params->student_id, $params->clientId]);

            // insert the fees collection record
            $counter = $this->append_zeros(($this->itemsCount("fees_collection", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $receiptId = strtoupper($clientPrefs->labels->receipt_label.$counter);

            // insert the new record into the database
            $stmt = $this->db->prepare("INSERT INTO fees_collection
                SET client_id = ?, item_id = ?, student_id = ?, payment_id = ?, class_id = ?, 
                amount = ?, created_by = ?, academic_year = ?, academic_term = ?, category_id = ?,
                description = ?, currency = ?, receipt_id = ?, payment_method = ?, has_reversal = ? {$append_sql}
            ");
            $stmt->execute([
                $params->clientId, $uniqueId, $params->student_id, $uniqueId,
                $arrearsRecord->class_id, $params->amount, $params->userId, $params->academic_year, 
                $params->academic_term, "Arrears", "Payment of Outstanding Fees Arrears", $currency, 
                $receiptId, $params->payment_method, 1
            ]);

            /* Record the user activity log */
            $this->userLogs("arrears_payment", $params->student_id, null, "{$params->userData->name} received an amount of <strong>{$params->amount}</strong> as Payment for <strong>Fees Arrears</strong> from <strong>{$arrearsRecord->student_name}</strong>. Outstanding Arrears is <strong>{$arrears_total}</strong>", $params->userId);
            
            // log the data in the statement account
            $check_account = $this->pushQuery("item_id, balance", "accounts", "client_id='{$params->clientId}' AND status='1' AND default_account='1' LIMIT 1");

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
                    $payment_id, $params->clientId, $account_id, "arrears", "Deposit", "Arrears Payment", $params->amount, $params->userId, 
                    date("Y-m-d"), $params->payment_method, "Fees Arrears Payment - for <strong>{$arrearsRecord->student_name}</strong>",
                    $params->academic_year, $params->academic_term, ($check_account[0]->balance + $params->amount)
                ]);

                // add up to the deposits
                $this->db->query("UPDATE accounts SET total_credit = (total_credit + {$params->amount}), balance = (balance + {$params->amount}) WHERE item_id = '{$account_id}' AND client_id='{$params->clientId}' LIMIT 1");

            }

            // Log the transaction information
            if(isset($params->transaction_id) && isset($params->reference_id)) {
                // Insert the transaction
                $this->db->query("INSERT INTO transaction_logs SET client_id = '{$params->clientId}',
                    transaction_id = '{$params->transaction_id}', endpoint = 'arrears', reference_id = '{$params->reference_id}', amount='{$params->amount}'
                ");
            }

            // send the receipt via sms
            if(isset($preference->send_receipt) && isset($params->contact_number)){
                
                // if the contact number is not empty
                if(strlen($params->contact_number) > 9 && preg_match("/^[0-9+]+$/", $params->contact_number)) {
                    
                    // append the message
                    $message = "Hello {$student_name},\nFees Arrears Payment was successfully processed.\nAmount Paid: {$currency} {$params->amount}\nBalance: {$currency} {$arrears_total}\n";
                    
                    // calculate the message text count
                    $chars = strlen($message);
                    $message_count = ceil($chars / $this->sms_text_count);
                    
                    // get the sms balance
                    $balance = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$params->clientId}' LIMIT 1");
                    $balance = $balance[0]->sms_balance ?? 0;

                    // return error if the balance is less than the message to send
                    if($balance > $message_count) {

                        //open connection
                        $ch = curl_init();

                        // set the field parameters
                        $fields_string = [
                            "key" => $this->mnotify_key,
                            "recipient" => [$params->contact_number],
                            "sender" => !empty($this->iclient->sms_sender) ? $this->iclient->sms_sender : $this->sms_sender,
                            "message" => $message
                        ];

                        // send the message
                        curl_setopt_array($ch, 
                            array(
                                CURLOPT_URL => "https://api.mnotify.com/api/sms/quick",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_POST => true,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_CAINFO => dirname(__FILE__)."\cacert.pem",
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => json_encode($fields_string),
                                CURLOPT_HTTPHEADER => [
                                    "Content-Type: application/json",
                                ]
                            )
                        );

                        //execute post
                        $result = json_decode(curl_exec($ch));
                        
                        // if the sms was successful
                        if(!empty($result)) {
                            // reduce the SMS balance
                            $this->db->query("UPDATE smsemail_balance SET sms_balance = (sms_balance - {$message_count}), sms_sent = (sms_sent + {$message_count}) WHERE client_id = '{$params->clientId}' LIMIT 1");
                        }
                    }

                }
            }

            // commit the statements
            $this->db->commit();

            // return the success message
            return [
                "data" => "Fees Arrears Payment was successfully recorded.",
                "additional" => [ "payment_id" => $payment_id ]
            ];

        } catch(PDOException $e) {
            // Role Back the statement
            $this->db->rollBack();
            
            // return an unexpected error notice
            return $this->unexpected_error;
        }

    }

    /**
     * Add Fees Arrears
     * 
     * @param Array         $params->data
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        // assign the values
        $student_id = $params->data["student_id"] ?? null;
        $class_id = $params->data["class_id"] ?? null;
        $academic_year = $params->data["academic_year"] ?? null;
        $academic_term = $params->data["academic_term"] ?? null;
        $category_id = $params->data["category_id"] ?? null;
        $amount = $params->data["amount"] ?? null;

        // confirm that the records are not empty
        if(empty($student_id)) {
            return ["code" => 203, "data" => "Sorry! Please select the student to continue."];
        }
        if(empty($class_id)) {
            return ["code" => 203, "data" => "Sorry! Please select the select of the student."];
        }
        if(empty($academic_year)) {
            return ["code" => 203, "data" => "Sorry! Please enter the academic year to continue."];
        }
        if(empty($academic_term)) {
            return ["code" => 203, "data" => "Sorry! Please enter the academic term to continue."];
        }
        if(empty($category_id)) {
            return ["code" => 203, "data" => "Sorry! The fees category id cannot be empty."];
        }
        if(empty($amount)) {
            return ["code" => 203, "data" => "Sorry! Please enter the amount."];
        }

        // load the student fees arrear list
        // get the student arrears
        $student_fees_arrears = "";
        $arrears_array = $this->pushQuery("arrears_details, arrears_category, fees_category_log, arrears_total", "users_arrears", "student_id='{$student_id}' AND client_id='{$params->clientId}' LIMIT 1");
        $fees_category = $this->pushQuery("id, name, amount, code, created_by", "fees_category", "id='{$category_id}' AND client_id='{$params->clientId}' LIMIT 1");
        
        // academic year
        $category = [$category_id => $amount];
        $fees_category_id = $fees_category[0] ?? [];

        // if the fees arrears not empty
        if(!empty($arrears_array)) {
            
            // set a new item for the arrears
            $arrears = $arrears_array[0];

            // category id
            $academic_key = str_ireplace("/", "_", $academic_year)."...{$academic_term}";

            // convert the item to array
            $fees_category_log = json_decode($arrears->fees_category_log, true);
            
            // category found check
            $categories_id = array_column($fees_category_log, "id");

            if(!in_array($fees_category_id->id, $categories_id)) {
                // append the category
                array_push($fees_category_log, $fees_category_id);
            }
            
            // existing arrears
            $old_arrears_details = json_decode($arrears->arrears_details, true);
            $old_arrears_category = json_decode($arrears->arrears_category, true);

            // format the data
            $current = [$academic_key => $category];
            $arrears_details = $this->append_fees_details($current, $old_arrears_details);
            $arrears_category = $this->append_fees_category($arrears_details);
            
            // arrears total
            $new_arrears_total = array_sum($arrears_category);

            // update the fees arrears log
            $update_query = $this->db->prepare("UPDATE users_arrears SET arrears_details = ?, arrears_category = ?, arrears_total = ?, last_updated = now(), fees_category_log = ? WHERE student_id = ? AND client_id = ? LIMIT 1");
            $update_query->execute([json_encode($arrears_details), json_encode($arrears_category), $new_arrears_total, json_encode($fees_category_log), $student_id, $params->clientId]);

            // insert the user activity
			$this->userLogs("fees_arrears", $student_id, null, "{$params->userData->name} - updated a new fees arrears record of the student.", $params->userId, null);

            return [
                "code" => 200,
                "data" => "Fees arrears successfully logged"
            ];
        }
        
        // insert new arrears record
        else {
            // category id
            $academic_key = str_ireplace("/", "_", $academic_year)."...{$academic_term}";

            // set additional variables
            $arrears_details = [$academic_key => $category];
            $arrears_category = $category;
            $new_arrears_total = $amount;

            // insert the fees arrears log
            $insert_query = $this->db->prepare("INSERT INTO users_arrears SET client_id = ?, student_id = ?, arrears_details = ?, arrears_category = ?, arrears_total = ?, date_created = now(), last_updated = now(), fees_category_log = ?");
            $insert_query->execute([$params->clientId, $student_id, json_encode($arrears_details), json_encode($arrears_category), $new_arrears_total, json_encode([$fees_category_id])]);

            // insert the user activity
			$this->userLogs("fees_arrears", $student_id, null, "{$params->userData->name} - inserted a new fees arrears record of the student.", $params->userId, null);

            return [
                "code" => 200,
                "data" => "Fees arrears successfully logged"
            ];
        }


    }

}