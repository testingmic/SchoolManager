<?php
define('ROOT_DIRECTORY', dirname(dirname(__DIR__)));

require ROOT_DIRECTORY . "/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Crons {

	private $mailer;
	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $ini_data;
	private $limit = 2000;
	private $baseUrl;
	private $mnotify_key;
	private $sms_sender = "MySchoolGH";
	private $siteName = "MySchoolGH - EmmallexTech.Com";

	public function __construct() {
		// INI FILE
		$this->ini_data = parse_ini_file(ROOT_DIRECTORY . "/db.ini");

		// set some more variables
		$this->baseUrl = $this->ini_data["base_url"];
		$this->rootUrl = $this->ini_data["root_url"];
		$this->mnotify_key = $this->ini_data["mnotify_key"];

		$this->dbConn();
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
			$conn = "mysql:host={$connectionArray['hostname']};dbname={$connectionArray['database']};charset=utf8mb4";			
			$this->db = new PDO($conn, $connectionArray['username'], $connectionArray['password']);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
		} catch(PDOException $e) {
			die("Database Connection Error: ".$e->getMessage());
		}

		return $this->db;

	}

	/**
	 * Properly format the email message to send to the recipient
	 * 
	 * @param String $message
	 * @param Strig $subject
	 * 
	 * @return String
	 */
	private function generate_message($message, $subject) {

		$mailerContent = '
		<!DOCTYPE html>
        <html>
            <head>
                <title>'.$subject.'</title>
            </head>
            <body>
                <div style="margin: auto auto; width: 610px; box-shadow: 0px 1px 2px #000; border-radius: 5px">
                    <table width="600px" border="0" cellpadding="0" style="min-height: 400px; margin: auto auto;" cellspacing="0">
                        <tr style="padding: 5px; border-bottom: solid 1px #ccc;">
                            <td colspan="4" align="center" style="padding: 10px;">
                                <h1 style="margin-bottom: 0px; margin-top:0px">'.$this->siteName.'</h1>
                                <hr style="border: dashed 1px #ccc;">
                                <div style="font-family: Calibri Light; background: #ff5e3a; font-size: 20px; padding: 5px;color: white; text-transform: ; font-weight; bolder">
                                <strong>'.$subject.'</strong>
                                </div>
                                <hr style="border: dashed 1px #ccc;">
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 5px; font-family: Calibri Light; text-transform: ;">
                                '.$message.'
                            </td>
                        </tr>
                    </table>
                    <table width="600px">
                        <tbody style="text-align: center;">
                            <tr>
                                <td colspan="4">
                                    <hr style="border: dashed 1px #ccc; text-align: center;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </body>
        </html>';

		return $mailerContent;
	}
	
	/**
	 * General emails list. Get the list of emails to be sent to users and send them out.
	 * Includes messages for signup, logins, reset password and the likes.
	 */
	public function load_emails() {
		
		try {

			// run the query
			$stmt = $this->db->prepare("SELECT a.* FROM users_messaging_list a WHERE a.sent_status='0' AND a.deleted='0' LIMIT 100");
			$stmt->execute();

			$dataToUse = null;

			print "Looping through the emails list.\n";
			// looping through the content
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
			    
				// set the store content
				$this->userAccount = $result;
				$this->siteName = "MySchoolGH.Com";

				// commence the processing
				$subject = $result->subject;
				$dataToUse = $this->generate_message($result->message, $subject, $result->template_type);

				// use the content submitted
				if(!empty($dataToUse)) {
				    // print progress
				    print "Data found, processing the recipients list to send\n";

					// convert the recipient list to an array
					$recipient_list = json_decode($result->recipients_list, true);
					$recipient_list = $recipient_list["recipients_list"];
					
					// submit the data for processing
					$mailing = $this->send_emails($recipient_list, $subject, $dataToUse);

					// set the mail status to true
					if($mailing) {
						$this->db->query("UPDATE users_messaging_list SET sent_status = '1', date_sent=now() WHERE id='{$result->id}' LIMIT 1");
						print "Mails successfully sent\n";
					}

				}
			}

			print "Sending of user emails completed successfully.\n";

		} catch(PDOException $e) {
			print "\n{$e->getMessage()}";
		}

	}
	
	/**
	 * The function used to send the message using the PHPMailer plugin
	 * 
	 * @param Array $recipient_list
	 * @param String $subject
	 * @param String $message
	 * 
	 * @return Bool
	 */
	private function send_emails($recipient_list, $subject, $message, $cc_list = null) {

		// send the email via the library
		print "Message submitted for sending via PHPMailer\n";

		//Create an instance; passing `true` enables exceptions
		$mailer = new PHPMailer(true);

		// configuration settings
		$config = (Object) array(
			'subject' => $subject,
			'headers' => "From: {$this->siteName} - MySchoolGH.Com<{$this->ini_data["smtp_user"]}> \r\n Content-type: text/html; charset=utf-8",
			'Smtp' => true,
			'SmtpHost' => $this->ini_data["smtp_host"],
			'SmtpPort' => $this->ini_data["smtp_port"],
			'SmtpUser' => $this->ini_data["smtp_user"],
			'SmtpPass' => $this->ini_data["smtp_password"],
			'SmtpSecure' => 'ssl'
		);

		print "Setting the configuration params.\n";

		// additional settings
		$mailer->SMTPDebug = SMTP::DEBUG_OFF;
		$mailer->isSMTP();
		$mailer->Host = $config->SmtpHost;
		$mailer->SMTPAuth = true;
		$mailer->Username = $config->SmtpUser;
		$mailer->Password = $config->SmtpPass;

		// set the port to sent the mail
		$mailer->Port = $config->SmtpPort;

		// set the user from which the email is been sent
		$mailer->setFrom($this->ini_data["smtp_from"], $this->siteName);

		print "Attach all documents where applicable.\n";

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

		print "Append the receipient to the email list\n";
		
		$receipient = false;

		// loop through the list of recipients for this mail
        foreach($recipient_list as $emailRecipient) {
        	if(!empty($emailRecipient['email'])) {
				$receipient = true;
				// user fullname
				$fullname = isset($emailRecipient['fullname']) ? $emailRecipient['fullname'] : $emailRecipient['name'];
				// append the email address
				$mailer->addAddress($emailRecipient['email'], $fullname);
			}
		}

		if(empty($receipient)) return true;

		print "Append any copied email address list\n";

		// loop through the list of cc if not empty
		if(!empty($cc_list)) {
			// loop through the copied list
			foreach($cc_list as $copiedRecipient) {
				// if the email address is not empty
				if(!empty($copiedRecipient['email'])) {
					// user fullname
					$fullname = isset($copiedRecipient['fullname']) ? $copiedRecipient['fullname'] : $copiedRecipient['name'];
					// append the email address
					$mailer->addCC($copiedRecipient['email'], $fullname);
				}
			}
		}

		// this is an html message
		$mailer->isHTML(true);

		print "Final setting of the email content and subject.\n";

		// set the subject and message
		$mailer->Subject = $subject;
		$mailer->Body    = $message;
		$mailer->AltBody = strip_tags($message);
		
		// send the email message to the users
		print "Send the email content\n";
		if($mailer->send()) {
			print "Sending of email completed\n";
			return true;
		} else {
			print "Sending of emails failed\n";
 			return false;
		}
	}

    /**
     * Run the scheduler cron activities
     */
    public function scheduler() {

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT * FROM cron_scheduler WHERE status = ? AND CURRENT_TIME() > TIMESTAMP(active_date) ORDER BY id ASC LIMIT 10");
			$stmt->execute([0]);

			// loop through the result
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				print "Looping through the cron job activity list.\n";

				// if the processed is true
				$processed = false;

				// if the request is notification
				if($result->cron_type == "notification") {
					// get the query to process
					$query = $result->query;
					// prepare and execute the statement
					$query_stmt = $this->db->prepare($query);
					$query_stmt->execute();	
					// form the notification parameters
					$notice_param = (object) [
						'_item_id' => $this->random_string(16),
						'subject' => $result->subject,
						'remote' => false, 
						'notice_type' => $result->notice_code,
						'userId' => $result->user_id,
						'initiated_by' => 'system'
					];
					// loop through each result and share the notice with them
					while($the_result = $query_stmt->fetch(PDO::FETCH_OBJ)) {
						$notice_param->user_id = $the_result->user_id;
						$notice_param->message = "Hello {$the_result->name}, an announcement was posted for your review. Visit the Announcements section to view.";
						$this->notice_add($notice_param);
					}
					$processed = true;
				}

				// if the request is email
				elseif($result->cron_type == "email") {
					// convert the list to an object
					$query = json_decode($result->query);

					// form the notification parameters
					$notice_param = (object) [
						'_item_id' => $this->random_string(16),
						'subject' => $result->subject,
						'remote' => false, 
						'notice_type' => $result->notice_code,
						'userId' => $result->user_id,
						'initiated_by' => 'system'
					];

					foreach($query as $eachUser) {
						$notice_param->user_id = $eachUser->user_id;
						$notice_param->message = "Hello {$eachUser->name}, you have been sent an email message. Visit the emails section to view.";
						$this->notice_add($notice_param);
					}

					$processed = true;
					
				}

				// if a user information have been uploaded
				elseif($result->cron_type == "users_upload") {
					$this->users_upload_modification($result->item_id, $result->client_id);
					$processed = true;
				}

				// if the type is to manage the terminal report functionality
				elseif($result->cron_type == "terminal_report") {
					$this->terminal_report_handler($result->item_id);
					$processed = true;
				}

				// if the activity is to assign fees to a particular class
				elseif($result->cron_type == "assign_student_fees") {
					$this->assign_student_fees($result);
					$processed = true;
				}

				// if the query is to update the student parent information
				elseif($result->cron_type == "bulk_student_update") {
					$this->update_student_information($result->query);
					$this->update_class_ids($result->client_id);
					$processed = true;
				}

				// if the processed state is true
				if($processed) {
					// update the cron status
					$this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");
				}

			}

		} catch(PDOException $e) {
			print $e->getMessage();
		}

    }

    /**
     * Update the student information
     * 
     * @param string $result
     */
    public function update_student_information($result) {

    	try {

    		// convert to array
    		$students_array_list = json_decode($result, true);

    		// loop through the entire students list
			foreach($students_array_list as $student_id => $data) {

				// update the student record
				$this->db->query("UPDATE users SET last_updated = now()
					".(isset($data["phone_number_2"]) ? ", phone_number_2 = '{$data["phone_number_2"]}'" : null)."
					".(isset($data["phone_number"]) ? ", phone_number = '{$data["phone_number"]}'" : null)."
					".(isset($data["gender"]) ? ", gender = '{$data["gender"]}'" : null)."
					".(!empty($data["date_of_birth"]) ? ", date_of_birth = '{$data["date_of_birth"]}'" : null)."
					".(!empty($data["enrollment_date"]) ? ", enrollment_date = '{$data["enrollment_date"]}'" : null)."
					".(!empty($data["image"]) ? ", image = '{$data["image"]}'" : null)."
					WHERE id='{$student_id}' AND user_type = 'student' LIMIT 1
				");

			}

			print "\nStudents data information successfully updated.\n";

    	} catch(PDOException $e) {}

    }

	/**
	 * Assign Fees Schedule to a List of Students
	 * 
	 * @return Bool
	 */
	private function assign_student_fees($result) {

		// assign some variables
		$class_id = $result->subject;
		$client_id = $result->client_id;
		$students_list = json_decode($result->query, true);
		
		// get the class fees schedule
		$fees_sql = $this->db->prepare("SELECT category_id, amount, currency FROM fees_allocations WHERE
			class_id = ? AND client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 100
		");
		$fees_sql->execute([$class_id, $client_id, $result->academic_year, $result->academic_term]);

		// assign the results list
		$fees_schedule = $fees_sql->fetchAll(PDO::FETCH_OBJ);
		$students_insert = null;
		$students_update = null;

		// run this query if the fees_schedule is not empty
		if(!empty($fees_schedule)) {

			// loop through the students list
			foreach($students_list as $student) {

				// delete all fees payments owed by the student
				$students_update .= "UPDATE fees_payments SET status = '0' WHERE client_id = '{$client_id}' AND
					student_id = (SELECT item_id FROM users WHERE client_id = '{$client_id}' AND id='{$student}' LIMIT 1) LIMIT 100;";
				
				// loop through the fees schedule list
				foreach($fees_schedule as $category) {
					// create the checkout url
					$checkout_url = random_string(16);
					
					// insert the record
					$students_insert .= "INSERT INTO fees_payments SET 
						client_id = '{$client_id}', checkout_url='{$checkout_url}',
						student_id = (SELECT item_id FROM users WHERE client_id = '{$client_id}' AND id='{$student}' LIMIT 1),
						class_id = '{$class_id}', category_id = '{$category->category_id}', currency = '{$category->currency}',
						amount_due = '{$category->amount}', balance = '{$category->amount}',
						created_by = '{$result->user_id}', academic_year = '{$result->academic_year}',
						academic_term = '{$result->academic_term}';";
				}
				// print some information
				print "Successfully prepared the fees allocation for Student with ID: {$student}\n";
			}
			// execute the prepared statements
			if(!empty($students_update)) {
				$this->db->query($students_update);
				print "\nInitial fees allocation for students have successfully been discarded.\n";
			}
			if(!empty($students_insert)) {
				$this->db->query($students_insert);
				print "Insertion of new fees allocation for students was successful.\n";
			}
		}
		print "Operation successfully completed.\n";
	}

	/**
	 * Perform some modification on the users list
	 * 
	 * @return Bool
	 */
	private function users_upload_modification($upload_id, $clientId) {
		// set the fullname of the user
		$u_stmt = $this->db->prepare("UPDATE users SET 
			name = CONCAT(firstname,' ', lastname,' ', othername), 
			client_id = UPPER(client_id) WHERE upload_id='{$upload_id}' AND client_id='{$clientId}' LIMIT 2000
		");
		$u_stmt->execute();
	}	

	/**
	 * Perform some modification on the users list
	 * 
	 * @return Bool
	 */
	private function terminal_report_handler($report_id) {
		
		try {
			// set the fullname of the user
			$u_stmt = $this->db->prepare("
				UPDATE grading_terminal_scores a SET 
					a.student_item_id = (SELECT u.item_id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
					a.student_name = (SELECT u.name FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
					a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1),
					a.student_row_id = (SELECT u.id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1)
				WHERE a.report_id='{$report_id}' LIMIT 1
			");
			$u_stmt->execute();

			// get the list of all users that was uploaded
			$u_stmt = $this->db->prepare("UPDATE grading_terminal_logs a SET 
				a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
				WHERE a.report_id='{$report_id}' LIMIT 1
			");
			$u_stmt->execute();

		} catch(PDOException $e) {}
			
	}
	
    /**
     * Add a new notification
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    private function notice_add(stdClass $params) {

        // predefine some variables
        $params->_item_id = $this->random_string(32);
        $params->notice_type = isset($params->notice_type) ? $params->notice_type : 3;
        $params->initiated_by = isset($params->initiated_by) ? $params->initiated_by : "user";
        
        try {
            // insert the record
            $stmt = $this->db->prepare("
                INSERT users_notification SET date_created=now()
                ".(isset($params->_item_id) ? ", item_id='{$params->_item_id}'" : null)."
                ".(isset($params->user_id) ? ", user_id='{$params->user_id}'" : null)."
                ".(isset($params->subject) ? ", subject='{$params->subject}'" : null)."
                ".(isset($params->message) ? ", message='{$params->message}'" : null)."
                ".(isset($params->initiated_by) ? ", initiated_by='{$params->initiated_by}'" : null)."
                ".(isset($params->notice_type) ? ", notice_type='{$params->notice_type}'" : null)."
                ".(isset($params->userId) ? ", created_by='{$params->userId}'" : null)."
            ");
            $stmt->execute();

            print "Notification was successfully record.\n";

        } catch(PDOException $e) {
            print $e->getMessage();
        }

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
					$response = $this->send_emails($value->recipient_list, $value->subject, $value->message);
					
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
				$response = $this->send_emails($user, "Student Bill", $result->bill);

		    	// update the status
		    	if($response) {
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

	/**
	 * Update students class ids
	 * 
	 * @param string		$client_id
	 * 
	 * @return bool
	 */
	public function update_class_ids($client_id = null) {

		try {

			print "Execute query to update the class ids of all students\n";
			$whereClause = !empty($client_id) ? "WHERE client_id = '{$client_id}'" : null;

			$stmt = $this->db->prepare("SELECT a.id AS student_id, a.firstname, b.id AS class_id FROM users a INNER JOIN classes b ON b.class_code = a.class_id {$whereClause}");
			$stmt->execute();

			$list = $this->db->prepare("select firstname, id, unique_id from users where client_id='MSGH00001'");
			$list->execute();

			while($result = $list->fetch(PDO::FETCH_OBJ)) {
				$first2 = substr($result->unique_id, 0, 2);
				print "The first2 for {$result->firstname} is: {$first2}\n";
			}

			return;


			print "Students have successfully been loaded.\n";

			// loop through the result
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				print "Updating the record of {$result->firstname} with student id: {$result->student_id}\n";
				$this->db->query("UPDATE users SET class_id = '{$result->class_id}' WHERE id='{$result->student_id}' LIMIT 1");
			}

		} catch(\Exception $e) {
			print $e->getMessage() . "\n";
		}

	}

}

// create new object
$jobs = new Crons;
// $jobs->load_emails();
// $jobs->scheduler();
// $jobs->send_smsemail();
$jobs->update_class_ids();
?>