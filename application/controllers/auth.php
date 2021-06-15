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
                    u.last_timetable_id, c.client_state, u.user_status
                FROM users u
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

                        // check the client state
                        if($results->client_state === "Pending") {
                            // return ["code" => 201, "data" => "Sorry! You must first activate your account to continue."];
                        }

                        // verify the password
                        if(password_verify($params->password, $results->password)) {

                            // last login trial
                            $lastLogin = $this->pushQuery("attempts", "users_access_attempt", "username='{$params->username}' AND attempt_type='login'");
                            
                            // if the last login information is not empty
                            if(!empty($lastLogin)) {

                                // get the user record
                                $last_attempt = $lastLogin[0]->attempts;

                                // if the attempt is 4 or more then lodge a notification to the user
                                if($last_attempt >= 4) {

                                    // form the notification parameters
                                    $params = (object) [
                                        '_item_id' => random_string("alnum", 32),
                                        'user_id' => $results->user_id,
                                        'subject' => "Login Failures",
                                        'username' => $params->username,
                                        'remote' => false, 
                                        'message' => "An attempt count of <strong>{$last_attempt}</strong> was made to access your Account. We recommend that you change your password to help secure it. <a href=\"{{APPURL}}profile\">Visit your profile</a> to effect the changes.",
                                        'notice_type' => 3,
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

                                // set the last timetable id in session
                                $session->set("last_TimetableId", $results->last_timetable_id);

                                // set additional session for student
                                if($results->user_type === "student") {
                                    $session->set("student_id", $results->user_id);
                                }

                                // set a general session for all except for a parent user
                                if($results->user_type !== "parent") {
                                    $session->set("ready_App", true);
                                }
                            }
                            
                            // unset session locked
                            $session->userSessionLocked = null;

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

                            // log the history record
                            $stmt = $this->db->prepare("INSERT INTO users_login_history 
                                SET username='{$params->username}', client_id='{$results->client_id}', log_ipaddress='{$ip}', log_browser='{$br}', 
                                user_id='{$session->userId}', log_platform='{$this->agent}'
                            ");
                            $stmt->execute();

                            // update the last login for this user
                            $stmt = $this->db->prepare("UPDATE users SET last_login=now(), last_visited_page='{{APPURL}}dashboard', last_seen = now() WHERE item_id=? LIMIT 1");
                            // $stmt->execute([$results->user_id]);

                            // commit all transactions
                            $this->db->commit();

                            // response to return
                            return [
                                "code" => 200,
                                "data" => "Login successful. Redirecting", 
                                "refresh" => 1000
                            ];

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
        $token = random_string("alnum", mt_rand(68, 70));
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
            $stmt = $this->db->prepare("UPDATE users_api_keys SET status = ? WHERE (TIMESTAMP(expiry_timestamp) < CURRENT_TIME()) AND access_type = ? LIMIT 100");
            $stmt->execute([0, 'temp']);

            // create the temporary token
            $this->db->query("INSERT INTO users_api_keys 
                SET user_id = '{$params->user_id}', username = '{$params->username}', access_token = '".password_hash($token, PASSWORD_DEFAULT)."', access_type = 'temp', 
                expiry_date = '".date("Y-m-d")."', expiry_timestamp = now(), requests_limit = '5000', access_key = '{$token}'
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
                        $random_string = random_string("alnum", 32);
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
                            request_token='$request_token', user_agent='$br|$ip', 
                            expiry_time='$expiry_time'
                        ");
                        $stmt->execute();
                        
                        #FORM THE MESSAGE TO BE SENT TO THE USER
                        $message = 'Hi '.$fullname.'<br>You have requested to reset your password at '.config_item('site_name');
                        $message .= '<br><br>Before you can reset your password please follow this link. The reset link expires after 2 hours<br><br>';
                        $message .= '<a class="alert alert-success" href="'.config_item('base_url').'verify?dw=password&token='.$request_token.'">Click Here to Reset Password</a>';
                        $message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
                        $message .= config_item('base_url').'verify?dw=password&token='.$request_token;

                        // recipient list
                        $reciepient = ["recipients_list" => [["fullname" => $fullname,"email" => $params->email,"customer_id" => $user_id]]];
                        
                        // insert the email content to be processed by the cron job
                        $stmt = $this->db->prepare("
                            INSERT INTO users_messaging_list 
                            SET  template_type = ?, client_id = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?
                        ");
                        $stmt->execute([
                            'password-recovery', $results->client_id, $random_string, json_encode($reciepient),
                            $user_id, "[".config_item('site_name')."] Change Password", $message, $user_id
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
                        return ["code" => 200, "data" => "Please check your email for steps to reset password."];

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
        $baseUrl = config_item("base_url");

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
                    $message = 'Hi '.$fullname.'<br>You have successfully changed your password at '.config_item('site_name');
                    $message .= '<br><br>Do ignore this message if your rightfully effected this change.<br>';
                    $message .= '<br>If not, do ';
                    $message .= '<a class="alert alert-success" href="'.$baseUrl.'recover">Click Here</a> if you did not perform this act.';

                    #send email to the user
                    $reciepient = ["recipients_list" => [["fullname" => $fullname, "email" => $email, "customer_id" => $user_id]]];

                    // add to the email list to be sent by a cron job
                    $stmt = $this->db->prepare("
                        INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, 
                        users_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?
                    ");
                    $stmt->execute([
                        'password-recovery', $user_id, $user_id, json_encode($reciepient),
                        $user_id, "[".config_item('site_name')."] Change Password", $message
                    ]);

                    // send a notification to the user
                    $params = (object) [
                        '_item_id' => random_string("alnum", 32),
                        'user_id' => $user_id,
                        'subject' => "Password Reset",
                        'message' => "You have successfully changed your password.",
                        'notice_type' => 4,
                        'userId' => $user_id,
                        'initiated_by' => 'system'
                    ];

                    // add a new notification
                    $noticeClass->add($params);

                    // update the initial notification sent to the user for the request to change password
                    $this->db->query("UPDATE users_notification SET confirmed='1' WHERE item_id='{$request_item_id}' LIMIT 1");

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
            "userLoggedIn", "userName", "current_url",
            "userId", "userRole", "activated",
            "complaints", "claims", "policies"
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
        if(!$this->check_time("clients_accounts", 0.1)) {
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

            // check if an account aready exists with the same email address
            $a_check = $this->pushQuery("client_status", "clients_accounts", "client_email='{$params->email}' LIMIT 1");

            // perform the checks
            if(!empty($a_check)) {
                if($a_check[0]->client_status == 0) {
                    return ["code" => 201, "data" => "Sorry! You already have an account pending verification. Please wait while we verify."];
                } elseif($a_check[0]->client_status == 1) {
                    return ["code" => 201, "data" => "Sorry! You already have an account. Please try login instead."];
                }
            }
            $u_check = $this->pushQuery("username", "users", "username='{$username}' LIMIT 1");
            if(!empty($u_check)){
                return ["code" => 201, "data" => "Sorry! There is an existing account with the same email address. Please try login instead."];
            }

            // get the prefix from the school name
            $name = explode(" ", $params->school_name);
            $prefix = "";

            // get the name for a unique id
            foreach($name as $word) {
                $prefix .= ucwords($word[0]);
            }

            // create a token
            $user_id = "{$prefix}U".$this->append_zeros(1, 6);
            $item_id = random_string("alnum", 32);
            $token = random_string("alnum", 54);

            // the preferences
            $preference = (object) [
                "labels" => [
                    "staff" => "{$prefix}U",
                    "student" => "$prefix",
                    "parent" => "{$prefix}P",
                    "receipt" => "R{$prefix}"
                ],
                "academics" => [
                    "academic_year" => date("Y") . "/" . (date("Y") - 1),
                    "academic_term" => "",
                    "next_academic_year" => "",
                    "next_academic_term" => ""
                ],
                "account" => (object) [
                    "type" => $params->plan ?? "basic",
                    "activation_code" => $token,
                    "date_created" => date("Y-m-d h:iA"),
                    "expiry" => date("Y-m-d h:iA", strtotime("+1 months"))
                ],
                "opening_days" => $this->default_opening_days,
            ];

            // check the user password to see if it meets the requirements
            if(!passwordTest($params->password)) {
                return ["code" => 201, "data" => $this->password_ErrorMessage];
            }
            $password =  password_hash($params->password, PASSWORD_DEFAULT);

            // get the last client id
            $last_id = $this->lastRowId("clients_accounts") + 1;
            
            // create a client id
            $client_id = "MSGH".$this->append_zeros($last_id, 6);

            // create and insert a new event with the slug public holiday
            $evt_stmt = $this->db->prepare("INSERT INTO events_types SET client_id = ?, item_id = ?");
            $evt_stmt->execute([$client_id, random_string("alnum", 32)]);

            // gift 20 sms messages to the client
            $sms_stmt = $this->db->prepare("INSERT INTO smsemail_balance SET client_id = ?, sms_balance = ?");
            $sms_stmt->execute([$client_id, 20]);
            
            // insert the client details
            $client_stmt = $this->db->prepare("INSERT INTO clients_accounts SET 
                client_id = ?, client_name = ?, client_contact = ?, client_email = ?, client_preferences = ?, ip_address = ?
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
                address = ?, username = ?, verify_token = ?, user_status = ?, email = ?, phone_number = ?, status = ?
            ");
            $ac_stmt->execute([
                $item_id, $user_id, $client_id, $access_level, $password, "admin", 
                $params->school_address, $username, $token, "Pending", $params->email, $contact, 1
            ]);

            // log the user access level
			$stmt2 = $this->db->prepare("INSERT INTO users_roles SET user_id = ?, client_id = ?, permissions = ?");
			$stmt2->execute([$item_id, $client_id, $permissions]);

            // send a message to the user email
            $message = "Thank you for registering your School: <strong>{$params->school_name}</strong> with ".config_item('site_name').".
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
                $user_id, "[".config_item('site_name')."] Account Verification", $message, $item_id
            ]);

            // insert the user activity
            $this->userLogs("verify_account", $item_id, null, "{$params->school_name} created a new Account pending Verification.", $item_id, $client_id);

            // auto log the user in and show notification message at the top of the page
            $session->set([   
                "userLoggedIn" => random_string('alnum', 50),
                "userName" => $username,
                "clientId" => $client_id,
                "userId" => $user_id,
                "userRole" => $access_level,
                "last_TimetableId" => true,
                "activated" => 0,
                "ready_App" => true
            ]);

            // create the account
            return [
                "code" => 200,
                "data" => "Your account has successfully been created. Please check your email for the Verification Link.",
                "clear" => true
            ];

        } catch(PDOException $e) {
            return ["code" => 201, "data" => "Sorry! An error occured while processing the request."];
        }

    }

}