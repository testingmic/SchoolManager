<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Crons {

	private $mailer;
	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $ini_data;
	private $limit = 2000;
	private $siteName = "MySchoolGH - EmmallexTech.Com";

	public function __construct() {
		// INI FILE
		$this->ini_data = parse_ini_file("db.ini");

		// set some more variables
		$this->baseUrl = $this->ini_data["base_url"];
		$this->rootUrl = $this->ini_data["root_url"];
		
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

		// additional settings
		$mailer->SMTPDebug = SMTP::DEBUG_SERVER;
		$mailer->isSMTP();
		$mailer->Host = $config->SmtpHost;
		$mailer->SMTPAuth = true;
		$mailer->Username = $config->SmtpUser;
		$mailer->Password = $config->SmtpPass;
		$mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

		// set the port to sent the mail
		$mailer->Port = $config->SmtpPort;

		// set the user from which the email is been sent
		$mailer->setFrom($this->ini_data["smtp_from"], $this->siteName);

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

		// loop through the list of recipients for this mail
        foreach($recipient_list as $emailRecipient) {
        	if(!empty($emailRecipient['email'])) {
				// user fullname
				$fullname = isset($emailRecipient['fullname']) ? $emailRecipient['fullname'] : $emailRecipient['name'];
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
					$fullname = isset($copiedRecipient['fullname']) ? $copiedRecipient['fullname'] : $copiedRecipient['name'];
					// append the email address
					$mailer->addCC($copiedRecipient['email'], $fullname);
				}
			}
		}

		// this is an html message
		$mailer->isHTML = true;

		// set the subject and message
		$mailer->Subject = $subject;
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
     * 
     * 
     * 
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
$jobs = new Crons;
$jobs->load_emails();
// $jobs->scheduler();
?>