<?php
 
class SMS_CronJOB {

	private $db;
	private $siteName = "MySchoolGH - EmmallexTecnologies.Com";
	private $mnotify_key = "3LhA1Cedn4f2qzkTPO3cIkRz8pv0inBl9TWavaoTeEVFe";

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
	private function dbConn() {
		
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
     * Send SMS Messages
     * 
     * @return String
     */
    public function send_smsemail() {

        $stmt = $this->db->prepare("SELECT a.*, c.client_name, c.sms_sender
			FROM smsemail_send_list a
			LEFT JOIN clients_accounts c ON c.client_id = a.client_id
			WHERE a.sent_status='Pending' LIMIT 5
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
            if(($value->type === "sms") && (time() > strtotime($value->schedule_time))) {

				// get the response of the request
				$response = $this->mnotify_send($key, $value->message, $value->recipient_list, $value->sms_sender);
				
				// save the  reponse
				if(!empty($response)) {
					$this->db->query("UPDATE smsemail_send_list SET sent_status='Delivered' WHERE sent_time=now() AND item_id='{$key}' LIMIT 1");
				}
			}
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
	 * @return Bool
	 */
	public function mnotify_send($item_id, $message, $recipients, $sender) {

		if(empty($recipients) || !is_array($recipients)) {
			return false;
		}
		
		// get the list of all recipients
		$recipients_ids = array_column($recipients, "phone_number");
		$recipients_join = implode(",", $recipients_ids);

		//open connection
        $ch = curl_init();

		// set the field parameters
        $fields_string = [
            "key" => $this->mnotify_key,
			"recipient" => $recipients_ids,
			"sender" => $sender,
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

}

// create new object
$jobs = new SMS_CronJOB;
$jobs->send_smsemail();
?>