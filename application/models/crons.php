<?php
 
class Crons {

	public $db;
	public $userAccount;
	public $mailAttachment = array();
	public $rootUrl;
	public $siteName = "Followin - Analitica Innovare";

	public function __construct() {
		$this->baseUrl = "https://insurehub365.com/";
		$this->rootUrl = "/home/www/dev.insurehub365.com/";
		// $this->rootUrl = "C:\\xampp\\htdocs\\analitica_innovare\\medics\\";
		$this->dbConn();

		require $this->rootUrl."system/libraries/phpmailer.php";
		require $this->rootUrl."system/libraries/smtp.php";

		// require $this->rootUrl."system\\libraries\\phpmailer.php";
		// require $this->rootUrl."system\\libraries\\smtp.php";
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
			'database' => "insured_medics",
			'username' => "insurehub365_imp",
			'password' => 'p3W0U1Hkee'
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
				$this->siteName = "Insurehub365";

				// commence the processing
				if(in_array($result->template_type, array("login", "general", "recovery"))) {
					$subject = $result->subject;
					$dataToUse = $this->generateGeneralMessage($result->message, $subject, $result->template_type);
				}

				// use the content submitted
				if(!empty($dataToUse)) {

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
			'headers' => "From: {$this->siteName} - Insurehub365<no-reply@insurehub365.com> \r\n Content-type: text/html; charset=utf-8",
			'Smtp' => true,
			'SmtpHost' => 'mail.supremecluster.com',
			'SmtpPort' => '465',
			'SmtpUser' => 'no-reply@insurehub365.com',
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
				$filepath = $this->rootUrl.str_replace("/", "\\", $theAttachment["path"]); // $theAttachment["path"];
				// append the attachment to the mail
				$mail->AddAttachment($filepath, $theAttachment["name"]);
			}
		}
		// set the port to sent the mail
		$mail->Port = $config->SmtpPort;

		// set the user from which the email is been sent
		$mail->setFrom('no-reply@insurehub365.com', $this->siteName .' - Insurehub365');

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
			$stmt = $this->db->prepare("SELECT * FROM cron_scheduler WHERE status = ? AND CURRENT_TIME() > TIMESTAMP(active_date) ORDER BY id ASC LIMIT 1");
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
				if($result->cron_type == "email") {
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

				// update the cron status
				$this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");

			}

		} catch(PDOException $e) {}

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
$jobs->loadEmailRequests();
$jobs->inApp_Emails();
$jobs->scheduler();
?>