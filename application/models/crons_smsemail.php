<?php
 
class SMS_CronJOB {

	private $db;
	private $ini_data;
	private $sms_sender = "MySchoolGH";
	private $mailAttachment = array();
	private $siteName = "MySchoolGH - EmmallexTech.Com";
	private $mnotify_key;

	public function __construct() {
		// INI FILE
		$this->ini_data = parse_ini_file("db.ini");

		// set some more variables
		$this->baseUrl = $this->ini_data["base_url"];
		$this->rootUrl = $this->ini_data["root_url"];
		$this->mnotify_key = $this->ini_data["mnotify_key"];
		
		$this->dbConn();

		require $this->rootUrl."system/libraries/phpmailer.php";
		require $this->rootUrl."system/libraries/smtp.php";
	}
	
	/**
	 * Run the connection to the database
	 * 
	 * @return $this
	 */
	private function dbConn() {
		
		// CONNECT TO THE DATABASE
		$connectionArray = array(
			'hostname' => $this->ini_data['hostname'],
			'database' => $this->ini_data['database'],
			'username' => $this->ini_data['username'],
			'password' => $this->ini_data['password']
		);

		// run the database connection
		try {
			$conn = "mysql:host={$connectionArray['hostname']}; dbname={$connectionArray['database']}; charset=utf8";			
			$this->db = new PDO($conn, $connectionArray['username'], $connectionArray['password']);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
		} catch(PDOException $e) {
			die("Database Connection Error: ".$e->getMessage());
		}

		return $this->db;

	}

    /**
     * Send SMS Messages
     * 
     * @return String
     */
    public function send_smsemail() {

        $stmt = $this->db->prepare("SELECT a.*, c.client_name, c.sms_sender
			FROM smsemail_send_list a
			LEFT JOIN clients_accounts c ON c.client_id = a.client_id
			WHERE a.sent_status='Pending' LIMIT 10
		");
        $stmt->execute();

        $data = array();
        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result->recipient_list = json_decode($result->recipient_list, true);
            $data[$result->item_id] = $result;
        }

        // loop through the results list
        foreach($data as $key => $value) {
            
			// append the list
			if(time() > strtotime($value->schedule_time)) {

				// if the message type is sms
				if(($value->type === "sms")) {

					// get the response of the request
					$response = $this->mnotify_send($key, $value->message, $value->recipient_list, $value->sms_sender);
					
					// save the  reponse
					if(!empty($response)) {
						$this->db->query("UPDATE smsemail_send_list SET sent_status='Delivered', sent_time=now() WHERE item_id='{$key}' LIMIT 1");
					} else {
						print "Sorry! The sms message could not be delivered to the purported users.\n";
					}
				} elseif($value->type === "email") {

					// get the response of the request
					$response = $this->phpmailer_send($key, $value->message, $value->recipient_list, $value->subject);
					
					// save the  reponse
					if(!empty($response)) {
						$this->db->query("UPDATE smsemail_send_list SET sent_status='Delivered', sent_time=now() WHERE item_id='{$key}' LIMIT 1");
					} else {
						print "Sorry! The email message could not be delivered to the purported users.\n";
					}

				} elseif($value->type === "reminder") {
					// open up the send_mode column
					$send_mode = json_decode($value->send_mode, true);

					// if the message must be sent via sms.
					if(in_array("sms", $send_mode)) {
						// get the response of the request
						$response = $this->mnotify_send($key, $value->message, $value->recipient_list, $value->sms_sender);						
					}

					// save the  reponse
					if(!empty($response)) {
						$this->db->query("UPDATE smsemail_send_list SET sent_status='Delivered', sent_time=now() WHERE item_id='{$key}' LIMIT 1");
					} else {
						print "Sorry! The message could not be delivered to the purported users.\n";
					}
				}

			}
        }

    }

