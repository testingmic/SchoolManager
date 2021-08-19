<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $limit = 2000;
	private $siteName = "MySchoolGH - EmmallexTech.Com";

	public function __construct() {
		$this->baseUrl = "https://app.myschoolgh.com/";
		$this->rootUrl = "/home/mineconr/app.myschoolgh.com/";
		$this->dbConn();

		$this->rootUrl = "/var/www/html/myschool_gh/";

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
			'hostname' => "localhost",
			'database' => "mineconr_school",
			'username' => "mineconr_school",
			'password' => "YdwQLVx4vKU_"
		);

		$connectionArray = array(
			'hostname' => "localhost",
			'database' => "myschoolgh",
			'username' => "newuser",
			'password' => "password"
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
			'headers' => "From: {$this->siteName} - MySchoolGH.Com<app@myschoolgh.com> \r\n Content-type: text/html; charset=utf-8",
			'Smtp' => true,
			'SmtpHost' => 'mail.supremecluster.com',
			'SmtpPort' => '465',
			'SmtpUser' => 'app@myschoolgh.com',
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
		$mail->setFrom('app@myschoolgh.com', $this->siteName);

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
					$this->users_upload_modification($result->item_id, $result->client_id);
				}

				// if the type is to manage the terminal report functionality
				elseif($result->cron_type == "terminal_report") {
					$this->terminal_report_handler($result->item_id);
				}

				// if the type is to manage the end of term propagation
				elseif($result->cron_type == "end_academic_term") {
					$data_to_import = json_decode($result->query, true);
					$this->end_academic_term_handler($result->item_id, $data_to_import);
				}

				// update the cron status
				$this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");

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
	private function users_upload_modification($upload_id, $clientId) {
		// set the fullname of the user
		$u_stmt = $this->db->prepare("UPDATE users SET 
			name = CONCAT(firstname,' ', lastname,' ', othername), 
			client_id = UPPER(client_id) WHERE upload_id='{$upload_id}' AND client_id='{$clientId}'
		");
		$u_stmt->execute();

		// get the list of all users that was uploaded
		$u_list = $this->db->prepare("UPDATE users SET username = (SUBSTRING(email, 1, LOCATE('@', email) - 1))
			WHERE LENGTH(email) > 5 AND upload_id='{$upload_id}' AND client_id='{$clientId}'
		");
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
			a.student_item_id = (SELECT u.item_id FROM users u WHERE u.academic_year = a.academic_year AND u.academic_term = a.academic_term AND u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
			a.student_name = (SELECT u.name FROM users u WHERE u.academic_year = a.academic_year AND u.academic_term = a.academic_term AND u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
			a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
		WHERE a.report_id='{$report_id}'");
		$u_stmt->execute();

		// get the list of all users that was uploaded
		$u_stmt = $this->db->prepare("UPDATE grading_terminal_logs a SET 
			a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
		WHERE a.report_id='{$report_id}' LIMIT 1");
		$u_stmt->execute();
		
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
	 * client_data
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	private function client_data($clientId = null) {

		try {

			$stmt = $this->db->prepare("SELECT a.*, 
				c.grading AS grading_system, c.structure AS grading_structure, 
				c.show_position, c.show_teacher_name, c.allow_submission
			FROM clients_accounts a 
				LEFT JOIN grading_system c ON c.client_id = a.client_id
			WHERE a.client_id = ? AND a.client_status = ? LIMIT 1");
			$stmt->execute([$clientId, 1]);
			
			$data = array();

			// loop through the list
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				// loop through the items and convert into an object
				foreach(["client_preferences", "grading_system", "grading_structure"] as $value) {
					$result->{$value} = json_decode($result->{$value});
				}

				// append to the data
				$data[] = $result;
			}

			return !(empty($data)) ? $data[0] : (object) array();
			
		} catch(PDOException $e) {
			return (object) array();
		}
	}

	/**
	 * Get the Student Fees
	 * 
	 * @param String $studentId 
	 * @param String $academic_year
	 * @param String $academic_term
	 * 
	 * @return Array
	 */
	private function student_fees($studentId, $academic_year, $academic_term) {

		try {

			$stmt = $this->db->prepare("SELECT a.*, 
					b.name AS category_name, b.amount AS category_default_amount
				FROM fees_payments a 
				LEFT JOIN fees_category b ON b.id = a.category_id
				WHERE a.student_id = ? AND a.status = ? AND a.academic_year = ? AND a.academic_term = ?
			");
			$stmt->execute([$studentId, 1, $academic_year, $academic_term]);
			$result = $stmt->fetchAll(PDO::FETCH_OBJ);

			$data = array();

			// loop through the results list
			foreach($result as $key => $value) {
				// append the list
				$data[$studentId][$value->category_id] = [
					"category_id" => $value->category_id,
					"amount" => [
						"due" => $value->amount_due,
						"paid" => $value->amount_paid,
						"balance" => $value->balance
					]
				];
			}

			// set the data
			$response = $data;

			return $response;

		} catch(PDOException $e) {}
	}

	/**
	 * Close Academic Term
	 * 
	 * Process the Populating of Data for a new Term
	 * 
	 * @param String $recordId
	 * @param Array $data_to_import
	 * 
	 * @return Bool
	 */
	private function end_academic_term_handler($recordId, $data_to_import) {

		// split the record id for the client id and the record id
		$clientId = explode("_", $recordId)[1];

		// load client data
		$client_data = $this->client_data($clientId);
		$original_client = $client_data;

		try {

		$this->db->beginTransaction();

		// confirm that the status is propagation of record
		if($client_data->client_state === "Propagation") {

			// academics information
			$preferences = $client_data->client_preferences;
			$academics = $preferences->academics;
			
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
					a.academic_year = ? AND a.academic_term = ? AND a.user_type = ? AND a.user_status = ? AND a.client_id = ?
				LIMIT {$this->limit}"
			);
			$list_users->execute([$academic_year, $academic_term, "student", "Active", $clientId]);
			$students_list = $list_users->fetchAll(PDO::FETCH_ASSOC);

			// variables
			$students_query_string = "";
			$students_query_array = array();

			// loop through the students list
			foreach($students_list as $ikey => $student) {
				
				// get the keys
				$columns = array_keys($student);

				// format the student promotion information
				$is_promoted = !empty($student["is_promoted"]) ? explode("_", $student["is_promoted"]) : null;
				
				// append new variables
				$student["class_id"] = (!empty($is_promoted) && ($is_promoted[0] == 1)) ? $is_promoted[2] : $student["class_id"];
				$student["academic_year"] = $next_academic_year;
				$student["academic_term"] = $next_academic_term;
				$student["date_created"] = date("Y-m-d H:i:s");
				$student["last_login"] = date("Y-m-d H:i:s");
				$student["verified_date"] = !empty($student["verified_date"]) ? $student["verified_date"] : date("Y-m-d H:i:s");
				$student["last_visited_page"] = "{{APPURL}}dashboard";

				// get the new values
				$values = array_values($student);
				$last_key = count($values)-1;

				// get the finances owed by the user
				$student_fees = $this->student_fees($student["item_id"], $academic_year, $academic_term);

				// begin the student insert string
				$query_string = "INSERT INTO users SET ";
				
				// loop through the columns
				foreach($columns as $key => $column) {
					// exempt some data from the query
					if(!in_array($key, [0, $last_key])) {
						$query_string .= "{$column}='{$values[$key]}',";
					}
				}
				$students_query_string .= trim($query_string, ",").";";

				$students_query_array[] = $student_fees;

			}

			// initial 
			$school_fees = array();

			// algorithm to calculate how much money the school should have received 
			foreach($students_query_array as $key => $value) {
				foreach($value as $ikey => $ivalue) {
					foreach($ivalue as $ivkey => $ivvalue) {
						$school_fees[$ivvalue["category_id"]]["due"] = isset($school_fees[$ivvalue["category_id"]]["due"]) ? ($school_fees[$ivvalue["category_id"]]["due"] + $ivvalue["amount"]["due"]) : $ivvalue["amount"]["due"];
						$school_fees[$ivvalue["category_id"]]["paid"] = isset($school_fees[$ivvalue["category_id"]]["paid"]) ? ($school_fees[$ivvalue["category_id"]]["paid"] + $ivvalue["amount"]["paid"]) : $ivvalue["amount"]["paid"];
						$school_fees[$ivvalue["category_id"]]["balance"] = isset($school_fees[$ivvalue["category_id"]]["balance"]) ? ($school_fees[$ivvalue["category_id"]]["balance"] + $ivvalue["amount"]["balance"]) : $ivvalue["amount"]["balance"];
					}
				}
			}

			// total balance
			$total_due = array_sum(array_column($school_fees, "due"));
			$total_paid = array_sum(array_column($school_fees, "paid"));
			$total_balance = array_sum(array_column($school_fees, "balance"));

			$school_fees_summary = [
				"total_due" => $total_due,
				"total_paid" => $total_paid,
				"total_balance" => $total_balance
			];

			// school fees log information
			$school_fees_log = [
				"fees_log" => $school_fees,
				"summary" => $school_fees_summary
			];

			// get only fees in the array list that the student has a balance
			$student_ownings = array();

			// algorithm to calculate how much money the school should have received 
			foreach($students_query_array as $key => $value) {
				foreach($value as $ikey => $ivalue) {
					foreach($ivalue as $ivkey => $ivvalue) {
						if(round($ivvalue["amount"]["balance"]) > 0) {
							$student_ownings[$ikey][$ivvalue["category_id"]] = $ivvalue["amount"]["balance"];
						}
					}
				}
			}

			// get fees category
			$fees_category = $this->db->prepare("SELECT * FROM fees_category WHERE client_id = ? AND status = ? LIMIT 30");
			$fees_category->execute([$clientId, 1]);
			$fees_category_log = $fees_category->fetchAll(PDO::FETCH_OBJ);

			// EXECUTE THE STUDENTS LIST
			if(in_array("students", $data_to_import)) {
				$this->db->query($students_query_string);
			}

			// UPDATE THE STUDENTS FEES DATA FOR THE TERM
			$update_query = $this->db->prepare("UPDATE clients_terminal_log SET fees_log = ?, arrears_log = ?, fees_category_log = ? WHERE student_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1");
			$insert_query = $this->db->prepare("INSERT INTO clients_terminal_log SET client_id = ?, fees_log = ?, arrears_log = ?, fees_category_log = ?, student_id = ?, academic_year = ?, academic_term = ?");
			
			// Loop through the Students Fees Log List
			foreach($students_query_array as $key => $value) {
				// loop through the students fees payments list
				foreach($value as $ikey => $ivalue) {
					// confirm that the owings already exists or not
					$owing = isset($student_ownings[$ikey]) ? $student_ownings[$ikey] : (object) array();
					// confirm that the record already exists or not
					if($this->fees_history_log_exist($ikey, $academic_year, $academic_term)) {
						$update_query->execute([json_encode($value), json_encode($owing), json_encode($fees_category_log), $ikey, $academic_year, $academic_term]);
					} else {
						$insert_query->execute([$clientId, json_encode($value), json_encode($owing), json_encode($fees_category_log), $ikey, $academic_year, $academic_term]);
					}
				}
			}

			// new query string for a school
			$update_query = $this->db->prepare("UPDATE clients_terminal_log SET fees_log = ?, fees_category_log = ? WHERE client_id = ? AND academic_year = ? AND academic_term = ? AND log_type = ? LIMIT 1");
			$insert_query = $this->db->prepare("INSERT INTO clients_terminal_log SET client_id = ?, fees_log = ?, fees_category_log = ?, academic_year = ?, academic_term = ?, log_type = ?");

			// confirm if the school fees log already exists
			if($this->fees_history_log_exist($clientId, $academic_year, $academic_term, "school")) {
				$update_query->execute([json_encode($school_fees_log), json_encode($fees_category_log), $clientId, $academic_year, $academic_term, "school"]);
			} else {
				$insert_query->execute([$clientId, json_encode($school_fees_log), json_encode($fees_category_log), $academic_year, $academic_term, "school"]);
			}

			// set the new term in the clients data table
			$preferences->academics->academic_year = $next_academic_year;
			$preferences->academics->academic_term = $next_academic_term;
			$preferences->academics->term_starts = $preferences->academics->next_term_starts;
			$preferences->academics->term_ends = $preferences->academics->next_term_ends;

			// unset the next academic term and year
			$preferences->academics->next_academic_year = "";
			$preferences->academics->next_academic_term = "";
			$preferences->academics->next_term_starts = "";
			$preferences->academics->next_term_ends = "";

			// IMPORT THE COURSES LIST
			if(in_array("courses", $data_to_import)) {

				// GET THE ACTUAL COURSES LIST
				$list_courses = $this->db->prepare("SELECT a.*
					FROM 
						courses a 
					WHERE 
						a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
					LIMIT {$this->limit}"
				);
				$list_courses->execute([$academic_year, $academic_term, $clientId, 1]);
				$courses_list = $list_courses->fetchAll(PDO::FETCH_ASSOC);

				// variables
				$courses_query_string = "";
				$courses_plan_query_string = "";
				$courses_resource_query_string = "";

				// loop through the courses list
				foreach($courses_list as $ikey => $course) {
					
					// get the keys
					$columns = array_keys($course);
					
					// append new variables
					$course["academic_year"] = $next_academic_year;
					$course["academic_term"] = $next_academic_term;
					$course["date_created"] = date("Y-m-d H:i:s");
					$course["date_updated"] = date("Y-m-d H:i:s");

					// get the new values
					$values = array_values($course);
					$last_key = count($values)-1;

					// begin the student insert string
					$query_string = "INSERT INTO courses SET ";
					
					// loop through the columns
					foreach($columns as $key => $column) {
						// exempt some data from the query
						if(!in_array($key, [0, $last_key])) {
							$query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
						}
					}
					$courses_query_string .= trim($query_string, ",").";";
				}
				
				// check for empty string
				if(strlen($courses_query_string) > 20) {
					$this->db->query($courses_query_string);
				}
			}
			
			// IMPORT THE COURSES LIST
			if(in_array("courses_plan", $data_to_import)) {

				// GET THE LIST OF COURSE PLAN
				$course_plan = $this->db->prepare("SELECT a.*
					FROM 
						courses_plan a 
					WHERE 
						a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND status = ?
					LIMIT {$this->limit}"
				);
				$course_plan->execute([$academic_year, $academic_term, $clientId, 1]);
				$course_plan_list = $course_plan->fetchAll(PDO::FETCH_ASSOC);

				// loop through the course plans list
				foreach($course_plan_list as $ikey => $course_plan_item) {
					
					// get the keys
					$columns = array_keys($course_plan_item);
					
					// append new variables
					$course_plan_item["start_date"] = NULL;
					$course_plan_item["end_date"] = NULL;
					$course_plan_item["academic_year"] = $next_academic_year;
					$course_plan_item["academic_term"] = $next_academic_term;
					$course_plan_item["date_created"] = date("Y-m-d");
					$course_plan_item["date_updated"] = date("Y-m-d H:i:s");

					// get the new values
					$values = array_values($course_plan_item);
					$last_key = count($values)-1;

					// begin the student insert string
					$query_string = "INSERT INTO courses_plan SET ";
					
					// loop through the columns
					foreach($columns as $key => $column) {
						// exempt some data from the query
						if(!in_array($key, [0, $last_key])) {
							$query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
						}
					}
					$courses_plan_query_string .= trim($query_string, ",").";";
				}
				
				// run the query for the course plan
				if(strlen($courses_plan_query_string) > 20) {
					$this->db->query($courses_plan_query_string);
				}

			}

			// IMPORT THE COURSES LIST
			if(in_array("courses_resource", $data_to_import)) {

				// LOAD THE COURSE RESOURCES LIST
				$list_course_resources = $this->db->prepare("SELECT a.*
					FROM 
						courses_resource_links a 
					WHERE 
						a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
					LIMIT {$this->limit}"
				);
				$list_course_resources->execute([$academic_year, $academic_term, $clientId, 1]);
				$course_resources_list = $list_course_resources->fetchAll(PDO::FETCH_ASSOC);

				// loop through the course resources list
				foreach($course_resources_list as $ikey => $course_resource) {
					
					// get the keys
					$columns = array_keys($course_resource);
					
					// append new variables
					$course_resource["academic_year"] = $next_academic_year;
					$course_resource["academic_term"] = $next_academic_term;
					$course_resource["date_created"] = date("Y-m-d");

					// get the new values
					$values = array_values($course_resource);
					$last_key = count($values)-1;

					// begin the student insert string
					$query_string = "INSERT INTO courses_resource_links SET ";
					
					// loop through the columns
					foreach($columns as $key => $column) {
						// exempt some data from the query
						if(!in_array($key, [0, $last_key])) {
							$query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
						}
					}
					$courses_resource_query_string .= trim($query_string, ",").";";
				}

				// run the query for the course resources
				if(strlen($courses_resource_query_string) > 20) {
					$this->db->query($courses_resource_query_string);
				}

			}
	
			// IMPORT THE FEES ALLOCATIONS LIST
			if(in_array("fees_allocation", $data_to_import)) {

				// init variables
				$fees_allocation_query_string = "";
				$student_fees_allocation_query_string = "";

				// LOAD THE COURSE RESOURCES LIST
				$fees_allocation = $this->db->prepare("SELECT a.*
					FROM 
						fees_allocations a 
					WHERE 
						a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
					LIMIT {$this->limit}"
				);
				$fees_allocation->execute([$academic_year, $academic_term, $clientId, 1]);
				$fees_allocation_list = $fees_allocation->fetchAll(PDO::FETCH_ASSOC);

				// loop through the course resources list
				foreach($fees_allocation_list as $ikey => $allocation) {
					
					// get the keys
					$columns = array_keys($allocation);
					
					// append new variables
					$allocation["academic_year"] = $next_academic_year;
					$allocation["academic_term"] = $next_academic_term;
					$allocation["date_created"] = date("Y-m-d H:i:s");

					// get the new values
					$values = array_values($allocation);
					$last_key = count($values)-1;

					// begin the fees_allocations insert string
					$query_string = "INSERT INTO fees_allocations SET ";
					
					// loop through the columns
					foreach($columns as $key => $column) {
						// exempt some data from the query
						if(!in_array($key, [0, $last_key])) {
							$query_string .= "{$column}='{$values[$key]}',";
						}
					}
					$fees_allocation_query_string .= trim($query_string, ",").";";
				}

				// LOAD THE COURSE RESOURCES LIST
				$fees_allocation = $this->db->prepare("SELECT a.*
					FROM 
						fees_payments a 
					WHERE 
						a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
					LIMIT {$this->limit}"
				);
				$fees_allocation->execute([$academic_year, $academic_term, $clientId, 1]);
				$fees_allocation_list = $fees_allocation->fetchAll(PDO::FETCH_ASSOC);

				// loop through the course resources list
				foreach($fees_allocation_list as $ikey => $allocation) {
					
					// get the keys
					$columns = array_keys($allocation);
					
					// append new variables
					$allocation["academic_year"] = $next_academic_year;
					$allocation["academic_term"] = $next_academic_term;
					$allocation["paid_status"] = 0;
					$allocation["editable"] = 0;
					$allocation["amount_paid"] = 0.00;
					$allocation["last_payment_id"] = NULL;
					$allocation["last_payment_date"] = NULL;
					$allocation["balance"] = $allocation["amount_due"];
					$allocation["date_created"] = date("Y-m-d H:i:s");
					$allocation["checkout_url"] = $this->random_string(22);

					// get the new values
					$values = array_values($allocation);
					$last_key = count($values)-1;

					// begin the fees_allocations insert string
					$query_string = "INSERT INTO fees_payments SET ";
					
					// loop through the columns
					foreach($columns as $key => $column) {
						// exempt some data from the query
						if(!in_array($key, [0, $last_key])) {
							$query_string .= in_array($column, ["last_payment_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
						}
					}
					$student_fees_allocation_query_string .= trim($query_string, ",").";";
				}

				// check for empty string
				if(strlen($fees_allocation_query_string) > 20) {
					$this->db->query($fees_allocation_query_string);
				}
				if(strlen($student_fees_allocation_query_string) > 20) {
					$this->db->query($student_fees_allocation_query_string);
				}
				
			}

			// update the clients preferences
			$stmt = $this->db->prepare("UPDATE clients_accounts SET client_preferences = ?, client_state = ? WHERE client_id = ? LIMIT 1");
			$stmt->execute([json_encode($preferences), "Complete", $clientId]);

		}

		$this->db->commit();

		print "Processing of Academic Term Data was successful.\n";

		} catch(PDOException $e) {
			$this->db->rollBack();
			print "{$e->getMessage()}\n";
		}

	}

	/**
	 * Fees History Log Check
	 * 
	 * @param String $studentId
	 * @param String $academic_year
	 * @param String $academic_term
	 * @param String $type		default(student, school)
	 * 
	 * @return Bool
	 */
	private function fees_history_log_exist($studentId, $academic_year, $academic_term, $type = "student") {

		try {

			// set the field
			$field = ($type == "student") ? "student_id" : "client_id";

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT id FROM clients_terminal_log WHERE {$field} = ? AND academic_year = ? AND academic_term = ? AND log_type = ? LIMIT 1");
			$stmt->execute([$studentId, $academic_year, $academic_term, $type]);

			return $stmt->rowCount();

		} catch(PDOException $e) {
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
$jobs = new Crons;
// $jobs->loadEmailRequests();
// $jobs->inApp_Emails();
$jobs->scheduler();
?>