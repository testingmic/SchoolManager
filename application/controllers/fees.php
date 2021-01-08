<?php
// ensure this file is being included by a parent file
if( !defined( 'SITE_URL' ) && !defined( 'SITE_DATE_FORMAT' ) ) die( 'Restricted access' );

/**
 * Fees class extends Myschoolgh Model
 *
 * Loads the base classes and executes the request.
 *
 * @package		MySchoolGH
 * @subpackage	Students super class
 * @category	Fees Controller
 * @author		Emmallen Networks
 * @link		https://www.myschoolgh.com/
 */
class Fees extends Myschoolgh {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * @method feesCollection
	 * @param array $stdClass
	 * @param array $stdClass->dataColumns 
	 * @param array $stdClass->whereClause
	 *
	 * @return queryResults 
	 **/
	public function list(stdClass $params) {

        $params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;

        /** Init the user type */
        $student_id = $params->userData->user_id;

        /** The user id algorithm */
        if(in_array($params->userData->user_type, ["accountant", "admin"])) {
            $student_id = "";
        } else if(in_array($params->userData->user_type, ["parent"])) {
            // if the user is a parent
			$student_id = $this->session->student_id;
        }

        $filters = "";
		$filters .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : "";
        $filters .= isset($params->department_id) && !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : "";
        $filters .= !empty($student_id) ? " AND a.student_id IN ('{$student_id}')" : "";
        $filters .= isset($params->item_id) ? " AND a.item_id='{$params->item_id}'" : "";
        $filters .= isset($params->programme_id) && !empty($params->programme_id) ? " AND a.programme_id='{$params->programme_id}'" : "";
        $filters .= isset($params->category_id) && !empty($params->category_id) ? " AND a.category_id='{$params->category_id}'" : ""; 
        $filters .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $filters .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";
        $filters .= isset($params->date) ? " AND DATE(a.recorded_date='{$params->date}')" : "";

		try {

			$stmt = $this->db->prepare("
				SELECT a.*,
                    (SELECT b.name FROM departments b WHERE b.id = a.department_id LIMIT 1) AS department_name,
                    (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_info
                FROM fees_collection a
				WHERE a.client_id = ? {$filters} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
			$stmt->execute([$params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // loop through the information
                foreach(["student_info", "created_by_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["unique_id", "user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }
                }
                $data[] = $result;
            }

			return [
                "code" => 200,
                "data" => $data
            ];

		} catch(PDOException $e) {
			return [];
		}
	}
    
    /**
     * List Category List
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function category_list(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->code)) ? " AND a.code='{$params->code}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id)) ? " AND a.id='{$params->category_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, 
                    (SELECT COUNT(*) FROM fees_allocations b WHERE a.id = b.category_id AND b.client_id = a.client_id) AS fees_count
                FROM fees_category a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }
    
    /**
     * List Class Fees Allocation
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function class_fees_allocation(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id)) ? " AND a.id='{$params->category_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name
                FROM fees_allocations a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Load the Fees Payment Form
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function payment_form(stdClass $params) {

        $html_form = "<div class='table-responsive'>";
        $html_form .= "<table class='table table-bordered'>";
        $html_form .= "<tr>";
        $html_form .= "<td width='40%'>Amount Due:</td>";
        $html_form .= "<td></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Amount Paid:</td>";
        $html_form .= "<td></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Outstanding Balance:</td>";
        $html_form .= "<td></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Last Payment Info:</td>";
        $html_form .= "<td></td>";
        $html_form .= "</tr>";
        $html_form .= "</table>";
        $html_form .= "</div>";

        return [
            "data" => $html_form
        ];

    }

	/**
	 * @method feesPayment
	 * @param array $params
	 * @param array $params->dataColumns 
	 * @param array $params->whereClause
	 *
	 * @return queryResults 
	 **/
	public function feesPayment(array $params) {

		$params = (Object) $params;

		try {

			$stmt = $this->db->prepare("
				SELECT {$params->dataColumns} 
				FROM _fees_payments ".(isset($params->tableJoins) ? $params->tableJoins : null)."
				WHERE {$params->whereClause}
			");


			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ);

		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

    /**
     * Allocate Fees to Class/Student
     * 
     * @param stdClass $params
     *  
     * @return Array
     */
    public function allocate_fees(stdClass $params) {

        /** An annonymous function to insert the student fees record */
        function insert_student_fees(stdClass $params) {
            
            // global variable
            global $myschoolgh;

            /** Update the existing record */
            $stmt = $myschoolgh->prepare("INSERT INTO fees_payments SET 
                amount_due = ?, balance = ?, category_id = ?, student_id = ?, 
                client_id = ?, academic_year = ?, academic_term = ?, class_id = ?, created_by = ?
            ");
            /** Execute the prepared statement */
            return $stmt->execute([
                $params->amount, $params->amount, $params->category_id, $params->student_id, $params->clientId, 
                $params->academic_year, $params->academic_term, $params->class_id, $params->userId
            ]);
        }

        /** Check if the student id is valid */
        $class_check = $this->pushQuery("id", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($class_check)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        /** Confirm if the allocate to is student */
        if($params->allocate_to === "student") {

            /** Return false if the student id was not parsed */
            if(empty($params->student_id)) {
                return ["code" => 203, "data" => "Sorry! The student id cannot be left empty"];
            }

            /** Check if the student id is valid */
            $student_check = $this->pushQuery("id", "users", "item_id='{$params->student_id}' AND client_id='{$params->clientId}' AND status='1' AND deleted='0' LIMIT 1");
            if(empty($student_check)) {
                return ["code" => 203, "data" => "Sorry! An invalid student id was supplied."];
            }

            /** Confirm if a record already exist */
            if($this->confirm_student_payment_record($params)) {
                /** Update the existing record */
                $stmt = $this->db->prepare("UPDATE fees_payments SET 
                        amount_due = ?, balance = ($params->amount - amount_paid)
                    WHERE category_id = ? AND student_id = ? AND client_id = ? 
                        AND academic_year = ? AND academic_term = ?");

                /** Execute the prepared statement */
                $stmt->execute([
                    $params->amount, $params->category_id, $params->student_id, $params->clientId, 
                    $params->academic_year, $params->academic_term
                ]);

            } else {
                /** Insert a new record */
                $pay = insert_student_fees($params);
            }
        } elseif($params->allocate_to === "class") {
            /** Confirm if a record already exist */
            if($this->confirm_class_payment_record($params)) {
                
            }
        }
    }

	/**
	 * @method confirm_class_payment_record
	 * @param String $params->student_id 	    This is the unique id of the student
	 * @param String $params->category_id		This is the fees type (tuition, ict, pta, or any other)
	 * @param String $params->academic_year	    This specifies the academic year to fetch the record
	 * @param String $params->academic_term 	This specifies the academic term that the record is been fetched
	 * @return Bool
	 **/
	public function confirm_class_payment_record(stdClass $params) {
		try {
			$stmt = $this->db->prepare("
				SELECT amount 
				FROM fees_allocations 
				WHERE client_id = ? AND class_id = ? AND category_id = ? AND academic_year = ? AND academic_term = ? 
			");
			$stmt->execute([$params->clientId, $params->class_id, $params->category_id, $params->academic_year, $params->academic_term]);
			return ($stmt->rowCount() > 0) ? true : false;
		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * @method confirm_student_payment_record
	 * @param String $params->student_id 	    This is the unique id of the student
	 * @param String $params->category_id		This is the fees type (tuition, ict, pta, or any other)
	 * @param String $params->academic_year	    This specifies the academic year to fetch the record
	 * @param String $params->academic_term 	This specifies the academic term that the record is been fetched
	 * @return Bool
	 **/
	public function confirm_student_payment_record(stdClass $params) {
		try {
			$stmt = $this->db->prepare("
				SELECT amount_due 
				FROM fees_payments 
				WHERE client_id = ? AND student_id = ? AND category_id = ? AND academic_year = ? AND academic_term = ? 
			");
			$stmt->execute([$params->clientId, $params->student_id, $params->category_id, $params->academic_year, $params->academic_term]);
			return ($stmt->rowCount() > 0) ? true : false;
		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * @method studentFeesPaymentsHistory
	 * @param string $studentId 		This is the student id that the filtering will be based on
	 * @return array of the record list
	 **/
	public function studentFeesPaymentsHistory($studentId) {
		
		try {
			/* Fetch the student payment history */
			$history = [];

			/* Make the query */
			$stmt = $this->db->prepare("
				SELECT 
				fc.*, stu.fullname, pg.programme_name AS programme_name, 
				cl.name AS class_name, ft.fees_category AS fees_category,
				stu.phone, stu.email, stu.user_image,
				(SELECT fullname FROM _users WHERE unique_id = fc.recorded_by) AS recorder_name
				FROM _fees_collection fc
				LEFT JOIN _fees_category ft ON ft.id = fc.fees_category 
				LEFT JOIN _users stu ON stu.unique_id = fc.student_id
				LEFT JOIN _programmes pg ON pg.id = stu.programme
				LEFT JOIN _classes cl ON cl.id = stu.class_id
				WHERE fc.school_id = ? AND fc.status = ? AND fc.student_id = ? ORDER BY fc.id DESC
				");
			$stmt->execute([$this->session->school_id, 1, $studentId]);

			/* Fetch the results and return it in an array object */
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$history[] = $result;
			}

			return $history;

		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @method studentsFeesAllocation
	 * @param string $studentId 		Assign fees to a specific students 
	 * @return array of the record list
	 **/
	public function studentsFeesAllocation() {

		$stmt = $this->db->prepare("
			SELECT 
			fa.*, stu.fullname, pg.programme_name AS programme_name, stu.class_id,
			cl.name AS class_name, ft.fees_category AS fees_category, stu.programme
			FROM _fees_payments fa
			LEFT JOIN _users stu ON stu.unique_id = fa.student_id
			LEFT JOIN _programmes pg ON pg.id = stu.programme
			LEFT JOIN _classes cl ON cl.id = stu.class_id 
			LEFT JOIN _fees_category ft ON ft.id = fa.fees_category 
			WHERE fa.school_id = ?
			");
		$stmt->execute([$this->session->school_id]);

		/* Initializing */
		$responseData = [];

		// initializing
		if($stmt->rowCount() > 0) {
			$i = 0;
			// using while loop
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$i++;
				//print the results
				$responseData[] = array(
					1 => $i,
					2 => "<a href='javascript:void(0);' data-request='student' class='quick-view' data-id='{$result->student_id}'>{$result->fullname}</a> <br>{$result->programme_name} <br> {$result->class_name}",
					3 => "{$result->fees_category}",
					4 => "{$result->amount_due}",
					5 => "{$result->amount_paid}",
					6 => (($result->paid_status == 1) ? '<span class="badge badge-pill badge-success d-block mg-t-8">PAID</span>' : '<span class="badge badge-pill badge-danger d-block mg-t-8">Unpaid</span>'),
					7 => ((isEqual('FINANCE') or isEqual('DEVELOPER')) ? "
						<div class=\"dropdown\">
							<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"
							aria-expanded=\"false\">
								<span class=\"flaticon-more-button-of-three-dots\"></span>
							</a>
							<div class=\"dropdown-menu dropdown-menu-right\">
								".(($result->paid_status != 1) ? "<a class=\"dropdown-item\" data-value=\"{$result->id}\" href=\"{$this->config->base_url("fees/fees-collection/{$result->fees_category}_{$result->programme}_{$result->class_id}_{$result->student_id}")}\"><i class=\"fa fa-money-check text-success\"></i> Receive</a>" : null)."
								<a data-mode=\"student-fees-allocation\" class=\"dropdown-item delete-item\" data-value=\"{$result->id}\" href=\"javascript:void(0)\"><i class=\"fas fa-times text-orange-red\"></i> Delete</a>
							</div>
						</div>" : null)
				);
			}
		}

		return $responseData;
	}

	public function processFeesCollection($studentId, $programmeId, $classId, $feesType, $amount, $description) {

		$status = 500;
		$uniqueId = null;

		$paymentRecord = $this->feesPayment(
			array(
				"dataColumns" => "amount_due, amount_paid, balance",
				"whereClause" => "school_id = '{$this->session->school_id}' AND academic_term = '{$this->session->academicTerm}' 
				AND academic_year = '{$this->session->academicYear}'
				AND student_id = '{$studentId}' AND fees_category = '{$feesType}'"
			)
		);

		/* Confirm that there was a record found for the query */
		if(isset($paymentRecord->amount_due)) {

			/* Outstanding balance calculator */
			$outstandingBalance = $paymentRecord->balance - $amount;
			$totalPayment = $paymentRecord->amount_paid + $amount;

			/* Verify if the student has made full payment */
			if($totalPayment >= $paymentRecord->amount_due) {
				$paidStatus = 1;
			} else {
				$paidStatus = 0;
			}

			/* Confirm if the user has any credits */
			if($outstandingBalance < 0) {
				$creditBalance = $outstandingBalance * -1;
				$outstandingBalance = 0;
			}

			$uniqueId = random_string('alnum', 14);

			/* Record the payment made by the user */
			$stmt = $this->db->prepare("
				INSERT INTO _fees_collection 
				SET school_id = ?, unique_id = ?, student_id = ?, programme_id = ?, class_id = ?, fees_category = ?, amount = ?,
				recorded_by = ?, academic_year = ?, academic_term = ?, description = ?
			");
			$stmt->execute([
				$this->session->school_id, $uniqueId, $studentId, $programmeId, $classId, $feesType, $amount,
				$this->session->userId, $this->session->academicYear, $this->session->academicTerm, $description
			]);

			/* Update the user payment record */
			$stmt = $this->db->prepare("
				UPDATE _fees_payments
				SET amount_paid = ?, balance = ?
				WHERE school_id = ? AND student_id = ? AND fees_category = ? AND academic_year = ? AND academic_term = ?
			");
			$stmt->execute([
				$totalPayment, $outstandingBalance, $this->session->school_id, 
				$studentId, $feesType, $this->session->academicYear, $this->session->academicTerm
			]);

			/* Update the student credit balance */
			if(isset($creditBalance)) {
				// fetch the amount that the user already have
				$recordedBalance = $this->itemById('_users', 'unique_id', $studentId, 'account_balance');
				$creditBalance = $recordedBalance + $creditBalance;

				// update the user data
				$stmt = $this->db->prepare("
					UPDATE _users SET account_balance = ? WHERE unique_id = ?
				");
				$stmt->execute([
					$creditBalance, $studentId
				]);
			}

			/* Update the paid status for the student if the outstanding balance is equal to zero (0) */
			if($outstandingBalance == 0) {
				$stmt = $this->db->prepare("
					UPDATE _fees_payments
					SET paid_status = ?
					WHERE student_id = ? AND fees_category = ? AND academic_year = ? AND academic_term = ?
				");
				$stmt->execute([
					1, $studentId, $feesType, $this->session->academicYear, $this->session->academicTerm
				]);
			}

			/* Record the user activity log */
			$this->recordUserHistory(array($uniqueId, 'fees-collection', "An amount of $amount was received from <strong>student-id::$studentId</strong> for the payment of <strong>fees-type::$feesType</strong>. The Outstanding balance is <strong>$outstandingBalance</strong>."));

			/* Print the success message */
			$status = 200;

			$data = 'Fee payment was successfully recorded. Issuing receipt in some few seconds...';
		} else {
			$data = '<div class="alert alert-danger">Sorry! There was no record for this Student. Please contact the Administrator in order to rectify this error before you can continue.</div>';
		}

		return [
			'data' => $data,
			'status' => $status,
			'uniqueId' => $uniqueId
		];
	}

}