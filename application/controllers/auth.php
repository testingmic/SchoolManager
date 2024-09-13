<?php

class Auth extends Myschoolgh {

    public $status = false;
    public $redirect_url;
    public $error_response;
    public $success_response;

    // set the attempts for login and password reset
    private $time_period = 60;
    private $attempts_count = 7;

    private $password_ErrorMessage;

    public function __construct() {
        parent::__construct();

        $this->password_ErrorMessage = "<div style='width:100%'>Sorry! Please use a stronger password. <br><strong>Password Format</strong><br><ul>
			<li style='padding-left:15px;'>Password should be at least 8 characters long</li>
			<li style='padding-left:15px;'>At least 1 Uppercase</li>
			<li style='padding-left:15px;'>At least 1 Lowercase</li>
			<li style='padding-left:15px;'>At least 1 Numeric</li>
			<li style='padding-left:15px;'>At least 1 Special Character</li></ul></div>";
    }

    /**
     * Log the user in
     * 
     * @param String $username
     * @param String $password
     * 
     * @return String
     */
    public function login(stdClass $params) {

        global $session, $noticeClass;

        try {

            // begin transaction
            $this->db->beginTransaction();

            // make a query for the username
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.password, u.item_id AS user_id, 
                    u.access_level, u.username, u.client_id, 
                    u.status AS activated, u.email, u.user_type,
                    u.last_timetable_id, c.client_state, u.user_status,
                    cl.item_id AS class_guid
                FROM users u
                LEFT JOIN classes cl ON cl.id = u.class_id
                LEFT JOIN clients_accounts c ON c.client_id = u.client_id
                WHERE (u.username = ? OR u.email = ?) AND u.deleted =  ? ORDER BY u.id DESC LIMIT 1
            ");
            $stmt->execute([$params->username, $params->username, 0]);

            // count the number of rows found
            if($stmt->rowCount() == 1) {

                #check the number of login attempts 
                $loginAttempt = $this->confirmAttempt($params->username, "login", 1);

                # if the user has not yet hit the maximum attempt within the last 1 hour
                if(!$loginAttempt) {

                    // using the foreach to fetch the information
                    while($results = $stmt->fetch(PDO::FETCH_OBJ)) {

                        // verify the password
                        if(password_verify($params->password, $results->password)) {

                            // confirm if the user has permission to login
                            if(in_array($results->user_status, $this->allowed_login_status)) {

                                // last login trial
                                $lastLogin = $this->pushQuery("attempts", "users_access_attempt", "username='{$results->username}' AND attempt_type='login' LIMIT 5");
                                
                                // if the last login information is not empty
                                if(!empty($lastLogin)) {

                                    // get the user record
                                    $last_attempt = $lastLogin[0]->attempts;

                                    // if the attempt is 4 or more then lodge a notification to the user
                                    if($last_attempt >= 3) {

                                        // form the notification parameters
                                        $params = (object) [
                                            '_item_id' => random_string("alnum", RANDOM_STRING),
                                            'user_id' => $results->user_id,
                                            'subject' => "Login Failures",
                                            'username' => $results->username,
                                            'remote' => false, 
                                            'message' => "An attempt count of <strong>{$last_attempt}</strong> was made to access your Account. 
                                                We recommend that you change your password to help secure it. Visit the profile section to effect the change.",
                                            'content' => "An attempt count of <strong>{$last_attempt}</strong> was made to access your Account. 
                                                    We recommend that you change your password to help secure it. Visit the <a href=\"{{APPURL}}profile\">profile section</a> to effect the change.",
                                            'notice_type' => 3,
                                            'clientId' => $results->client_id,
                                            'userId' => $results->user_id,
                                            'initiated_by' => 'system'
                                        ];

                                        // add a new notification
                                        $noticeClass->add($params);
                                    }
                                }

                                // clear the login attempt
                                $this->clearAttempt($params->username);

                                // set the status variable to true
                                $this->status = true;

                                // unset the password from the results
                                unset($results->password);

                                // set these sessions if not a remote call
                                if(!$params->remote) {

                                    // set the user sessions for the person to continue
                                    $session->set("userLoggedIn", random_string('alnum', 50));
                                    $session->set("userId", $results->user_id);
                                    $session->set("userName", $params->username);
                                    $session->set("clientId", $results->client_id);
                                    $session->set("activated", $results->user_status);
                                    $session->set("userRole", $results->access_level);

                                    // check the client state
                                    if($results->client_state === "Pending") {
                                        $session->set("initialAccount_Created", true);
                                    }

                                    // set the last timetable id in session
                                    $session->set("last_TimetableId", $results->last_timetable_id);

                                    // set additional session for student
                                    if($results->user_type === "student") {
                                        // set the student id
                                        $session->set("student_id", $results->user_id);
                                        // set the student class ids
                                        $session->set("student_class_id", $results->class_guid);
                                    }
                                }
                                
                                // if a remote call was made for the access token
                                if($params->remote) {
                                    
                                    // get any active access token available or generate a new one if none exists
                                    $access = $this->temporary_access($results);

                                    // commit the transactions
                                    $this->db->commit();
                                    
                                    // return the response
                                    return $access;
                                }

                                // remove all temporary files uploaded before a logout
                                $this->clear_temp_files($results->user_id);
                                
                                #update the table
                                $ip = ip_address();
                                $br = $this->browser."|".$this->platform;

                                // update the last login for this user
                                $stmt = $this->db->prepare("UPDATE users SET last_login='{$this->current_timestamp}', last_visited_page='{{APPURL}}dashboard', last_seen = '{$this->current_timestamp}' WHERE item_id=? LIMIT 55");
                                $stmt->execute([$results->user_id]);

                                // log the history record
                                $stmt = $this->db->prepare("INSERT INTO users_login_history 
                                    SET username='{$params->username}', client_id='{$results->client_id}', log_ipaddress='{$ip}', log_browser='{$br}', 
                                    user_id='{$session->userId}', log_platform='{$this->agent}'
                                ");
                                $stmt->execute();

                                // if the user is an admin or accountant
                                if(in_array($results->user_type, ["admin", "accountant"])) {
                                    // run simple cron activity
                                    $this->execute_cron($results->client_id);
                                }

                                // commit all transactions
                                $this->db->commit();

                                // response to return
                                return [
                                    "code" => 200,
                                    "data" => "Login successful. Redirecting", 
                                    "refresh" => 1000
                                ];
                            } else {
                                //return the error message
                                return ["code" => 201, "data" => "Sorry! You have been denied access to the system."];
                            }

                        } else {
                            // add user attempt
                            $this->addAttempt($params->username, "login", 1);
                            $this->db->commit();
                            //return the error message
                            return ["code" => 201, "data" => "Sorry! Invalid Username/Password."];
                        }
                    }
                    
                } else {
                    // return the error message
                    return ["code" => 201, "data" => "Access denied due to multiple trial. Try again in an Hour's time."];
                }
            } else {
                // add user attempt
                //$this->addAttempt($params->username);
                $this->db->commit();
                return ["code" => 201, "data" => "Sorry! Invalid Username/Password."];
            }

            // return the success response
            if($params->remote && !$this->status) {
                return [
                    "error" => [
                        "code" => 201,
                        "data" => "Sorry! The Username/Password could not be validated" 
                    ]
                ];
            }

        } catch(PDOException $e) {
            $this->db->rollBack();
            return "Sorry! The Username/Password could not be validated";
        }
    }

    /**
     * Execute a Simple Cron Activity
     * 
     * @return Bool
     */
    public function execute_cron($clientId) {

        try {
            // fees payment reversal disallowed after 24 HOURS
            $this->db->query("UPDATE fees_collection SET has_reversal='0' WHERE recorded_date < (NOW() + INTERVAL - 24 HOUR) AND has_reversal='1' AND client_id='{$clientId}' LIMIT 1000");
            
            // do same to transactions recorded - auto set it to approved after 24 hours
            $this->db->query("UPDATE accounts_transaction SET state='Approved' WHERE date_created < (NOW() + INTERVAL - 24 HOUR) AND state='Pending' AND status='1' AND client_id='{$clientId}' LIMIT 500");

            // update the status of events
            $this->db->query("UPDATE events SET state='Over' WHERE end_date < CURDATE() AND state='Pending' AND status='1' AND client_id='{$clientId}' LIMIT 500");

            // users comments is not deletable after 3 hours of posting
            $this->db->query("UPDATE users_feedback SET is_deletable='0' WHERE date_created < (NOW() + INTERVAL - 3 HOUR) AND is_deletable='1' AND client_id='{$clientId}' LIMIT 500");
            
        } catch(PDOException $e) {}
    }

    /**
     * Clear all user temporary files
     * 
     * @param String $user_id
     * 
     * @return Bool
     */
    public function clear_temp_files($user_id) {
        // if the directory is active
        if(is_dir("assets/uploads/{$user_id}/tmp/")) {
            // file travessing
            foreach(get_dir_file_info("assets/uploads/{$user_id}/tmp/", false, true) as $eachFile) {
                // format the output
                if($eachFile["relative_path"] !== "assets/uploads/{$user_id}/tmp/thumbnail\\") {
                    unlink($eachFile["server_path"]);
                }
            }
        }
        return true;
    }
    
    /**
     * Generate an access token for a user
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function temporary_access(stdClass $params) {

        // create the temporary accesstoken
        $token = random_string("alnum", 32);
        $expiry = date("Y-m-d H:i:s", strtotime("+2 hours"));

        // replace the string
        $expiry = str_replace(": ", "", $expiry);

        // most recent query
        $recent = $this->lastAccessKey($params->username);

        // if within the last 10 minutes
        if($recent) {
            return [
                "validated" => true,
                "result" => "The temporary access token could not be generated since the last generated one is within 5 minutes interval.",
                "unexpired" => $this->temporaryKeys($params->username)
            ];
        }

        // access
        $access = [
            "username" => $params->username,
            "access_token" => base64_encode("{$params->username}:{$token}"),
            "expiry" => $expiry,
            "description" => "The access_token key must be parsed as part of the query parameters when making requests. This access token will expiry after 2 hours"
        ];
        
        try {
            // delete all temporary tokens
            $stmt = $this->db->prepare("UPDATE users_api_keys SET status = ? WHERE (TIMESTAMP(expiry_timestamp) < CURRENT_TIME()) AND access_type = ? LIMIT {$this->temporal_maximum}");
            $stmt->execute([0, 'temp']);

            // create the temporary token
            $this->db->query("INSERT INTO users_api_keys 
                SET user_id = '{$params->user_id}', username = '{$params->username}', 
                access_token = '".password_hash($token, PASSWORD_DEFAULT)."', access_type = 'temp', 
                expiry_date = '".date("Y-m-d", strtotime("+2 hours"))."', 
                expiry_timestamp = '".date("Y-m-d H:i:s", strtotime("+2 hours"))."', 
                requests_limit = '5000', access_key = '{$token}', client_id = '{$params->client_id}'
            ");

        } catch(PDOException $e) {} 
        
        // return the access token information
        return $access;
    }

    /**
     * Get the list of access tokens that are still active and can be used by the user
     * 
     * @param String $username      The username to use in loading record
     * 
     * @return Bool
     */
    private function temporaryKeys($username) {

        try {
            // run a query
            $stmt = $this->db->prepare("SELECT username, access_key, expiry_timestamp FROM users_api_keys WHERE username = ? AND (TIMESTAMP(expiry_timestamp) > CURRENT_TIME()) ORDER BY id DESC LIMIT 25");
            $stmt->execute([$username]);

            // load the results
            $data = [];

            // loop through the record list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // append to the array list
                $data[] = [
                    "username" => $result->username,
                    "access_token" => base64_encode("{$result->username}:{$result->access_key}"),
                    "access_type" => "temporary",
                    "expiry" => $result->expiry_timestamp
                ];
            }

            return $data;
        } catch(PDOException $e) {} 
    }