	/**
	 * Load the student bill and send via Email
	 * 
	 * 
	 * @return Bool
	 */
	public function send_student_bill() {

		try {

			// send 50 mails at a time
			$stmt = $this->db->prepare("SELECT id, recipient_list, bill FROM users_bills WHERE sent_status='0' LIMIT 50");
		    $stmt->execute();

		    // loop through the results list
		    while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
		    	// convert the list to array
		    	$list = json_decode($result->recipient_list, true);
		    	foreach($list as $key => $value) {
		    		$user = ["name" => $key, "email" => $value];
		    	}
		    	// send the mail
		    	$send = $this->phpmailer_send(true, $result->bill, $user, "Student Bill");

		    	// update the status
		    	if($send) {
		    		$this->db->query("UPDATE users_bills SET sent_status='1', sent_date=now() WHERE id='{$result->id}' LIMIT 1");
		    	}
		    }

		} catch(PDOException $e) {
			print $e->getMessage() . "\n";
		}

	}

	/**
	 * Send Message with MNotify Api
	 * 
	 * @param String 	$item_id
	 * @param String 	$message
	 * @param Array		$recipients
	 * @param String	$sender
	 * 
	 * @return Object
	 */
	public function mnotify_send($item_id, $message, $recipients, $sender) {

		if(empty($recipients) || !is_array($recipients)) {
			return false;
		}
		
		// get the list of all recipients
		$recipients_contact = array_column($recipients, "phone_number");
		$recipients_join = implode(",", $recipients_contact);

		//open connection
        $ch = curl_init();

		// set the field parameters
        $fields_string = [
            "key" => $this->mnotify_key,
			"recipient" => $recipients_contact,
			"sender" => empty($sender) ? $this->sms_sender : $sender,
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
        
		return $result;

	}

	/**
	 * Send the Email Message to the List of Recipients
	 *  
	 * @return Object
	 */
	public function phpmailer_send($item_id, $message, $recipient_list, $subject = null, $cc_list = null) {

		//Create an instance; passing `true` enables exceptions
		$mailer = new PHPMailer(true);

		// set the message 
		$message = htmlspecialchars_decode($message);

		// configuration settings
		$config = (Object) array(
			'subject' => $subject,
			'headers' => "From: {$this->siteName} - MySchoolGH.Com<{$this->ini_data["smtp_user"]}> \r\n Content-type: text/html; charset=utf-8",
			'Smtp' => true,
			'SmtpHost' => $this->ini_data["smtp_host"],
			'SmtpPort' => $this->ini_data["smtp_host"],
			'SmtpUser' => $this->ini_data["smtp_user"],
			'SmtpPass' => $this->ini_data["smtp_password"],
			'SmtpSecure' => 'ssl'
		);

		// additional settings
		$mailer->SMTPDebug = SMTP::DEBUG_SERVER;
		$mailer->isSMTP();
		$mailer->Host = $config->SmtpHost;
		$mailer->SMTPAuth = true;
		$mailer->Username = $config->SmtpUser;
		$mailer->Password = $config->SmtpPass;
		$mailer->SMTPSecure = $config->SmtpSecure;
		$mailer->Port = $config->SmtpPort;
		$mailer->Subject = $subject;
		
		// attach documents if any was found
		if(!empty($this->mailAttachment)) {
			// loop through the attachments list
			foreach($this->mailAttachment as $theAttachment) {
				// file path
				$filepath = $theAttachment["path"];
				// append the attachment to the mail
				$mailer->AddAttachment($filepath, $theAttachment["name"]);
			}
		}

		// set the user from which the email is been sent
		$mailer->setFrom($this->ini_data["smtp_from"], $this->siteName);

		// loop through the list of recipients for this mail
        foreach($recipient_list as $emailRecipient) {
        	// if the email was parsed
        	if(isset($emailRecipient['name']) && !empty($emailRecipient['email'])) {
				// user fullname
				$fullname = $emailRecipient['name'];
				// append the email address
				$mailer->addAddress($emailRecipient['email'], $fullname);
			}
		}

		// loop through the list of cc if not empty
		if(!empty($cc_list)) {
			// loop through the copied list
			foreach($cc_list as $copiedRecipient) {
				// if the email address is not empty
				if(!empty($copiedRecipient['email'])) {
					// user fullname
					$fullname = isset($copiedRecipient['name']) ? $copiedRecipient['name'] : $copiedRecipient['name'];
					// append the email address
					$mailer->addCC($copiedRecipient['email'], $fullname);
				}
			}
		}

		// this is an html message
		$mailer->isHTML(true);

		// set the subject and message
		$mailer->Body    = $message;
		$mailer->AltBody = strip_tags($message);
		
		// send the email message to the users
		if($mailer->send()) {
			return true;
		} else {
 			return false;
		}

	}

    /**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	int	number of characters
	 * @return	string
	 */
	private function random_string($len = 8) {
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}

}

// create new object
$jobs = new SMS_CronJOB;
$jobs->send_smsemail();
$jobs->send_student_bill();
?>