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

	public function __construct($params) {
		parent::__construct();

        // get the client data
        $client_data = $this->client_data($params->clientId ?? null);

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term;
        $this->academic_year = $client_data->client_preferences->academics->academic_year;
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

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $filters = "";
		$filters .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : "";
        $filters .= isset($params->department_id) && !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : "";
        $filters .= !empty($student_id) ? " AND a.student_id IN ('{$student_id}')" : "";
        $filters .= isset($params->item_id) ? " AND a.item_id='{$params->item_id}'" : "";
        $filters .= isset($params->programme_id) && !empty($params->programme_id) ? " AND a.programme_id='{$params->programme_id}'" : "";
        $filters .= isset($params->category_id) && !empty($params->category_id) ? " AND a.category_id='{$params->category_id}'" : ""; 
        $filters .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $filters .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";
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

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        /** Init the user type */
        $class_id = $params->userData->class_id;

        /** The user id algorithm */
        if(in_array($params->userData->user_type, ["accountant", "admin"])) {
            $class_id = "";
        } else if(in_array($params->userData->user_type, ["parent"])) {
            // if the user is a parent
			$class_id = $this->session->student_class_id;
        }

        $params->query .= !empty($class_id) ? " AND a.class_id='{$class_id}'" : null;
        $params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id)) ? " AND a.id='{$params->category_id}'" : null;
        $params->query .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $params->query .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";

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
     * List Students Fees Allocation
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function students_fees_allocation(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        /** Init set the student id */
        $student_id = $params->userData->user_id;

        /** The user id algorithm */
        if(in_array($params->userData->user_type, ["accountant", "admin"])) {
            $student_id = "";
        } else if(in_array($params->userData->user_type, ["parent"])) {
            // if the user is a parent
			$student_id = $this->session->student_id;
        }

        /** Init the user type */
        $student_id = isset($params->student_id) ? $params->student_id : $student_id;

        $params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= !empty($student_id) ? " AND a.student_id='{$student_id}'" : null;
        $params->query .= (isset($params->record_id)) ? " AND a.id='{$params->record_id}'" : null;
        $params->query .= (isset($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id)) ? " AND a.category_id='{$params->category_id}'" : null;
        $params->query .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $params->query .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_info,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM fees_payments a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["student_info", "created_by_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["unique_id", "user_id", "name", "image", "user_type"]);
                    }
                }

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
     * List the Class Fees Allocation Array
     * 
     * @param String $params->clientId
     * 
     * @return Array
     */
    public function class_allocation_array(stdClass $params) {
        
        // load fees allocation list for class
        $class_allocation_list = "";
        $class_allocation_array = $this->class_fees_allocation($params)["data"];

        // if the result is not empty
        if(!empty($class_allocation_array)) {
            // loop through the results list
            foreach($class_allocation_array as $key => $each) {
                $class_allocation_list .= "<tr data-row_id=\"{$each->id}\">";
                $class_allocation_list .= "<td>".($key+1)."</td>";
                $class_allocation_list .= "<td>{$each->class_name}</td>";
                $class_allocation_list .= "<td>{$each->category_name}</td>";
                $class_allocation_list .= "<td>{$each->amount}</td>";
                $class_allocation_list .= "<td></td>";
                $class_allocation_list .= "</tr>";
            }
        }
        return $class_allocation_list;
    }

    /**
     * List the Student Fees Allocation Array
     * 
     * @param String $params->clientId
     * 
     * @return Array
     */
    public function student_allocation_array(stdClass $params) {
        
        // load fees allocation list for class
        $student_allocation_list = "";
        $student_allocation_array = $this->students_fees_allocation($params)["data"];

        // if the result is not empty
        if(!empty($student_allocation_array)) {

            // loop through the results list
            foreach($student_allocation_array as $key => $student) {
                // append to the url string
                $student_allocation_list .= "<tr data-row_id=\"{$student->id}\">";
                $student_allocation_list .= "<td>".($key+1)."</td>";
                $student_allocation_list .= "<td>
                    <div class='d-flex justify-content-start'>
                        ".(!empty($student->student_info->image) ? "
                        <div class='mr-2'><img src='{$this->baseUrl}{$student->student_info->image}' width='40px' height='40px'></div>" : "")."
                        <div>
                            {$student->student_info->name} <br class='p-0 m-0'>
                            <strong>{$student->student_info->unique_id}</strong>
                        </div>
                    </div>
                </td>";
                $student_allocation_list .= "<td>{$student->category_name}</td>";
                $student_allocation_list .= "<td>{$student->amount_due}</td>";
                $student_allocation_list .= "<td>{$student->amount_paid}</td>";

                // confirm if the user has the permission to make payment
                if(!empty($params->receivePayment)) {
                    $student_allocation_list .= "<td width='13%' class='pl-2'>";
                    // assign variable
                    $isPaid = (bool) ($student->amount_due < $student->amount_paid) || ($student->amount_due === $student->amount_paid);

                    // confirm if the fee has been paid
                    if($isPaid) {
                        $student_allocation_list .= "<span class='badge badge-success'>Paid</span>";
                    } else {
                        $student_allocation_list .= "<button onclick='return loadPage(\"{$this->baseUrl}fees-payment?record_id={$student->id}&student_id={$student->student_id}\");' class='btn btn-sm btn-outline-success'>Pay</button>";
                    }
                    // delete the record if possible => that is allowed only if the student has not already made an payment
                    if(!empty($params->canAllocate) && empty($student->amount_paid)) {
                        $student_allocation_list .= " &nbsp; <button onclick='return remove_Fees_Allocation(\"{$student->id}\",\"student\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button>";
                    }
                    $student_allocation_list .= "</td>";
                }

                $student_allocation_list .= "</tr>";
            }
        }
        return $student_allocation_list;
    }

    /**
     * Load the Fees Payment Form
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function payment_form(stdClass $params) {

        /** Load the payment information that has been allocated to the student */
        $allocation = $this->confirm_student_payment_record($params);

        /** If no allocation record was found */
        if(empty($allocation)) {
            return ["code" => 203, "data" => "Sorry! No allocation has been made for the selected category.
                Please ensure an allocation has been made before payment can be received."];
        }

        /** Format the last_payment_info value */
        if(!empty($allocation->last_payment_info)) {
                // b.amount,'|',b.description,'|',b.created_by,'|',b.recorded_date
            $allocation->last_payment_info = $this->stringToArray(
                $allocation->last_payment_info, "|",
                ["amount", "description", "created_by", "created_date"]
            );
        }

        /** Set the label for the amount */
        if(($allocation->amount_paid > 1) && (round($allocation->amount_due) > round($allocation->amount_paid))) {
            $label = "<span class='badge badge-primary'>Part Payment</span>";
        } elseif(round($allocation->amount_paid) == 0) {
            $label = "<span class='badge badge-danger'>Not Paid</span>";
        } else if(($allocation->amount_due < $allocation->amount_paid) || ($allocation->amount_due === $allocation->amount_paid)) {
            $label = "<span class='badge badge-success'>Paid</span>";
        }

        /** Quick CSS */
        $html_form = "<style>.t_table td {padding:10px;}</style>";
        
        /** Set the HMTL form to display */
        $html_form .= "<div class='table-responsive'>";
        $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";
        $html_form .= "<tr>";
        $html_form .= "<td width='60%'>Amount Due:</td>";
        $html_form .= "<td>{$allocation->amount_due}</td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Amount Paid:</td>";
        $html_form .= "<td><span class='amount_paid'>{$allocation->amount_paid}</span></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Outstanding Balance:</td>";
        $html_form .= "<td><span data-checkout_url='{$allocation->checkout_url}' data-amount_payable='{$allocation->balance}' class='outstanding'>{$allocation->balance}</span></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Last Payment Info:</td>";
        $html_form .= "<td><span class='last_payment_date'>{$allocation->last_payment_date}</span></td>";
        $html_form .= "</tr>";
        $html_form .= "<td>Status:</td>";
        $html_form .= "<td>{$label}</td>";
        $html_form .= "</tr>";
        $html_form .= "</table>";
        $html_form .= "</div>";

        return [
            "data" => [
                "form" => $html_form,
                "query" => $allocation
            ]
        ];

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

            /** Insert the existing record */
            $stmt = $myschoolgh->prepare("INSERT INTO fees_payments SET 
                amount_due = ?, balance = ?, category_id = ?, student_id = ?, checkout_url = ?,
                client_id = ?, academic_year = ?, academic_term = ?, class_id = ?, created_by = ?
            ");
            /** Execute the prepared statement */
            return $stmt->execute([
                $params->amount, $params->amount, $params->category_id, $params->student_id, 
                $params->clientId, random_string("alnum", 32),
                $params->academic_year, $params->academic_term, $params->class_id, $params->userId
            ]);
        }

        /** An annonymous function to update the student fees record */
        function update_student_fees(stdClass $params) {

            // global variable
            global $myschoolgh;

            /** Update the existing record */
            $stmt = $myschoolgh->prepare("UPDATE fees_payments SET 
                    amount_due = ?, balance = ($params->amount - amount_paid)
                WHERE category_id = ? AND student_id = ? AND client_id = ? 
                    AND academic_year = ? AND academic_term = ?");

            /** Execute the prepared statement */
            return $stmt->execute([
                $params->amount, $params->category_id, $params->student_id, $params->clientId, 
                $params->academic_year, $params->academic_term
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
                update_student_fees($params);
            } else {
                /** Insert a new record */
                insert_student_fees($params);
            }

            return ["data" => "Fees Allocation was successfully executed."];

        } elseif($params->allocate_to === "class") {

            /** Fetch all students that fall under this category */
            $student_param = (object) ["clientId" => $params->clientId, "class_id" => $params->class_id, "user_type" => "student", "minified" => "simplified"];
            $student_list = load_class("users", "controllers")->list($student_param);

            /** Confirm if a record already exist */
            if($this->confirm_class_payment_record($params)) {

                /** Update the existing record */
                $stmt = $this->db->prepare("UPDATE fees_allocations SET 
                    amount = ? WHERE category_id = ? AND client_id = ? AND academic_year = ? AND academic_term = ? AND class_id = ?
                ");
                
                /** Execute the prepared statement */
                $stmt->execute([
                    $params->amount, $params->category_id, $params->clientId, 
                    $params->academic_year, $params->academic_term, $params->class_id
                ]);
            } else {
                /** Insert the Record */
                $stmt = $this->db->prepare("INSERT INTO fees_allocations SET 
                    amount = ?, category_id = ?, client_id = ?, academic_year = ?, academic_term = ?, class_id = ?, created_by = ?
                ");
                
                /** Execute the prepared statement */
                $stmt->execute([
                    $params->amount, $params->category_id, $params->clientId, 
                    $params->academic_year, $params->academic_term, $params->class_id, $params->userId
                ]);
            }

            // loop through the students list
            foreach($student_list["data"] as $key => $student) {
                /** Append the student id as the current user id */
                $params->student_id = $student->user_id;

                /** Confirm if a record already exist */
                if($this->confirm_student_payment_record($params)) {
                    update_student_fees($params);
                } else {
                    /** Insert a new record */
                    insert_student_fees($params);
                }
            }

            return ["data" => "Fees Allocation was successfully executed."];

        }
    }

    /**
     * Allocated Fees Amount
     * 
     * @param stdClass $params
     *  
     * @return Array
     */
    public function allocate_fees_amount(stdClass $params) {

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

            /** Confirm if a record already exist */
            $query = $this->pushQuery("amount_due", "fees_payments", "student_id='{$params->student_id}' AND category_id='{$params->category_id}' AND class_id='{$params->class_id}' AND status='1' AND client_id='{$params->clientId}' ORDER BY id DESC LIMIT 1");

            return ["data" => $query[0]->amount_due ?? null];

        } elseif($params->allocate_to === "class") {

            /** Confirm if a record already exist */
            $query = $this->pushQuery("amount", "fees_collection", "class_id='{$params->class_id}' AND category_id='{$params->category_id}' AND client_id='{$params->clientId}' AND status='1' ORDER BY id DESC LIMIT 1");

            return ["data" => $query[0]->amount ?? null];

        }
    }

	/**
	 * @method confirm_class_payment_record
	 * @param String $params->student_id 	    This is the unique id of the student
	 * @param String $params->category_id		This is the fees type (tuition, ict, pta, or any other)
	 * @param String $params->academic_year	    This specifies the academic year to fetch the record
	 * @param String $params->academic_term 	This specifies the academic term that the record is been fetched
     * 
	 * @return Object
	 **/
	public function confirm_class_payment_record(stdClass $params) {
		try {
			$stmt = $this->db->prepare("
				SELECT amount 
				FROM fees_allocations 
				WHERE client_id = ? AND class_id = ? AND category_id = ? AND academic_year = ? AND academic_term = ? AND status = '1' LIMIT 1
			");
			$stmt->execute([$params->clientId, $params->class_id, $params->category_id, $params->academic_year, $params->academic_term]);
			
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_OBJ) : false;

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
     * 
	 * @return Object
	 **/
	public function confirm_student_payment_record(stdClass $params) {
		
        try {

            // return false if any parameter is missing
            if(!isset($params->student_id) || !isset($params->category_id)) {
                return null;
            }

            // run the query
			$stmt = $this->db->prepare("
				SELECT 
                    a.checkout_url, a.student_id, a.class_id, a.category_id, a.amount_due, a.amount_paid, a.balance, 
                    a.paid_status, a.last_payment_id, a.academic_year, a.academic_term, a.date_created, a.last_payment_date, 
                    (SELECT CONCAT(b.amount,'|',b.description,'|',b.created_by,'|',b.recorded_date) FROM fees_collection b WHERE b.item_id = a.last_payment_id LIMIT 1) AS last_payment_info
				FROM fees_payments a
				WHERE a.client_id = ? AND a.student_id = ? AND a.category_id = ? AND a.academic_year = ? AND a.academic_term = ? AND a.status = '1' LIMIT 1
			");
			$stmt->execute([$params->clientId, $params->student_id, $params->category_id, $params->academic_year, $params->academic_term]);
			
            return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_OBJ) : false;

		} catch(PDOException $e) {
            return false;
		}
	}

    /**
     * Make payment for the fees
     * 
     * @return Array
     */
	public function make_payment(stdClass $params) {

        
        try {

            // begin transaction
            $this->db->beginTransaction();

            /** Get the checkout details */
            $paymentRecord = $this->pushQuery("a.*, 
                (SELECT b.department FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS department_id,
                (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                (SELECT b.department FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_name", 
                "fees_payments a", "a.checkout_url='{$params->checkout_url}' AND a.client_id='{$params->clientId}' LIMIT 1
            ");

            /** If no allocation record was found */
            if(empty($paymentRecord)) {
                return ["code" => 203, "data" => "Sorry! An invalid checkout url was parsed for processing."];
            }
            // get the details of the record
            $paymentRecord = $paymentRecord[0];

            /* Outstanding balance calculator */
            $outstandingBalance = $paymentRecord->balance - $params->amount;
            $totalPayment = $paymentRecord->amount_paid + $params->amount;

            /* Confirm if the user has any credits */
            if($outstandingBalance < 0) {
                $creditBalance = $outstandingBalance * -1;
                $outstandingBalance = 0;
            }

            $uniqueId = random_string('alnum', 14);

            /* Record the payment made by the user */
            $stmt = $this->db->prepare("
                INSERT INTO fees_collection 
                SET client_id = ?, item_id = ?, student_id = ?, department_id = ?, class_id = ?, 
                category_id = ?, amount = ?, created_by = ?, academic_year = ?, academic_term = ?, description = ?
            ");
            $stmt->execute([
                $params->clientId, $uniqueId, $paymentRecord->student_id, $paymentRecord->department_id, 
                $paymentRecord->class_id, $paymentRecord->category_id, $params->amount, $params->userId, 
                $paymentRecord->academic_year, $paymentRecord->academic_term, $params->description
            ]);

            /* Update the user payment record */
            $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
            last_payment_date = now(), last_payment_id = '{$uniqueId}' ".(($outstandingBalance == 0) ? ", paid_status='1'" : "")."
            WHERE checkout_url = ? AND client_id = ? LIMIT 1");
            $stmt->execute([$totalPayment, $outstandingBalance, $params->checkout_url, $params->clientId]);

            /* Update the student credit balance */
            if(isset($creditBalance)) {
                // update the user data
                $this->db->query("UPDATE users SET account_balance = (account_balance + $creditBalance) WHERE item_id = ? AND client_id = '{$params->clientId}' LIMIT 1");
            }

            /* Record the user activity log */
            $this->userLogs("fees_payment", $params->checkout_url, null, "{$params->userData->name} received an amount of 
                <strong>{$params->amount}</strong> as Payment for <strong>{$paymentRecord->category_name}</strong> from <strong>{$paymentRecord->student_name}</strong>. 
                Outstanding Balance is <strong>{$outstandingBalance}</strong>", $params->userId);

            // commit the statements
            $this->db->commit();

            // return the success message
            return [
                "data" => "Fee payment was successfully recorded."
            ];

        } catch(PDOException $e) {
            // Role Back the statement
            $this->db->rollBack();

            // return an unexpected error notice
            return $this->unexpected_error;
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

}