    /**
     * If the user's last key generated is within a 10 minutes span
     * Then deny regeneration
     * 
     * @param String $username      The username to use in loading record
     * 
     * @return Bool
     */
    private function lastAccessKey($username) {

        // run a query
		$stmt = $this->db->prepare("SELECT date_generated FROM users_api_keys WHERE username = ? ORDER BY id DESC LIMIT 1");
		$stmt->execute([$username]);

        // load the results
		$result = $stmt->fetch(PDO::FETCH_OBJ);

		$lastUpdate = $result->date_generated ?? 0;
        
        // if the last update was parsed
		if($lastUpdate) {
			return (strtotime($lastUpdate) + (60*5)) >= time() ? true : false;
		}

		return false;
	
    }

    /**
     * Add an attempt to login to the system
     * 
     * @param String $username      The username whom the attempt was made on behalf
     * @param String $attempt_type  The attempt type (default is login)
     * 
     * @return Bool
     */
    private function addAttempt($username, $attempt_type = "login", $username_found = 0) {
		
        try {
            // increase number of attempts
            // set last login attempt time if required    
            $sql = $this->db->prepare("SELECT attempts, id FROM users_access_attempt WHERE `ipaddress` = ? AND username = ? LIMIT 1"); 
            $sql->execute([ip_address(), $username]);
            
            // count the number of rows found
            if($sql->rowCount() > 0) {
                // loop through the results
                while($data = $sql->fetch(PDO::FETCH_OBJ)) {
                    // increment the attempts count
                    $attempts = $data->attempts + 1;
                    // check the attempts count
                    if($attempts == $this->attempts_count) {
                        $sql = $this->db->prepare("UPDATE users_access_attempt SET attempts=?, lastattempt = now() WHERE `ipaddress` = ? AND username = ? AND attempt_type = ? LIMIT 1");
                        $sql->execute([$attempts, ip_address(), $username, $attempt_type]);
                    } else {
                        $sql = $this->db->prepare("UPDATE users_access_attempt SET attempts = ? WHERE `ipaddress` = ? AND username = ? AND attempt_type = ? LIMIT 1");
                        $sql->execute([$attempts, ip_address(), $username, $attempt_type]);
                    }
                }
            } else {
                // insert a new attempt record
                $sql = $this->db->prepare("INSERT INTO users_access_attempt SET ipaddress = ?, attempts = ?, lastattempt = now(), username = ?, attempt_type = ?, username_found = ?");
                $sql->execute([ip_address(), 1, $username, $attempt_type, $username_found]);
            }
        } catch(PDOException $e) {
            return false;
        }
	}

