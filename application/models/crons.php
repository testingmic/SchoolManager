<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $limit = 100;
	private $siteName = "MySchoolGH - VisamiNetSolutions.Com";

	public function __construct() {
		$this->baseUrl = "https://app.myschoolgh.com/";
		$this->rootUrl = "/home/mineconr/app.myschoolgh.com/";
		$this->dbConn();

		// require $this->rootUrl."system/libraries/phpmailer.php";
		// require $this->rootUrl."system/libraries/smtp.php";
	}
	
	/**
	 * Run the connection to the database
	 * 
	 * @return $this
	 */
	public function dbConn() {
		
		// CONNECT TO THE DATABASE
		$connectionArray = array(
			'hostname' => "localhost",
			'database' => "mineconr_school",
			'username' => "mineconr_school",
			'password' => 'YdwQLVx4vKU_'
		);

		$connectionArray = array(
			'hostname' => "localhost",
			'database' => "myschoolgh",
			'username' => "root",
			'password' => ''
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
	private function generateGeneralMessage($message, $subject) {

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
	public function loadEmailRequests() {
		
		try {

			// run the query
			$stmt = $this->db->prepare("SELECT a.* FROM users_messaging_list a WHERE a.sent_status='0' AND a.deleted='0' LIMIT 200");
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
				$dataToUse = $this->generateGeneralMessage($result->message, $subject, $result->template_type);

				// use the content submitted
				if(!empty($dataToUse)) {
				    // print progress
				    print "Data found, processing the recipients list to send\n";

					// convert the recipient list to an array
					$recipient_list = json_decode($result->recipients_list, true);
					$recipient_list = $recipient_list["recipients_list"];
					
					// submit the data for processing
					$mailing = $this->cronSendMail($recipient_list, $subject, $dataToUse);

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
	 * This processes the in-app emails that have been scheduled
	 * Loop through the list and send out the emails
	 */
	public function inApp_Emails() {
		
		try {

			// run the query
			$stmt = $this->db->prepare("SELECT a.*,
				(SELECT b.description FROM files_attachment b WHERE b.record_id = a.thread_id ORDER BY b.id DESC LIMIT 1) AS attachment
				FROM users_emails a WHERE a.sent_status='0' AND (CURRENT_TIME() > TIMESTAMP(a.schedule_date)) AND a.status='1' LIMIT 10
			");
			$stmt->execute();

			// the data to use
			$dataToUse = null;

			// looping through the content
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				// empty the attachment 
				$this->mailAttachment = null;
				
				// set the store content
				$this->userAccount = $result;
				$this->siteName = "Insurehub365";

				$result->message = htmlspecialchars_decode($result->message);

				// commence the processing
				$subject = $result->subject;
				$dataToUse = $this->generateGeneralMessage($result->message, $subject);

				// use the content submitted
				if(!empty($dataToUse)) {

					// convert the recipient and copied list to an array
					$recipient_list = json_decode($result->recipient_details, true);
					$copied_list = json_decode($result->copy_recipients, true);
					$attachments = json_decode($result->attachment, true);

					// if the attachment is not empty then get the files list
					if(!empty($attachments)) {
						// gtet the files list
						$attachments = $attachments["files"];
						// assign the attachments to the list
						$this->mailAttachment = $attachments;
					}

					// submit the data for processing
					$mailing = $this->cronSendMail($recipient_list, $subject, $dataToUse, $copied_list);

					// set the mail status to true
					if(!empty($mailing)) {
						$this->db->query("UPDATE users_emails SET sent_status = '1', sent_date=now() WHERE id='{$result->id}' LIMIT 1");
						print "Mails successfully sent\n";
					} else {
						print "Sending email failed\n";
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
	private function cronSendMail($recipient_list, $subject, $message, $cc_list = null) {

		$mail = new Phpmailer();
		$smtp = new Smtp();

		// configuration settings
		$config = (Object) array(
			'subject' => $subject,
			'headers' => "From: {$this->siteName} - MySchoolGH.Com<app@school.mineconrsl.com> \r\n Content-type: text/html; charset=utf-8",
			'Smtp' => true,
			'SmtpHost' => 'mail.supremecluster.com',
			'SmtpPort' => '465',
			'SmtpUser' => 'app@school.mineconrsl.com',
			'SmtpPass' => 'C30C5aamUl',
			'SmtpSecure' => 'ssl'
		);

		// additional settings
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = $config->SmtpHost;
		$mail->SMTPAuth = true;
		$mail->Username = $config->SmtpUser;
		$mail->Password = $config->SmtpPass;
		$mail->SMTPSecure = $config->SmtpSecure;

		// attach documents if any was found
		if(!empty($this->mailAttachment)) {
			// loop through the attachments list
			foreach($this->mailAttachment as $theAttachment) {
				// file path
				$filepath = $theAttachment["path"];
				// append the attachment to the mail
				$mail->AddAttachment($filepath, $theAttachment["name"]);
			}
		}
		// set the port to sent the mail
		$mail->Port = $config->SmtpPort;

		// set the user from which the email is been sent
		$mail->setFrom('app@school.mineconrsl.com', $this->siteName);

		// loop through the list of recipients for this mail
        foreach($recipient_list as $emailRecipient) {
			// user fullname
			$fullname = isset($emailRecipient['fullname']) ? $emailRecipient['fullname'] : $emailRecipient['name'];
			// append the email address
			$mail->addAddress($emailRecipient['email'], $fullname);
		}

		// loop through the list of cc if not empty
		if(!empty($cc_list)) {
			// loop through the copied list
			foreach($cc_list as $copiedRecipient) {
				// user fullname
				$fullname = isset($copiedRecipient['fullname']) ? $copiedRecipient['fullname'] : $copiedRecipient['name'];
				// append the email address
				$mail->addCC($copiedRecipient['email'], $fullname);
			}
		}

		// this is an html message
		$mail->isHTML(true);

		// set the subject and message
		$mail->Subject = $subject;
		$mail->Body    = $message;
		
		// send the email message to the users
		if($mail->send()) {
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

				// if the request is notification
				if($result->cron_type == "notification") {
					// get the query to process
					$query = $result->query;
					// prepare and execute the statement
					$query_stmt = $this->db->prepare($query);
					$query_stmt->execute();	
					// form the notification parameters
					$notice_param = (object) [
						'_item_id' => $this->random_string(32),
						'subject' => $result->subject,
						'remote' => false, 
						'notice_type' => $result->notice_code,
						'userId' => $result->user_id,
						'initiated_by' => 'system'
					];
					// loop through each result and share the notice with them
					while($the_result = $query_stmt->fetch(PDO::FETCH_OBJ)) {
						$notice_param->user_id = $the_result->user_id;
						$notice_param->message = "<a title=\"Click to View\" class=\"preview-announcement\" data-announcement_id=\"{$result->item_id}\" href=\"{{APPURL}}announcements\">Hello {$the_result->name}, an announcement was posted for your review.</a>";
						$this->notice_add($notice_param);
					}
				}

				// if the request is email
				elseif($result->cron_type == "email") {
					// convert the list to an object
					$query = json_decode($result->query);

					// form the notification parameters
					$notice_param = (object) [
						'_item_id' => $this->random_string(32),
						'subject' => $result->subject,
						'remote' => false, 
						'notice_type' => $result->notice_code,
						'userId' => $result->user_id,
						'initiated_by' => 'system'
					];

					foreach($query as $eachUser) {
						$notice_param->user_id = $eachUser->user_id;
						$notice_param->message = "<a title=\"Click to View\" class=\"preview-email\" data-email_id=\"{$result->item_id}\" href=\"{{APPURL}}emails\">Hello {$eachUser->name}, you have been sent an email message.</a>";
						$this->notice_add($notice_param);
					}
					
				}

				// if a user information have been uploaded
				elseif($result->cron_type == "users_upload") {
					$this->users_upload_modification($result->item_id);
				}

				// if the type is to manage the terminal report functionality
				elseif($result->cron_type == "terminal_report") {
					$this->terminal_report_handler($result->item_id);
				}

				// if the type is to manage the end of term propagation
				elseif($result->cron_type == "end_academic_term") {
					$this->end_academic_term_handler($result->item_id);
				}

				// update the cron status
				// $this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");

			}

		} catch(PDOException $e) {
			print $e->getMessage();
		}

    }

	/**
	 * Perform some modification on the users list
	 * 
	 * @return Bool
	 */
	private function users_upload_modification($upload_id) {
		// set the fullname of the user
		$u_stmt = $this->db->prepare("UPDATE users SET name = CONCAT(firstname,' ', lastname,' ', othername), client_id = UPPER(client_id) WHERE upload_id='{$upload_id}'");
		$u_stmt->execute();

		// get the list of all users that was uploaded
		$u_list = $this->db->prepare("UPDATE users a SET a.username = (SELECT SUBSTRING(b.email, 1, LOCATE('@', b.email) - 1) AS the_username FROM users b WHERE b.id = a.id) WHERE LENGTH(a.email) > 5 AND a.upload_id='{$upload_id}'");
		$u_list->execute();
	}	

	/**
	 * Perform some modification on the users list
	 * 
	 * @return Bool
	 */
	private function terminal_report_handler($report_id) {
		
		// set the fullname of the user
		$u_stmt = $this->db->prepare("UPDATE grading_terminal_scores a SET 
			a.student_item_id = (SELECT u.item_id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
			a.student_name = (SELECT u.name FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
			a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
		WHERE a.report_id='{$report_id}'");
		$u_stmt->execute();

		// get the list of all users that was uploaded
		$u_stmt = $this->db->prepare("UPDATE grading_terminal_logs a SET 
			a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
		WHERE a.report_id='{$report_id}'");
		$u_stmt->execute();		
	}
	
    /**
     * Add a new notification
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function notice_add(stdClass $params) {

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
	 * client_data
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	public function client_data($clientId = null) {

		try {

			$stmt = $this->db->prepare("SELECT a.*, 
				c.grading AS grading_system, c.structure AS grading_structure, 
				c.show_position, c.show_teacher_name, c.allow_submission
			FROM clients_accounts a 
				LEFT JOIN grading_system c ON c.client_id = a.client_id
			WHERE a.client_id = ? AND a.client_status = ? LIMIT 1");
			$stmt->execute([$clientId, 1]);
			
			$data = [];

			// loop through the list
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				// loop through the items and convert into an object
				foreach(["client_preferences", "grading_system", "grading_structure"] as $value) {
					$result->{$value} = json_decode($result->{$value});
				}

				// append to the data
				$data[] = $result;
			}

			return !(empty($data)) ? $data[0] : (object) [];
			
		} catch(PDOException $e) {
			return (object) [];
		}
	}

	/**
	 * Close Academic Term
	 * 
	 * Process the Populating of Data for a new Term
	 * 
	 * @param String $recordId
	 * 
	 * @return Bool
	 */
	public function end_academic_term_handler($recordId) {

		// split the record id for the client id and the record id
		$clientId = explode("_", $recordId)[1];

		// load client data
		$client_data = $this->client_data($clientId);

		// academics information
		$academics = $client_data->client_preferences->academics;
		
		// set variables for the academic year and term
		$academic_year = $academics->academic_year;
		$academic_term = $academics->academic_term;
		$next_academic_year = $academics->next_academic_year;
		$next_academic_term = $academics->next_academic_term;

		/**
		 * STEP ONE:: 
		 * 
		 * GET THE LIST OF ALL STUDENTS FOR THE CURRENT ACADEMIC YEAR / TERM
		 */
		$list_users = $this->db->prepare("SELECT a.*,
				(
					SELECT 
						CONCAT (
							b.is_promoted,'_',b.promote_to,'_',
							(
								SELECT d.id FROM classes d WHERE d.item_id = b.promote_to LIMIT 1
							)
						)
					FROM 
						promotions_log b 
					LEFT JOIN promotions_history c ON c.history_log_id = b.history_log_id
					WHERE 
						b.academic_year = a.academic_year AND b.academic_term = a.academic_term AND 
						a.item_id = b.student_id AND c.status='Processed'
				) AS is_promoted
			FROM 
				users a 
			WHERE 
				a.academic_year = ? AND a.academic_term = ? AND a.user_type = ? AND a.user_status = ? 
			LIMIT {$this->limit}"
		);
		$list_users->execute([$academic_year, $academic_term, "student", "Active"]);
		$students_list = $list_users->fetchAll(PDO::FETCH_ASSOC);

		// variables
		$students_query_string = "";

		// loop through the students list
		foreach($students_list as $student) {
			
			// get the keys
			$columns = array_keys($student);
			$values = array_values($student);
			$last_key = count($values)-1;

			// format the student promotion information
			$is_promoted = explode("_", $student["is_promoted"]);
			
			// append new variables
			$student["class_id"] = ($is_promoted[0] == 1) ? $is_promoted[2] : $student["class_id"];
			$student["academic_year"] = $next_academic_year;
			$student["academic_term"] = $next_academic_term;
			$student["last_visited_page"] = "{{APPURL}}dashboard";
			
			// begin the student insert string
			$query_string = "INSERT INTO users SET ";
			
			// loop through the columns
			foreach($columns as $key => $column) {
				// exempt some data from the query
				if(!in_array($key, [0, $last_key])) {
					$query_string .= "{$column}='{$values[$key]}',";
				}
			}
			$students_query_string .= trim($query_string, ",").";\n";

		}

		print $students_query_string;

	}

    /**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	int	number of characters
	 * @return	string
	 */
	public function random_string($len = 8) {
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}

}

// create new object
$jobs = new Crons;
// $jobs->loadEmailRequests();
// $jobs->inApp_Emails();
$jobs->scheduler();
?>