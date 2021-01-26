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

	public function __construct(stdClass $params = null) {
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
			$student_id = isset($params->student_array_ids) ? $params->student_array_ids : $this->session->student_id;
        }

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $filters = "1";
		$filters .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id IN {$this->inList($params->class_id)}" : "";
        $filters .= isset($params->department_id) && !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : "";
        $filters .= !empty($student_id) ? " AND a.student_id IN {$this->inList($student_id)}" : "";
        $filters .= isset($params->item_id) ? " AND a.item_id='{$params->item_id}'" : "";
        $filters .= isset($params->programme_id) && !empty($params->programme_id) ? " AND a.programme_id='{$params->programme_id}'" : "";
        $filters .= isset($params->category_id) && !empty($params->category_id) ? " AND a.category_id IN {$this->inList($params->category_id)}" : ""; 
        $filters .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $filters .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";
        $filters .= isset($params->date) ? " AND DATE(a.recorded_date='{$params->date}')" : "";
        $filters .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "recorded_date") : null;

        // if the return_where_clause was parsed
        // then return the filters that have been pushed
        if(isset($params->return_where_clause)) {
            return $filters;
        }

		try {

			$stmt = $this->db->prepare("
				SELECT a.*,
                    (SELECT b.name FROM departments b WHERE b.id = a.department_id LIMIT 1) AS department_name,
                    (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type,'|',b.phone_number,'|',b.email) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_info
                FROM fees_collection a
				WHERE {$filters} AND a.client_id = ? ORDER BY DATE(a.recorded_date) DESC LIMIT {$params->limit}
            ");
			$stmt->execute([$params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // loop through the information
                foreach(["student_info", "created_by_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["unique_id", "user_id", "name", "image","last_seen","online","user_type", "phone_number", "email"]);
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
                $class_allocation_list .= "<td>{$each->currency} {$each->amount}</td>";
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

                // verify if the student has paid some amount
                $due = round($student->amount_due);
                $paid = round($student->amount_paid);
                $balance = round($student->balance);

                // assign variable
                $isPaid = (bool) ($student->amount_due < $student->amount_paid) || ($student->amount_due === $student->amount_paid);

                // label
                $label = "<span class='badge p-1 badge-success'>Paid</span>";
                if($due === $balance) {
                    $label = "<br><span class='badge p-1 badge-danger'>Not Paid</span>";
                } elseif($paid > 0 && !$isPaid) {
                    $label = "<br><span class='badge p-1 badge-primary'>Part Paid</span>";
                }

                // append to the url string
                $student_allocation_list .= "<tr data-row_id=\"{$student->id}\">";
                $student_allocation_list .= "<td>".($key+1)."</td>";
                $student_allocation_list .= "<td>
                    <div class='d-flex justify-content-start'>
                        ".(!empty($student->student_info->image) ? "
                        <div class='mr-2'>
                            <img src='{$this->baseUrl}{$student->student_info->image}' width='40px' height='40px'>
                        </div>" : "")."
                        <div>
                            {$student->student_info->name} <br class='p-0 m-0'>
                            <strong>{$student->student_info->unique_id}</strong>
                            {$label}
                        </div>
                    </div>
                </td>";
                $student_allocation_list .= "<td>{$student->category_name}</td>";
                $student_allocation_list .= "<td>{$student->currency} {$student->amount_due}</td>";
                $student_allocation_list .= "<td>{$student->currency} {$student->amount_paid}</td>";

                // confirm if the user has the permission to make payment
                if(!empty($params->receivePayment)) {
                    $student_allocation_list .= "<td width='13%' class='pl-2'>";

                    // confirm if the fee has been paid
                    if($isPaid) {
                        $student_allocation_list .= "<span class='badge badge-success'>Paid</span>";
                    } else {
                        $student_allocation_list .= "<button onclick='return loadPage(\"{$this->baseUrl}fees-payment?checkout_url={$student->checkout_url}\");' class='btn btn-sm btn-outline-success'>Pay</button>";
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
        $allocation = isset($params->allocation_info) ? $params->allocation_info : $this->confirm_student_payment_record($params);

        /** If no allocation record was found */
        if(empty($allocation)) {
            return ["code" => 203, "data" => "Sorry! No allocation has been made for the selected category.
                Please ensure an allocation has been made before payment can be received."];
        }

        /** Format the last_payment_info value */
        if(!empty($allocation->last_payment_info)) {
            // assign a key to the concat value
            $allocation->last_payment_info = $this->stringToArray(
                $allocation->last_payment_info, "|",
                ["pay_id", "amount", "created_by", "created_date", "currency", "description"]
            );
        }

        /** Set the label for the amount */
        if(($allocation->amount_paid > 1) && (round($allocation->amount_due) > round($allocation->amount_paid))) {
            $label = "<span data-payment_label='status' class='badge badge-primary'>Part Payment</span>";
        } elseif(round($allocation->amount_paid) === 0) {
            $label = "<span data-payment_label='status' class='badge badge-danger'>Not Paid</span>";
        } else if(($allocation->amount_due < $allocation->amount_paid) || ($allocation->amount_due === $allocation->amount_paid)) {
            $label = "<span data-payment_label='status' class='badge badge-success'>Paid</span>";
        } else {
            $label = "<span data-payment_label='status' class='badge badge-danger'>Not Paid</span>";    
        }

        /** Quick CSS */
        $html_form = "<style>.t_table td {padding:10px;}</style>";
        $currency = $params->client->client_preferences->labels->currency ?? null;

        /** Set the HMTL form to display */
        $html_form .= "<div class='table-responsive'>";
        $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";
        $html_form .= "<tr>";
        $html_form .= "<td width='55%'>Amount Due:</td>";
        $html_form .= "<td>{$currency} {$allocation->amount_due}</td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Amount Paid:</td>";
        $html_form .= "<td><span data-checkout_url='{$allocation->checkout_url}' class='amount_paid'>{$currency} {$allocation->amount_paid}</span></td>";
        $html_form .= "</tr>";
        $html_form .= "<tr>";
        $html_form .= "<td>Outstanding Balance:</td>";
        $html_form .= "<td><span data-checkout_url='{$allocation->checkout_url}' data-amount_payable='{$allocation->balance}' class='outstanding'>{$currency} {$allocation->balance}</span></td>";
        $html_form .= "</tr>";

        $html_form .= "<tr>";
        $html_form .= "<td>Status:</td>";
        $html_form .= "<td>{$label}</td>";
        $html_form .= "</tr>";
        $html_form .= "</table>";

        /** Last payment container */
        $html_form .= "<div class='last_payment_container'>";

        /** If last payment information is not empty */
        if(!empty($allocation->last_payment_info)) {

            // append value
            $allocation->last_payment_uid = $allocation->last_payment_info["pay_id"];
            $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";
            // set the rows for the last payment id
            $html_form .= "<tr>";
            $html_form .= "<td width='55%'>Last Payment Info:</td>";
            $html_form .= "
            <td>
                <span class='last_payment_id'><strong>Payment ID:</strong> {$allocation->last_payment_uid}</span><br>
                <span class='amount_paid'><i class='fa fa-money-bill'></i> {$allocation->last_payment_info["currency"]} {$allocation->last_payment_info["amount"]}</span><br>
                <span class='last_payment_date'><i class='fa fa-calendar-check'></i> {$allocation->last_payment_date}</span><br>
                <p class='mt-3 mb-0 pb-0' id='print_receipt'><a class='btn btn-sm btn-outline-primary' target='_blank' href='{$this->baseUrl}fees-view/{$allocation->last_payment_id}/print'><i class='fa fa-print'></i> Print Receipt</a></p>
            </td>";
            $html_form .= "</tr>";
            $html_form .= "</table>";
        }

        $html_form .= "</div>";
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

        global $defaultUser;

        $params->currency = $defaultUser->client->client_preferences->labels->currency ?? null;

        /** An annonymous function to insert the student fees record */
        function insert_student_fees(stdClass $params) {
            
            // global variable
            global $myschoolgh;

            /** Insert the existing record */
            $stmt = $myschoolgh->prepare("INSERT INTO fees_payments SET 
                amount_due = ?, balance = ?, category_id = ?, student_id = ?, checkout_url = ?, client_id = ?,
                academic_year = ?, academic_term = ?, class_id = ?, created_by = ?, currency = ?
            ");

            /** Execute the prepared statement */
            return $stmt->execute([
                $params->amount, $params->amount, $params->category_id, $params->student_id, 
                random_string("alnum", 32), $params->clientId, 
                $params->academic_year, $params->academic_term, $params->class_id, 
                $params->userId, $params->currency
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

        /** Check if the class id is valid */
        $class_check = $this->pushQuery("a.id, (SELECT b.name fees_category b WHERE b.id = '{$params->category_id}' LIMIT 1) AS category_name", "classes a", 
            "a.id='{$params->class_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1");

        if(empty($class_check)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        try {

            /** Start a transaction */
            $this->db->beginTransaction();

            /** Confirm if the allocate to is student */
            if($params->allocate_to === "student") {

                /** Return false if the student id was not parsed */
                if(empty($params->student_id)) {
                    return ["code" => 203, "data" => "Sorry! The student id cannot be left empty"];
                }

                /** Check if the student id is valid */
                $student_check = $this->pushQuery("a.id, a.name, (SELECT b.name fees_category b WHERE b.id = '{$params->category_id}' LIMIT 1) AS category_name", 
                    "users a", 
                    "a.item_id='{$params->student_id}' 
                        AND a.client_id='{$params->clientId}' 
                        AND a.status='1' AND a.deleted='0'
                        AND a.academic_year = '{$params->academic_year}' 
                        AND a.academic_term = '{$params->academic_term}'
                    LIMIT 1");
                if(empty($student_check)) {
                    return ["code" => 203, "data" => "Sorry! An invalid student id was supplied."];
                }

                /** Confirm if a record already exist */
                if($this->confirm_student_payment_record($params, "simple_load")) {
                    // update the user information
                    update_student_fees($params);
                    
                    // log the user activity
                    $this->userLogs("fees_allocation", $params->student_id, null, 
                        "{$params->userData->name} updated the fee allocation for <strong>{$student_check[0]->category_name}</strong> to: <strong>{$params->currency} {$params->amount}</strong>", $params->userId);

                } else {
                    /** Insert a new record */
                    insert_student_fees($params);

                    // log the user activity
                    $this->userLogs("fees_allocation", $params->student_id, null, 
                        "{$params->userData->name} added the fee allocation for <strong>{$student_check[0]->category_name}</strong> of: <strong>{$params->currency} {$params->amount}</strong>", $params->userId);
                }

                $this->db->commit();

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

                     // log the user activity
                    $this->userLogs("fees_allocation", $params->student_id, null, 
                        "{$params->userData->name} updated the fee allocation for <strong>{$class_check[0]->category_name}</strong> to: <strong>{$params->currency} {$params->amount}</strong>", $params->userId);

                } else {

                    /** Insert the Record */
                    $stmt = $this->db->prepare("INSERT INTO fees_allocations SET 
                        amount = ?, category_id = ?, client_id = ?, academic_year = ?, 
                        academic_term = ?, class_id = ?, created_by = ?, currency = ?
                    ");
                    
                    /** Execute the prepared statement */
                    $stmt->execute([
                        $params->amount, $params->category_id, $params->clientId, 
                        $params->academic_year, $params->academic_term, $params->class_id,
                        $params->userId, $params->currency
                    ]);

                    // log the user activity
                    $this->userLogs("fees_allocation", $params->student_id, null, 
                        "{$params->userData->name} added the fee allocation for <strong>{$class_check[0]->category_name}</strong> of: <strong>{$params->currency} {$params->amount}</strong>", $params->userId);
                }

                // remove some unimportant keys
                unset($params->userData);
                
                // loop through the students list
                foreach($student_list["data"] as $key => $student) {
                    /** Append the student id as the current user id */
                    $params->student_id = $student->user_id;
                    
                    /** Confirm if a record already exist */
                    if($this->confirm_student_payment_record($params, "simple_load")) {
                        update_student_fees($params);
                    } else {
                        /** Insert a new record */
                        insert_student_fees($params);
                    }
                }

                $this->db->commit();

                return ["data" => "Fees Allocation was successfully executed."];

            }
        } catch(PDOException $e) {
            $this->db->rollBack();
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
            $query = $this->pushQuery("amount", "fees_allocations", "class_id='{$params->class_id}' AND category_id='{$params->category_id}' AND client_id='{$params->clientId}' AND status='1' ORDER BY id DESC LIMIT 1");

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
     * @param String $load_type                 This ascertains how the query should take form
     * 
	 * @return Object
	 **/
	public function confirm_student_payment_record(stdClass $params, $load_type = "full") {
		
        try {

            // return false if any parameter is missing
            if((!isset($params->student_id) || !isset($params->category_id)) && !isset($params->checkout_url)) {
                return null;
            }

            // run the query
			$stmt = $this->db->prepare("
				SELECT 
                    a.checkout_url, a.student_id, a.class_id, a.category_id, a.amount_due, a.amount_paid, a.balance, 
                    a.paid_status, a.last_payment_id, a.academic_year, a.academic_term, a.date_created, a.last_payment_date,
                    u.name AS student_name, u.department AS department_id, a.currency,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                    (
                        SELECT CONCAT(b.id,'|',b.amount,'|',b.created_by,'|',b.recorded_date,'|',b.currency,'|',b.description) 
                        FROM fees_collection b 
                        WHERE b.item_id = a.last_payment_id LIMIT 1
                    ) AS last_payment_info
				FROM fees_payments a
                LEFT JOIN users u ON u.item_id = a.student_id
				WHERE ".(isset($params->checkout_url) ? "checkout_url='{$params->checkout_url}'" : 
                    " a.student_id = '{$params->student_id}' AND 
                        a.category_id = '{$params->category_id}' AND a.academic_year = '{$params->academic_year}'
                        AND a.academic_term = '{$params->academic_term}'
                ")." AND a.client_id = '{$params->clientId}' AND a.status = '1' LIMIT 1
			");
			$stmt->execute();
			
            $result = ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_OBJ) : false;

            // if clean_payment_info was parsed then query below
            if(!empty($result) && isset($params->clean_payment_info)) {
                // convert the last payment information into an array
                $result->last_payment_info = $this->stringToArray($result->last_payment_info, "|",
                    ["pay_id", "amount", "created_by", "created_date", "currency", "description"]
                );
            }

            return $result;

		} catch(PDOException $e) {
            return false;
		}
	}

    /**
     * Make payment for the fees
     * 
     * @param String        $params->checkout_url
     * @param Float         $params->amount
     * 
     * @return Array
     */
	public function make_payment(stdClass $params) {

        
        try {

            global $defaultUser;

            // begin transaction
            $this->db->beginTransaction();

            /** Get the checkout details */
            $paymentRecord = $this->confirm_student_payment_record($params);

            /** If no allocation record was found */
            if(empty($paymentRecord)) {
                return ["code" => 203, "data" => "Sorry! An invalid checkout url was parsed for processing."];
            }

            /* Outstanding balance calculator */
            $outstandingBalance = $paymentRecord->balance - $params->amount;
            $totalPayment = $paymentRecord->amount_paid + $params->amount;

            $paid_status = (bool) ((round($totalPayment) === round($paymentRecord->amount_due)) || ($totalPayment > $paymentRecord->amount_due));

            /* Confirm if the user has any credits */
            if($outstandingBalance < 0) {
                $creditBalance = $outstandingBalance * -1;
                $outstandingBalance = 0;
            }

            // generate a unique id for the payment record
            $uniqueId = random_string('alnum', 32);

            $currency = $defaultUser->client->client_preferences->labels->currency ?? null;

            /* Record the payment made by the user */
            $stmt = $this->db->prepare("
                INSERT INTO fees_collection 
                SET client_id = ?, item_id = ?, student_id = ?, department_id = ?, class_id = ?, 
                category_id = ?, amount = ?, created_by = ?, academic_year = ?, 
                academic_term = ?, description = ?, currency = ?
            ");
            $stmt->execute([
                $params->clientId, $uniqueId, $paymentRecord->student_id, $paymentRecord->department_id, 
                $paymentRecord->class_id, $paymentRecord->category_id, $params->amount, $params->userId, 
                $paymentRecord->academic_year, $paymentRecord->academic_term, 
                $params->description ?? "null", $currency
            ]);

            /* Update the user payment record */
            $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
                last_payment_date = now(), last_payment_id = '{$uniqueId}' ".($paid_status ? ", paid_status='1'" : "")."
                WHERE checkout_url = ? AND client_id = ? LIMIT 1
            ");
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

            // append to the query
            $params->clean_payment_info = true;

            // return the success message
            return [
                "data" => "Fee payment was successfully recorded.",
                "additional" => [
                    "payment" => $this->confirm_student_payment_record($params)
                ]
            ];

        } catch(PDOException $e) {
            // Role Back the statement
            $this->db->rollBack();

            // return an unexpected error notice
            return $this->unexpected_error;
        }
        
	}
    
}