    /**
     * Clear the login attempt
     * 
     * @param String $username      The username whom the attempt was made on behalf
     * @param String $attempt_type  The attempt type (default is login)
     * 
     * @return Bool
     */
	private function clearAttempt($username, $attempt_type = "login") {
			
        try {
            // prepare a new query
            $sql = $this->db->prepare("UPDATE users_access_attempt SET attempts = '0', lastattempt = now() WHERE username=? AND `ipaddress` = ? AND attempt_type = ? LIMIT 1");
            // execute the statement
            return $sql->execute([$username, ip_address(), $attempt_type]);
        } catch(PDOException $e) {
            return false;
        }
	}

    /**
     * Confirm if the login attempt has not been exceeded
     * 
     * @param $username     The username to confirm
     * @param String $attempt_type  The attempt type (default is login)
     *  
     * @return Bool
     */
	private function confirmAttempt($username, $attempt_type = "login", $username_found = 0) {
		
        try {
            // create the statement
            $sql = $this->db->prepare("SELECT 
                attempts, (
                    CASE when lastattempt is not NULL and DATE_ADD(lastattempt, INTERVAL {$this->time_period} MINUTE) > NOW() then 1 else 0 end
                ) as Denied FROM users_access_attempt WHERE `ipaddress` = ? AND username = ? AND attempt_type = ? LIMIT 1
            ");
            $sql->execute([ip_address(), $username, $attempt_type]);

            // loop through the results found
            while($data  = $sql->fetch(PDO::FETCH_OBJ)) {
                if ($data->attempts >= $this->attempts_count) {
                    if($data->Denied == 1) {
                        return 1;
                    } else {
                        $this->addAttempt($username, $attempt_type, $username_found);
                        return 0;
                    }
                }
            }
            return 0;
        } catch(PDOException $e) {
            return 0;
        }
	}

    /**
     * Send a reset token to the user
     * 
     * @param String $email
     * 
     * @return Bool
     */
    public function send_password_reset_token($params) {

        // if the email parameter was not parsed
        if(!isset($params->email)) {
            // print the error message
            return ["code" => 201, "data" => "Sorry! Please enter a valid email address."];
        }

        // if the email address could not be validated
        if(!filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
            return ["code" => 201, "data" => "Sorry! Please enter a valid email address."];
        }

        // create the user agent
        $user_agent = load_class('user_agent', 'libraries');

        try {
            // notification class init
            global $noticeClass;

            // begin transaction
            $this->db->beginTransaction();

            #check the number of login attempts 
            $resetAttempt = $this->confirmAttempt($params->email, "reset", 1);

            # if the user has not yet hit the maximum attempt within the last 1 hour
            if(!$resetAttempt) {
                // query for the email address match
                // since there are multiple accounts for a user, we can fetch just one item from the list where
                // the deleted status is 0
                $stmt = $this->db->prepare("SELECT item_id AS user_id, client_id, username, name AS fullname FROM users WHERE email='{$params->email}' AND deleted = ? AND disabled = ? LIMIT 1");
                $stmt->execute([0, 0]);

                // count the number of rows found
                if($stmt->rowCount() == 1) {

                    // set the status variable to true
                    $this->status = true;
                    
                    // add the reset attempt
                    $this->addAttempt($params->email, "reset", 1);

                    // using the foreach to fetch the information
                    while($results = $stmt->fetch(PDO::FETCH_OBJ)) {

                        #assign variable
                        $user_id = $results->user_id;
                        $fullname = $results->fullname;
                        $username = $results->username;

                        #create the reset password token
                        $request_token = random_string('alnum', mt_rand(60, 75));

                        #set the token expiry time to 2 hour from the moment of request
                        $expiry_time = time()+(60*60*2);

                        #update the table
                        $ip = $user_agent->ip_address();
                        $random_string = random_string("alnum", RANDOM_STRING);
                        $br = $user_agent->browser()." ".$user_agent->platform();

                        #deactivate all reset tokens
                        $stmt = $this->db->prepare("UPDATE users_reset_request SET token_status='ANNULED' WHERE username='{$username}' AND user_id='{$user_id}' AND token_status='PENDING'");
                        $stmt->execute();

                        #remove the item from the mailing list
                        $stmt = $this->db->prepare("UPDATE users_messaging_list SET deleted='1' WHERE item_id='{$user_id}' AND template_type='password-recovery'");
                        $stmt->execute();
                        
                        #process the form
                        $stmt = $this->db->prepare("INSERT INTO users_reset_request SET 
                            item_id = '{$random_string}', username='{$username}', user_id='{$user_id}', 
                            request_token='{$request_token}', user_agent='{$br}|{$ip}', 
                            expiry_time='{$expiry_time}', client_id = '{$results->client_id}'
                        ");
                        $stmt->execute();
                        
                        #FORM THE MESSAGE TO BE SENT TO THE USER
                        $message = 'Hi '.$fullname.'<br>You have requested to reset your password at '.$this->appName;
                        $message .= '<br><br>Before you can reset your password please follow this link. The reset link expires after 2 hours<br><br>';
                        $message .= '<a class="alert alert-success" href="'.$this->baseUrl.'verify?dw=password&token='.$request_token.'">Click Here to Reset Password</a>';
                        $message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
                        $message .= $this->baseUrl.'verify?dw=password&token='.$request_token;

                        // recipient list
                        $reciepient = ["recipients_list" => [["fullname" => $fullname,"email" => $params->email,"customer_id" => $user_id]]];
                        
                        // insert the email content to be processed by the cron job
                        $stmt = $this->db->prepare("
                            INSERT INTO users_messaging_list 
                            SET  template_type = ?, client_id = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?
                        ");
                        $stmt->execute([
                            'password-recovery', $results->client_id, $random_string, json_encode($reciepient),
                            $user_id, "[".$this->appName."] Change Password", $message, $user_id
                        ]);

                        // send a notification to the user
                        $params = (object) [
                            '_item_id' => $random_string,
                            'user_id' => $user_id,
                            'subject' => "Password Reset Request",
                            'message' => "A request was made by yourself to change your password.",
                            'notice_type' => 4,
                            'userId' => $user_id,
                            'initiated_by' => 'system'
                        ];

                        // add a new notification
                        $noticeClass->add($params);

                        // insert the user activity
                        $this->userLogs("password-recovery", $user_id, null, "{$fullname} requested for a password reset code.", $user_id);
                        
                        // commit all transactions
                        $this->db->commit();

                        #record the password change request
                        return [
                            "code" => 200, 
                            "data" => "Please check your email for steps to reset password.",
                            "additional" => [
                                // "resetPath" => $this->baseUrl.'verify?dw=password&token='.$request_token
                            ]
                        ];

                    }

                } else {
                    $this->addAttempt($params->email, "reset");
                    return ["code" => 201, "data" => "Sorry! The email address could not be validated."];
                }
            } else {
                return ["code" => 201, "data" => "Access denied due to multiple trial. Try again in an Hour's time."];
            }

        } catch(PDOException $e) {
            // rollback all transactions if at least one fails
            $this->db->rollBack();
            print $e->getMessage();
            return ["code" => 201, "data" => "Sorry! An error was encountered while processing the request."];
        }
    }

    /**
     * Reset a user password
     * @param \stdClass $params
     * @param String $params->password
     * @param String $params->user_id
     * @param String $params->username
     * @param String $params->reset_token
     * 
     * @return Bool
     */
    public function reset_user_password(stdClass $params) {

        // set the url
        $baseUrl = $this->baseUrl;

        // if the email parameter was not parsed
        if(!isset($params->password) || !isset($params->password_2)) {
            // print the error message
            return ["code" => 201, "data" => "Sorry! Please the password and confirmation fields are required."];
        }
        // if the two passwords do not match
        if($params->password !== $params->password_2) {
            return ["code" => 201, "data" => "Sorry! Please ensure the passwords match."];
        }

        // password test
        if(!passwordTest($params->password)) {
            return ["code" => 201, "data" => $this->password_ErrorMessage ];
        }

        try {

            // notification class init
            global $noticeClass;

            $this->db->beginTransaction();

            // query the database for the record
            $stmt = $this->db->prepare("
                SELECT
                    r.username, r.user_id, r.request_token, u.name, u.email, r.item_id AS request_item_id
                FROM users_reset_request r 
                LEFT JOIN users u ON u.item_id = r.user_id
                WHERE r.request_token=? AND r.token_status = ?
            ");
            $stmt->execute([$params->reset_token, 'PENDING']);

            // count the number of rows found
            if($stmt->rowCount() == 1) {

                $this->status = true;
                    // using the foreach to fetch the information
                while($results = $stmt->fetch(PDO::FETCH_OBJ)) {

                    #assign variable
                    $user_id = $results->user_id;
                    $fullname = $results->name;
                    $email = $results->email;
                    $request_item_id = $results->request_item_id;

                    #update the table
                    $ip = ip_address();
                    $br = $this->browser." ".$this->platform;

                    #encrypt the password
                    $password = password_hash($params->password, PASSWORD_DEFAULT);
                    
                    #deactivate all reset tokens
                    $stmt = $this->db->prepare("UPDATE users SET password=? WHERE item_id=? LIMIT 10");
                    $stmt->execute([$password, $user_id]);

                    #process the form
                    $stmt = $this->db->prepare("
                        UPDATE users_reset_request SET 
                            request_token=NULL, reset_date=now(), reset_agent='{$br}|{$ip}', 
                            token_status='USED', expiry_time='".time()."'
                        WHERE request_token='{$params->reset_token}' LIMIT 1
                    ");
                    $stmt->execute();

                    #record the activity
                    $this->userLogs("password-recovery", $user_id, null, "You successfully changed your password.", $user_id);
                   
                    //FORM THE MESSAGE TO BE SENT TO THE USER
                    $message = 'Hi '.$fullname.'<br>You have successfully changed your password at '.$this->appName;
                    $message .= '<br><br>Ignore this message if your rightfully effected this change.<br>';
                    $message .= '<br>If not,';
                    $message .= '<a class="alert alert-success" href="'.$this->baseUrl.'forgot-password">Click Here</a> if you did not perform this act.';

                    #send email to the user
                    $reciepient = ["recipients_list" => [["fullname" => $fullname, "email" => $email, "customer_id" => $user_id]]];

                    // add to the email list to be sent by a cron job
                    $stmt = $this->db->prepare("
                        INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, 
                        users_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?
                    ");
                    $stmt->execute([
                        'password-recovery', $user_id, $user_id, json_encode($reciepient),
                        $user_id, "[{$this->appName}] Change Password", $message
                    ]);

                    // commit all transactions
                    $this->db->commit();
                    $this->session->redirect = "{$this->baseUrl}";

                    $this->session->remove("refresh_page");

                    // response to return
                    return ["code" => 200, "data" => "Your password was successfully changed.", "refresh" => 2000];

                }

            } else {
                return false;
            }

        } catch(PDOException $e) {
            // rollback all transactions if at least one fails
            $this->db->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * InApp Change User Password
     * 
     * @param String $params->password      The current password
     * @param String $params->password_1    The new password to use
     * @param String $params->password_2    Confirmation of the new passwod
     * 
     * @return Array
     */
    public function change_password(stdClass $params) {

        try {

            // global variable
            global $defaultUser;
            
            if(in_array($params->clientId, ["TLIS0000001"])) {
                return ["code" => "203", "data" => "Sorry! Changing of password is currently disabled for this account."];   
            }
            
            // reset count more than 4 then no long process the user request
            if(!empty($this->session->reset_count) && ($this->session->reset_count >= 4)) {
                return ["code" => "203", "data" => "Sorry! You have been blocked from multiple trial to reset password."];
            }

            // password test
            if(!passwordTest($params->password_1)) {
                return ["code" => 201, "data" => $this->password_ErrorMessage_2 ];
            }

            // confirm if the passwords match
            if($params->password_1 !== $params->password_2) {
                return ["code" => "203", "data" => "Sorry! The passwords supplied does not match."];
            }

            // get the user information
            $user = $this->pushQuery("password, changed_password, email, name", "users", "item_id='{$params->user_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($user)) {
                return ["code" => "203", "data" => "Sorry! An invalid user id was submitted."];
            }

            // get the first item
            $user = $user[0];

            // if the defaultUser is not empty and the password parameter was not set
            if(!empty($defaultUser)) {

                // if the password parameter was not set
                if($defaultUser->changed_password && !isset($params->password)) {
                    return ["code" => "203", "data" => "Sorry! The current password is required."];
                }

                // compare the password
                elseif($defaultUser->changed_password && !password_verify($params->password, $user->password)){

                    // count the error number
                    $this->session->reset_count = !empty($this->session->reset_count) ? ($this->session->reset_count + 1) : 1;

                    // return the error message
                    return ["code" => "203", "data" => "Sorry! We could not validate the password provided."];
                }

            }

            // disallow users from using their current password
            if(password_verify($params->password_1, $user->password)) {
                return ["code" => "203", "data" => "Sorry! Your password must be different from your current password."];
            }

            // change the password
            $stmt = $this->db->prepare("UPDATE users SET last_password_change = now(), password = ?, changed_password = ? WHERE item_id = ? AND client_id = ? LIMIT 3");
            $stmt->execute([password_hash($params->password_1, PASSWORD_DEFAULT), 1, $params->user_id, $params->clientId]);

            // log the user activity
            $this->userLogs("password_reset", $params->user_id, null, "Password was successfully changed.", $params->userId);
                   
            //FORM THE MESSAGE TO BE SENT TO THE USER
            $message = 'Hi '.$user->name.'<br>Your password was successfully changed.';
            $message .= '<br><br>Ignore this message if your rightfully effected this change.<br>';
            $message .= '<br>If not, ';
            $message .= '<a class="alert alert-success" href="'.$this->baseUrl.'forgot-password">Click Here</a> if you did not perform this act.';

            #send email to the user
            $reciepient = ["recipients_list" => [["fullname" => $user->name, "email" => $user->email, "customer_id" => $params->user_id]]];

            // add to the email list to be sent by a cron job
            $stmt = $this->db->prepare("INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, 
                users_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?
            ");
            $stmt->execute(['password_reset', $params->user_id, $params->user_id, json_encode($reciepient),
                $params->user_id, "[{$this->appName}] Password Reset", $message
            ]);

            // reset the counter
            $this->session->reset_count = 0;
            
            // return true
            return [
                "code" => 200,
                "data" => "Your password was successfully changed.",
                "additional" => [
                    "clear" => true,
                    "href" => $this->session->user_current_url
                ]
            ];

        } catch(PDOException $e) {}
        
    }

    /**
     * Logout the user
     * 
     * @param \stdClass $params
     * @param String $params->user_id
     * 
     * @return Array
     */
    public function logout(stdClass $params = null) {
        // unset the sessions
        $this->session->remove([
            "userLoggedIn", "userName", "current_url", "clientId", 
            "student_courses_id", "student_class_id", "student_id",
            "userId", "userRole", "activated", "client_subaccount", "recentSQLQuery",
            "student_csv_file", "course_csv_file", "staff_csv_file", "last_recordUpload"
        ]);
        // update the user online status
        $this->db->query("UPDATE users SET online='0' WHERE item_id='{$params->userId}' LIMIT 1 ");

        // perform any additional query if the need be here
        $this->session->destroy();

        // return success
        return ["code" => 200, "data" => "You have successfully been logged out."];
    }

    /**
     * Register a New School Account
     * 
     * Run tests for the email address that have been used to register for the account
     * 
     * @return Array
     */
    public function create(stdClass $params) {

        global $accessObject, $session;

        // check if the user has registered an account with the past 5 minutes
        if(!$this->check_time("clients_accounts", 5)) {
            return ["code" => 201, "data" => "Sorry! You are prohibited from registering multiple accounts within a short space of time."];
        }

        if(!filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
            return ["code" => 201, "data" => "Sorry! Please provide a valid email address."];
        }
        
        $username = explode("@", $params->email)[0];
        $contact = isset($params->school_contact) ? $this->format_contact($params->school_contact) : null;
        $contact_2 = isset($params->school_contact_2) ? $this->format_contact($params->school_contact_2) : null;

        if(empty($contact)) {
            return ["code" => 201, "data" => "Sorry! The contact number is required."];
        }

        if($contact === $contact_2) {
            return ["code" => 201, "data" => "Sorry! The contact numbers cannot be the same."];
        }
        
        try {

            // begin transaction
            $this->db->beginTransaction();

            // check if an account aready exists with the same email address
            $a_check = $this->pushQuery("client_status", "clients_accounts", "client_email='{$params->email}' LIMIT 1");

            // perform the checks
            if(!empty($a_check)) {
                if($a_check[0]->client_status == 0) {
                    return ["code" => 201, "data" => "Sorry! You already have an account pending verification. Please wait while we complete the verification process."];
                } elseif($a_check[0]->client_status == 1) {
                    return ["code" => 201, "data" => "Sorry! You already have an account. Please try to login instead or use the reset password option if you have forgotten your password."];
                }
            }

            // confirm if the username already exists
            $u_check = $this->pushQuery("username", "users", "username='{$username}' LIMIT 1");

            // if it does then set a new username for the admin user by appending a number to it.
            if(!empty($u_check)){
                $username = $username.rand(1, 9);
            }           

            // get the details of the tiral account
            $package = $this->pushQuery("*", "clients_packages", "package='Trial' LIMIT 1");
            $package = (array) $package[0];
            unset($package["id"]);

            // get the prefix from the school name
            $name = explode(" ", $params->school_name);
            $prefix = "";

            // get the name for a unique id
            foreach($name as $word) {
                $prefix .= ucwords($word[0]);
            }

            // create a token
            $user_id = "{$prefix}U".$this->append_zeros(1, 6);
            $item_id = random_string("alnum", RANDOM_STRING);
            $token = random_string("alnum", 32);

            // the preferences
            $preference = (object) [
                "labels" => [
                    "staff" => "{$prefix}U",
                    "student" => "$prefix",
                    "parent" => "{$prefix}P",
                    "receipt" => "R{$prefix}"
                ],
                "sessions" => [
                    "session" => "Term"
                ],
                "academics" => [
                    "academic_year" => date("Y") . "/" . (date("Y") - 1),
                    "academic_term" => "",
                    "term_starts" => "",
                    "term_ends" => "",
                    "next_academic_year" => "",
                    "next_academic_term" => "",
                    "next_term_starts" => "",
                    "next_term_ends" => ""
                ],
                "account" => [
                    "package" => "trial",
                    "activation_code" => $token,
                    "date_created" => date("Y-m-d h:iA"),
                    "expiry" => date("Y-m-d h:iA", strtotime("+1 months"))
                ],
                "opening_days" => $this->default_opening_days,
                "features_list" => array_keys($this->features_list)
            ];

            // merge the array information
            $new = array_merge($preference->account, $package);
            $preference->account = $new;

            // check the user password to see if it meets the requirements
            if(!passwordTest($params->password)) {
                return ["code" => 201, "data" => $this->password_ErrorMessage];
            }
            $password =  password_hash($params->password, PASSWORD_DEFAULT);

            // get the last client id
            $last_id = $this->lastRowId("clients_accounts") + 1;
            
            // create a client id
            $client_id = "MSGH".$this->append_zeros($last_id, 5);

            // create and insert a new event with the slug public holiday
            $evt_stmt = $this->db->prepare("INSERT INTO events_types SET client_id = ?, item_id = ?");
            $evt_stmt->execute([$client_id, random_string("alnum", RANDOM_STRING)]);

            // gift 10 sms messages to the client
            $sms_stmt = $this->db->prepare("INSERT INTO smsemail_balance SET client_id = ?, sms_balance = ?");
            $sms_stmt->execute([$client_id, 10]);

            // insert the academic terms information for the client
            $this->db->query("INSERT INTO academic_terms SET client_id = '{$client_id}', name='1st', description='1st Term'");
            $this->db->query("INSERT INTO academic_terms SET client_id = '{$client_id}', name='2nd', description='2nd Term'");
            $this->db->query("INSERT INTO academic_terms SET client_id = '{$client_id}', name='3rd', description='3rd Term'");
            
            // insert the client details
            $client_stmt = $this->db->prepare("INSERT INTO clients_accounts SET 
                client_id = ?, client_name = ?, client_contact = ?, client_email = ?, client_preferences = ?, ipaddress = ?
                ".(isset($params->school_address) ? ",client_address='{$params->school_address}'" : null)."
                ".(isset($contact_2) ? ",client_secondary_contact='{$contact_2}'" : null)."
            ");
            $client_stmt->execute([$client_id, $params->school_name, $contact, $params->email, json_encode($preference), ip_address()]);

            // get the user permissions
		    $accessPermissions = $accessObject->getPermissions("admin");

            // load the access level permissions
			$permissions = $accessPermissions[0]->user_permissions;
			$access_level = $accessPermissions[0]->id;

            // insert the user account details
            $ac_stmt = $this->db->prepare("INSERT INTO users SET 
                item_id = ?, unique_id = ?, client_id = ?, access_level = ?, password = ?, user_type = ?, 
                address = ?, username = ?, verify_token = ?, user_status = ?, email = ?, phone_number = ?, status = ?,
                changed_password = ?
            ");
            $ac_stmt->execute([
                $item_id, $user_id, $client_id, $access_level, $password, "admin", 
                $params->school_address, $username, $token, "Pending", $params->email, $contact, 1, 1
            ]);

            // log the user access level
			$stmt2 = $this->db->prepare("INSERT INTO users_roles SET user_id = ?, client_id = ?, permissions = ?");
			$stmt2->execute([$item_id, $client_id, $permissions]);

            // insert the client into the limits table
            $this->db->query("INSERT INTO clients_accounts_limit SET client_id = '{$client_id}'");

            // insert the school grading remarks record
            $this->db->query("INSERT INTO grading_remarks_list SET client_id = '{$client_id}'");

            // send a message to the user email
            $message = "Thank you for registering your School: <strong>{$params->school_name}</strong> with {$this->appName}
                        We are pleased to have you join and benefit from our platform.<br><br>
                        Your can login with your <strong>Email Address:</strong> {$params->email} or <strong>Username:</strong> {$username}
                        and the password that was provided during signup.<br><br>";
			$message .= "One of our personnel will get in touch shortly to assist you with additional setup processes that is required to aid you quick start the usage of the application.<br></br>";
			$message .= "<a href='{$this->baseUrl}verify?dw=account&token={$token}'><strong>Click Here</strong></a> to verify your Email Address and also to activate the account.<br><br>";

            // recipient list
            $reciepient = ["recipients_list" => [["fullname" => $params->school_name, "email" => $params->email, "customer_id" => $item_id]]];
            
            // insert the email content to be processed by the cron job
            $m_stmt = $this->db->prepare("INSERT INTO users_messaging_list SET  
                template_type = ?, client_id = ?, item_id = ?, recipients_list = ?, 
                created_by = ?, subject = ?, message = ?, users_id = ?
            ");
            $m_stmt->execute([
                'verify_account', $client_id, $item_id, json_encode($reciepient),
                $item_id, "[{$this->appName}] Account Verification", $message, $item_id
            ]);

            // create the client css file
            $fd = @fopen("assets/css/clients/{$client_id}.css", "w");

            // insert the user activity
            $this->userLogs("verify_account", $item_id, null, "{$params->school_name} created a new Account pending Verification.", $item_id, $client_id);

            // auto log the user in and show notification message at the top of the page
            $session->set([   
                "userLoggedIn" => random_string('alnum', 50),
                "userName" => $username,
                "clientId" => $client_id,
                "userId" => $item_id,
                "userRole" => $access_level,
                "last_TimetableId" => true,
                "initialAccount_Created" => true,
                "activated" => 0
            ]);

            // commit the statements
            $this->db->commit();

            // create the account
            return [
                "code" => 200,
                "data" => "Your account has successfully been created. Please check your email for the Verification Link.",
                "clear" => true
            ];

        } catch(PDOException $e) {
            // reverse any transaction
            $this->db->rollBack();
            // return the error message
            return ["code" => 201, "data" => "Sorry! An error occured while processing the request."];
        }

    }

    /**
     * Password Manager Control
     * 
     * @param Array $params->data       This contains all the required data
     * 
     * @return Array
     */
    public function password_manager(stdClass $params) {

        try {

            global $noticeClass, $accessObject, $isSupport, $isAdmin;

            // confirm if the user has the required permission
            if(!$accessObject->hasAccess("change_password", "permissions")) {
                return ["code" => 203, "data" => $this->permission_denied];
            }

            if(!is_array($params->data)) {
                return ["code" => 203, "data" => "Sorry! Invalid data parsed."];
            }

            // confirm that the request and request id were parsed
            if(!isset($params->data["request"])) {
                return ["code" => 203, "data" => "Sorry! Invalid data parsed."];
            }

            // set parameters
            $request = $params->data["request"];
            $request_id = $params->data["request_id"] ?? null;
            $message = "Request was successfully processed.";

            // check the request parsed
            if(!in_array($request, ["change", "cancel", "resend", "modify"])) {
                return ["code" => 203, "data" => "Sorry! Invalid data parsed."];
            }

            // run this section if the request is either to change, cancel or resend token
            if(in_array($request, ["change", "cancel", "resend"])) {
                // clean the request_id
                $request_id = xss_clean($request_id);

                // confirm that the request and request id were parsed
                if(empty($request_id)) {
                    return ["code" => 203, "data" => "Sorry! Invalid data parsed."];
                }
                
                // run the check
                $check = $this->pushQuery("id, user_id, username", "users_reset_request", "item_id = '{$request_id}' LIMIT 1");

                // confirm that the request exists
                if(empty($check)) {
                    return ["code" => 203, "data" => "Sorry! Invalid request id was parsed for processing."];
                }

                // send a notification to the user
                $notice_param = (object) [
                    '_item_id' => $request_id,
                    'user_id' => $check[0]->user_id,
                    'subject' => "Password Change",
                    'username' => $check[0]->username,
                    'remote' => false, 
                    'notice_type' => 4,
                    'userId' => $params->userId,
                    'clientId' => $params->clientId,
                    'initiated_by' => 'system'
                ];
            }

            // set some new variables
            $ip = ip_address();
            $br = $this->browser." ".$this->platform;

            // if the request is cancel
            if($request == "cancel") {
                // cancel request token
                $this->db->query("UPDATE users_reset_request SET request_token = NULL, token_status = 'ANNULED' WHERE item_id = '{$request_id}' LIMIT 1");
                
                // add a new notification
                $notice_param->message = "Your password reset request was cancelled by <strong>{$params->userData->name}.";
                $notice_param->subject = "Password Reset Cancelled";
                $noticeClass->add($notice_param);

                // set the message to submit
                $message = "Password change request successfully cancelled.";
            }

            // change password
            elseif($request == "change") {

                // if the password parameter was not parsed
                if(!isset($params->data["password"]) || !isset($params->data["password_2"])) {
                    return ["code" => 203, "data" => "Sorry! The password parameter is request."];
                }

                // if the password is empty
                if(empty($params->data["password"]) || empty($params->data["password_2"])) {
                    return ["code" => 203, "data" => "Sorry! The password parameter cannot be empty."];
                }

                // password test
                if(!passwordTest($params->data["password"])) {
                    return ["code" => 203, "data" => $this->password_ErrorMessage_2 ];
                }

                // confirm if the passwords match
                if($params->data["password"] !== $params->data["password_2"]) {
                    return ["code" => 203, "data" => "Sorry! The passwords supplied does not match."];
                }

                // change the password
                $stmt = $this->db->prepare("UPDATE users SET password = ?, last_password_change='{$this->current_timestamp}' WHERE item_id = ? AND client_id = ? LIMIT 10");
                $stmt->execute([password_hash($params->data["password"], PASSWORD_DEFAULT), $check[0]->user_id, $params->clientId]);

                // process the form
                $stmt = $this->db->query("UPDATE users_reset_request SET request_token=NULL, reset_date='{$this->current_timestamp}', reset_agent='{$br}|{$ip}', token_status='USED', expiry_time='".time()."', changed_by = '{$params->userId}' WHERE item_id='{$request_id}' LIMIT 1");

                // add a new notification
                $notice_param->message = "Your password was successfully changed by <strong>{$params->userData->name}.";
                $noticeClass->add($notice_param);

                // set the message to submit
                $message = "Password change request successfully changed.";
            }

            // change username and/or password
            elseif($request == "modify") {

                // set the username
                $request_id = xss_clean($params->data["user_id"]);
                $username = $params->data["username"] ?? null;

                // confirm that the user_id, password and password_2 variables were all parsed
                if(!isset($params->data["user_id"]) || !isset($params->data["password"]) || !isset($params->data["password_2"])) {
                    return ["code" => 203, "data" => "Sorry! The user_id, password and password_2 variables are all required."];
                }

                // if the password is empty
                if(empty($params->data["password"]) || empty($params->data["password_2"])) {
                    return ["code" => 203, "data" => "Sorry! The password parameter cannot be empty."];
                }

                // password test
                if(!passwordTest($params->data["password"])) {
                    return ["code" => 203, "data" => $this->password_ErrorMessage_2 ];
                }

                // confirm if the passwords match
                if($params->data["password"] !== $params->data["password_2"]) {
                    return ["code" => 203, "data" => "Sorry! The passwords supplied does not match."];
                }

                // get the user details using the username
                $check = $this->pushQuery("id, username, item_id, email, name, user_type", "users", "item_id = '{$request_id}' ORDER BY id DESC LIMIT 1");
                
                // confirm that the request and request id were parsed
                if(empty($check)) {
                    return ["code" => 203, "data" => "Sorry! An invalid user id was parsed."];
                }

                // send a notification to the user
                $notice_param = (object) [
                    '_item_id' => $request_id,
                    'user_id' => $check[0]->item_id,
                    'subject' => "Password Changed",
                    'username' => $check[0]->username,
                    'remote' => false, 
                    'notice_type' => 4,
                    'userId' => $params->userId,
                    'clientId' => $params->clientId,
                    'initiated_by' => 'system'
                ];

                // username can only be changed by the support admin personnel
                if($isSupport || $isAdmin) {
                
                    // if the username parsed does not match the existing one
                    if(!empty($username) && ($username !== $check[0]->username)) {

                        // check the username to see if its available to be used
                        $check_2 = $this->pushQuery("id", "users", "username = '{$username}' AND item_id != '{$request_id}' ORDER BY id DESC LIMIT 1");
                        
                        // return error message
                        if(!empty($check_2)) {
                            return ["code" => 203, "data" => "Sorry! The username parsed is not available for use."];
                        }
                    }

                    // set the new password
                    $stmt = $this->db->prepare("UPDATE users SET 
                        ".(!empty($username) ? "username = '{$username}', " : null)."
                        password = ?, last_password_change='{$this->current_timestamp}', 
                        changed_password='0' WHERE item_id = ? AND client_id = ? LIMIT 1"
                    );
                    $stmt->execute([password_hash($params->data["password"], PASSWORD_DEFAULT), $request_id, $params->clientId]);

                    // add a new notification
                    $notice_param->message = "Your password was successfully changed by <strong>{$params->userData->name}.";
                    $notice_param->subject = "Password Changed";
                    $noticeClass->add($notice_param);

                    // reset the requests list
                    $stmt = $this->db->query("UPDATE users_reset_request SET request_token=NULL, reset_date='{$this->current_timestamp}', reset_agent='{$br}|{$ip}', token_status='ANNULED', expiry_time='".time()."', changed_by = '{$params->userId}' WHERE user_id='{$request_id}' AND token_status='PENDING' LIMIT 10");

                    // if the usernames are not the same
                    if(!empty($username) && ($username !== $check[0]->username)) {
                        // change the username in the login_history table
                        $stmt = $this->db->query("UPDATE users_login_history SET username='{$username}' WHERE username='{$check[0]->username}' AND client_id='{$params->clientId}' ORDER BY id DESC LIMIT 500");
                        $stmt = $this->db->query("UPDATE users_reset_request SET username='{$username}' WHERE username='{$check[0]->username}' AND client_id='{$params->clientId}' ORDER BY id DESC LIMIT 500");
                    }
                }

            }

            return ["additional" => ["request" => $request], "data" => $message];


        } catch(PDOException $e) {}

    }
    
}