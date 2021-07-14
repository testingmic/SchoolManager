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

    private $iclient = [];
    private $mnotify_key = "3LhA1Cedn4f2qzkTPO3cIkRz8pv0inBl9TWavaoTeEVFe";

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
        $this->iclient = $client_data;

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

        global $usersClass;

        $params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;

        /** Init the user type */
        $student_id = $params->student_id ?? $params->userData->user_id;
        
        /** The user id algorithm */
        if(!isset($params->student_id) && in_array($params->userData->user_type, ["accountant", "admin"])) {
            $student_id = "";
        } else if(!isset($params->student_id) && in_array($params->userData->user_type, ["parent"])) {
            // if the user is a parent
			$student_id = isset($params->student_array_ids) ? $params->student_array_ids : $this->session->student_id;
        }

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $filters = "1";
		$filters .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id IN {$this->inList($params->class_id)}" : "";
        $filters .= isset($params->department_id) && !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : "";
        $filters .= !empty($student_id) ? " AND a.student_id IN {$this->inList($student_id)}" : "";
        $filters .= isset($params->item_id) && !empty($params->item_id) ? " AND a.item_id='{$params->item_id}'" : "";
        $filters .= isset($params->query) && !empty($params->query) ? " AND {$params->query}" : "";
        $filters .= isset($params->programme_id) && !empty($params->programme_id) ? " AND a.programme_id='{$params->programme_id}'" : "";
        $filters .= isset($params->category_id) && !empty($params->category_id) ? " AND a.category_id IN {$this->inList($params->category_id)}" : ""; 
        $filters .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}' AND u.academic_year='{$params->academic_year}'" : "";
        $filters .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}' AND u.academic_term='{$params->academic_term}'" : "";
        $filters .= isset($params->date) && !empty($params->date) ? " AND DATE(a.recorded_date='{$params->date}')" : "";
        $filters .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "recorded_date") : null;

        // if the return_where_clause was parsed
        // then return the filters that have been pushed
        if(isset($params->return_where_clause)) {
            return $filters;
        }

		try {

			$stmt = $this->db->prepare("
				SELECT a.*, fc.name AS category_name,
                    (SELECT b.name FROM departments b WHERE b.id = a.department_id LIMIT 1) AS department_name,
                    (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type,'|',COALESCE(b.phone_number,'NULL'),'|',b.email) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.guardian_id,'NULL')) FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_info
                FROM fees_collection a
                LEFT JOIN users u ON u.item_id = a.student_id
                LEFT JOIN fees_category fc ON fc.id = a.category_id
				WHERE {$filters} AND a.client_id = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
			$stmt->execute([$params->clientId]);
            
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // convert the created by string into an object
                $result->created_by_info = (object) $this->stringToArray($result->created_by_info, "|", ["unique_id", "user_id", "name", "image","user_type", "phone_number", "email"]);
                $result->student_info = (object) $this->stringToArray($result->student_info, "|", ["unique_id", "user_id", "name", "image","user_type", "phone_number", "guardian_id"]);

                $result->student_info->guardian_id = isset($result->student_info->guardian_id) ? $usersClass->guardian_list($result->student_info->guardian_id, $result->client_id, true) : [];
                
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
     * Search for a Payment Log
     * 
     * @param String $stdClass->term
     * 
     * @return Array
     */
    public function search(stdClass $params) {
        
        // if the search term is empty
        if(empty($params->term)) {
            return [
                "code" => 201,
                "data" => "Sorry! The search term is required."
            ];
        }

        try {
            
            $query = null;
            $query .= preg_match("/^[0-9]+$/", $params->term) ? "(a.id = '{$params->term}'" : null;
            $query .= !empty($query) && preg_match("/^[0-9a-z]+$/", $params->term) ? " OR u.name LIKE '%{$params->term}%'" : "(u.name LIKE '%{$params->term}%'";
            $params->term = strtoupper($params->term);
            $query .= preg_match("/^[0-9A-Z]+$/", $params->term) ? " OR a.receipt_id='{$params->term}'" : null;
            $query .= ")";

            $params->query = $query;

            return $this->list($params);

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

        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->code) && !empty($params->code)) ? " AND a.code='{$params->code}'" : null;
        $params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id) && !empty($params->category_id)) ? " AND a.id='{$params->category_id}'" : null;

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
                $class_allocation_list .= "<td align='center'></td>";
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
        
        // global variables
        global $isStudent, $isParent, $isWardParent, $defaultUser;

        // load fees allocation list for class
        $owning = false;
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
                    $label = "<br><span class='badge p-1 badge-primary'>Partly Paid</span>";
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
                    $student_allocation_list .= "<td width='13%' align='center' class='pl-2'>";

                    // confirm if the fee has been paid
                    if($isPaid) {
                        $student_allocation_list .= "<span class='badge badge-success'>Paid</span>";
                    } else {
                        $owning = true;
                        $_class = "class='btn btn-sm btn-outline-success'";
                        $student_allocation_list .= $isParent ? "
                            <a {$_class} href='{$this->baseUrl}payment_checkout/{$defaultUser->client_id}/fees/{$student->checkout_url}/checkout' target='_blank'>Pay Fee</a>
                        " : "<button onclick='return loadPage(\"{$this->baseUrl}fees-payment?checkout_url={$student->checkout_url}\");' {$_class}>Pay Fee</button>";
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
        if(isset($params->parse_owning)) {
            return [
                "list" => $student_allocation_list,
                "owning" => $owning
            ];
        } else {
            return $student_allocation_list;
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

        /** Load the payment information that has been allocated to the student */
        $allocation = isset($params->allocation_info) ? $params->allocation_info : $this->confirm_student_payment_record($params);
        
        /** If no allocation record was found */
        if(empty($allocation)) {
            return ["code" => 203, "data" => "Sorry! No allocation has been made for the selected category.
                Please ensure an allocation has been made before payment can be received."];
        }

        // set the first item
        $html_form = "<style>.t_table td {padding:10px;}</style>";
        $html_form .= "<div class='table-responsive'>";
        
        // response to return
        $response = [];
        
        /** Quick CSS */
        $currency = $params->client->client_preferences->labels->currency ?? null;

        // if the item is not an array
        if(!is_array($allocation)) {

            // append the data allocation
            $data_content = $allocation;
            $outstanding = $allocation->balance;

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

            /** Set the HMTL form to display */
            $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";
            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold' width='43%'>Amount Due:</td>";
            $html_form .= "<td>{$currency} {$allocation->amount_due}</td>";
            $html_form .= "</tr>";
            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold'>Amount Paid:</td>";
            $html_form .= "<td><span data-checkout_url='{$allocation->checkout_url}' class='amount_paid'>{$currency} {$allocation->amount_paid}</span></td>";
            $html_form .= "</tr>";
            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold'>Outstanding Balance:</td>";
            $html_form .= "<td><span data-checkout_url='{$allocation->checkout_url}' data-amount_payable='{$outstanding}' class='outstanding'>{$currency} {$allocation->balance}</span></td>";
            $html_form .= "</tr>";

            $html_form .= "<tr>";
            $html_form .= "<td>Status:</td>";
            $html_form .= "<td>{$label}</td>";
            $html_form .= "</tr>";
            $html_form .= "</table>";

            // the query attached
            $response["query"] = $allocation;

        } elseif(is_array($allocation)) {

            // initials
            $amount_due = 0;
            $amount_paid = 0;
            $balance = 0;
            $owings_list = "<table width='100%' class='t_table mt-4 table-hover table-bordered'>";
            $owings_list .= "
                <tr class='font-weight-bold'>
                    <td>CATEGORY NAME</td>
                    <td>AMOUNT DUE</td>
                    <td>AMOUNT PAID</td>
                    <td>BALANCE</td>
                    <td></td>
                </tr>";
            $data_content = null;

            // loop through the allocations list
            foreach($allocation as $fees) {

                // add up to the values
                $balance += $fees->balance;
                $amount_due += $fees->amount_due;
                $amount_paid += $fees->amount_paid;
            
                // assign a key to the concat value
                $last_payment = $this->stringToArray(
                    $fees->last_payment_info, "|", [
                        "pay_id", "amount", "created_by", "created_date", "currency", "description", 
                        "payment_method","cheque_bank", "cheque_number", "paidin_by", "paidin_contact", "payment_uid"
                    ]
                );
                
                // append to the owings list
                $owings_list .= "<tr>
                    <td class='font-weight-bold'>{$fees->category_name}</td>
                    <td>{$fees->amount_due}</td>
                    <td>{$fees->amount_paid}</td>
                    <td class='font-weight-bold'>{$fees->balance}</td>";

                // if the last payment info is not empty
                if(!empty($last_payment)) {
                    // append to the list
                    $owings_list .= "<td>
                        <span class='last_payment_id'><strong>Payment ID:</strong> {$last_payment["pay_id"]}</span><br>
                        <span class='amount_paid'><i class='fa fa-money-bill'></i> {$last_payment["currency"]} {$last_payment["amount"]}</span><br>
                        <span class='last_payment_date'><i class='fa fa-calendar-check'></i> {$last_payment["created_date"]}</span><br>
                        <span><strong>Payment Method:</strong> {$last_payment["payment_method"]}</span><br>";
                    
                    // if the payment method is a cheque
                    if($last_payment["payment_method"] === "Cheque") {
                        // check bank
                        $cheque_bank = explode("::", $last_payment["cheque_bank"])[0];

                        // show the check number and bank name
                        $owings_list .= "<span><strong>Bank:</strong> {$cheque_bank}</span><br>";
                        $owings_list .= "<span><strong>Cheque Number:</strong> {$last_payment["cheque_number"]}</span><br>";
                    }
                    
                    // show the paid button
                    $owings_list .= "<p class='mt-3 mb-0 pb-0' id='print_receipt'><a href='{$this->baseUrl}receipt/{$last_payment["payment_uid"]}' class='btn btn-sm btn-outline-primary' target='_blank'><i class='fa fa-print'></i> Print Receipt</a></p>";

                    $owings_list .= "</td>";
                } else {
                    $owings_list .= "<td></td>";
                }
                    
                $owings_list .= "</tr>";
            }

            $owings_list .= "</table>";

            /** Set the label for the amount */
            if(($amount_paid > 1) && (round($amount_due) > round($amount_paid))) {
                $status = 2;
                $label = "<span data-payment_label='status' class='badge badge-primary'>Part Payment</span>";
            } elseif(round($amount_paid) === 0) {
                $status = 0;
                $label = "<span data-payment_label='status' class='badge badge-danger'>Not Paid</span>";
            } else if(($amount_due < $amount_paid) || ($amount_due === $amount_paid)) {
                $status = 1;
                $label = "<span data-payment_label='status' class='badge badge-success'>Paid</span>";
            } else {
                $status = 0;
                $label = "<span data-payment_label='status' class='badge badge-danger'>Not Paid</span>";    
            }

            /**  */
            $balance = $balance;
            $amount_due = number_format($amount_due, 2);
            $amount_paid = number_format($amount_paid, 2);
            
            /** Set the HMTL form to display */
            $html_form .= "<div class='table-responsive'>";
            $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";
            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold' width='43%'>Amount Due:</td>";
            $html_form .= "<td>{$currency} {$amount_due}</td>";
            $html_form .= "</tr>";
            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold'>Amount Paid:</td>";
            $html_form .= "<td><span data-checkout_url='general' class='amount_paid'>{$currency} {$amount_paid}</span></td>";
            $html_form .= "</tr>";

            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold'>Outstanding Balance:</td>";
            $html_form .= "<td><span data-checkout_url='general' data-amount_payable='{$balance}' class='outstanding'>{$currency} ".number_format($balance, 2)."</span></td>";
            $html_form .= "</tr>";

            $html_form .= "<tr>";
            $html_form .= "<td class='font-weight-bold'>Status:</td>";
            $html_form .= "<td>{$label}</td>";
            $html_form .= "</tr>";

            $html_form .= "</table>";
            $html_form .= $owings_list;

            // append the multiple schedule
            $response["uncategorized"] = 1;
            $response["paid_status"] = $status;

        }

        /** Format the last_payment_info value */
        if(!empty($data_content->last_payment_info)) {
            // assign a key to the concat value
            $data_content->last_payment_info = $this->stringToArray(
                $data_content->last_payment_info, "|", [
                    "pay_id", "amount", "created_by", "created_date", "currency", "description", 
                    "payment_method","cheque_bank", "cheque_number", "paidin_by", "paidin_contact", "payment_uid"
                ]
            );
        }

        /** Last payment container */
        $html_form .= "<input name='fees_payment_student_id' hidden type='hidden' value='".($params->student_id ?? $allocation->student_id)."' readonly>";
        $html_form .= "<input name='fees_payment_category_id' hidden type='hidden' value='".($params->category_id ?? null)."' readonly>";
        $html_form .= "<div class='last_payment_container'>";
        
        /** If last payment information is not empty */
        if(!empty($data_content->last_payment_info)) {
            
            // append value
            $data_content->last_payment_uid = $data_content->last_payment_info["pay_id"];
            $html_form .= "<table width='100%' class='t_table table-hover table-bordered'>";

            // set the rows for the last payment id
            $html_form .= "<tr>";
            $html_form .= "<td width='43%' class='font-weight-bold'>Last Payment Info:</td><td>";
            
            // payment information
            $html_form .= "<span class='last_payment_id'><strong>Payment ID:</strong> {$data_content->last_payment_uid}</span><br>
                <span class='amount_paid'><i class='fa fa-money-bill'></i> {$data_content->last_payment_info["currency"]} {$data_content->last_payment_info["amount"]}</span><br>
                <span class='last_payment_date'><i class='fa fa-calendar-check'></i> {$data_content->last_payment_date}</span><br>";

            // payment method
            $html_form .= "<hr class=\"mt-1 mb-1\">
                <span><strong>Payment Method:</strong> {$data_content->last_payment_info["payment_method"]}</span><br>";

            // if the payment method is a cheque
            if($data_content->last_payment_info["payment_method"] === "Cheque") {
                // check bank
                $cheque_bank = explode("::", $data_content->last_payment_info["cheque_bank"])[0];

                // show the check number and bank name
                $html_form .= "<span><strong>Bank:</strong> {$cheque_bank}</span><br>";
                $html_form .= "<span><strong>Cheque Number:</strong> {$data_content->last_payment_info["cheque_number"]}</span><br>";
            }
            
            // show the paid button
            $html_form .= "<p class='mt-3 mb-0 pb-0' id='print_receipt'><a href='{$this->baseUrl}receipt/{$data_content->last_payment_id}' class='btn btn-sm btn-outline-primary' target='_blank'><i class='fa fa-print'></i> Print Receipt</a></p>";
            $html_form .= "</td></tr>";
            $html_form .= "</table>";
        }

        $html_form .= "</div>";
        $html_form .= "</div>";

        $response["form"] = $html_form;

        return ["data" => $response];

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

        try {

             /** Check if the class id is valid */
            $class_check = $this->pushQuery("a.id, (SELECT b.name FROM fees_category b WHERE b.id = '{$params->category_id}' LIMIT 1) AS category_name", 
                "classes a", 
                "a.id='{$params->class_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1");

            if(empty($class_check)) {
                return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
            }

            /** Start a transaction */
            $this->db->beginTransaction();

            /** Confirm if the allocate to is student */
            if($params->allocate_to === "student") {

                /** Return false if the student id was not parsed */
                if(empty($params->student_id)) {
                    return ["code" => 203, "data" => "Sorry! The student id cannot be left empty"];
                }

                /** Check if the student id is valid */
                $student_check = $this->pushQuery(
                    "a.id, a.name, (SELECT b.name FROM fees_category b WHERE b.id = '{$params->category_id}' LIMIT 1) AS category_name", 
                    "users a", 
                    "a.item_id='{$params->student_id}' AND a.client_id='{$params->clientId}' 
                        AND a.status='1' AND a.deleted='0' AND a.academic_year = '{$params->academic_year}' 
                        AND a.academic_term = '{$params->academic_term}' LIMIT 1");
                
                // return error if the student was not found
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
                    $this->userLogs("fees_allocation", $params->student_id ?? $params->class_id, null, 
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
                    $this->userLogs("fees_allocation", $params->class_id ?? $params->student_id, null, 
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
            return $this->unexpected_error;
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
            $query = $this->pushQuery("amount_due AS default_amount", "fees_payments", "student_id='{$params->student_id}' AND category_id='{$params->category_id}' AND class_id='{$params->class_id}' AND status='1' AND client_id='{$params->clientId}' ORDER BY id DESC LIMIT 1");
            
            // run this query if the init is empty
            if(empty($query)) {
                // default amount
                $query = $this->pushQuery(
                    "b.amount AS default_amount", 
                    "fees_category b", 
                    "b.id='{$params->category_id}' AND b.client_id='{$params->clientId}' LIMIT 1"
                );
            }

            // assign the amount
            $amount = $query[0]->default_amount ?? 0;

            // return the amount
            return ["data" => $amount];

        } elseif($params->allocate_to === "class") {

            /** Confirm if a record already exist */
            $query = $this->pushQuery(
                "a.amount as default_amount, (SELECT b.amount FROM fees_category b WHERE b.id='{$params->category_id}' AND b.client_id='{$params->clientId}' LIMIT 1) AS default_amount", 
                "fees_allocations a", 
                "a.class_id='{$params->class_id}' AND a.category_id='{$params->category_id}' AND a.client_id='{$params->clientId}' AND a.status='1' ORDER BY a.id DESC LIMIT 1"
            );
            
            // run this query if the init is empty
            if(empty($query)) {
                // default amount
                $query = $this->pushQuery(
                    "b.amount AS default_amount", 
                    "fees_category b", 
                    "b.id='{$params->category_id}' AND b.client_id='{$params->clientId}' LIMIT 1"
                );
            }

            // assign the amount
            $amount = $query[0]->default_amount ?? 0;

            // return the amount
            return ["data" => $amount];

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
            if(!isset($params->student_id) && !isset($params->checkout_url)) {
                return null;
            }

            // if the category id is not empty
            $category_id = isset($params->category_id) && !empty($params->category_id) ? $params->category_id : null ;

            // set the academic year and term
            $academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year; 
            $academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
            
            // run the query
			$stmt = $this->db->prepare("
				SELECT 
                    a.checkout_url, a.student_id, a.class_id, a.category_id, a.amount_due, a.amount_paid, 
                    a.balance, a.paid_status, a.last_payment_id, a.academic_year, a.academic_term, a.date_created, 
                    a.last_payment_date, a.currency,
                    (
                        SELECT 
                            CONCAT(
                                u.name,'|',COALESCE(u.department, 'NULL'),'|',COALESCE(u.account_balance,'0'),'|',COALESCE(u.unique_id,'0')
                            ) 
                        FROM users u 
                        WHERE 
                            u.academic_year = '{$academic_year}' AND u.academic_term = '{$academic_term}' AND u.item_id = a.student_id
                    ) as student_details,
                    (SELECT b.name FROM fees_category b WHERE b.id = a.category_id LIMIT 1) AS category_name,
                    (
                        SELECT 
                            CONCAT (
                                b.id,'|',b.amount,'|',b.created_by,'|',b.recorded_date,'|',
                                b.currency,'|',COALESCE(b.description,'NULL'),'|',
                                COALESCE(b.payment_method,'NULL'),'|',COALESCE(b.cheque_bank,'NULL'),'|',
                                COALESCE(b.cheque_number,'NULL'),'|',COALESCE(b.paidin_by,'NULL'),'|',
                                COALESCE(b.paidin_contact,'NULL'),'|',b.item_id
                            )
                        FROM fees_collection b 
                        WHERE b.item_id = a.last_payment_id LIMIT 1
                    ) AS last_payment_info
				FROM fees_payments a
				WHERE ".(isset($params->checkout_url) && ($params->checkout_url != "general") ? "checkout_url='{$params->checkout_url}'" : 
                    " a.student_id = '{$params->student_id}'
                    ".(!empty($category_id) ? " AND a.category_id = '{$params->category_id}'" : null)."
                ")." AND a.academic_year = '{$academic_year}' AND a.academic_term = '{$academic_term}' 
                    AND a.client_id = '{$params->clientId}' AND a.status = '1' LIMIT ".(!empty($category_id) ? 1 : 100)."
			");
			$stmt->execute();

            // count the number of rows found
            $showInfo = (bool) isset($params->clean_payment_info);

            // if clean_payment_info was parsed then query below
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // payment information
                $result->student_details = $this->stringToArray($result->student_details, "|", ["student_name", "department_id", "account_balance", "unique_id"], true);

                // set the account balance
                $result->account_balance = isset($result->student_details["account_balance"]) ? (float) $result->student_details["account_balance"] : 0;

                // if show payment info was also parsed in the request
                if($showInfo) {
                    // convert the last payment information into an array
                    $result->last_payment_info = $this->stringToArray($result->last_payment_info, "|",
                        ["pay_id", "amount", "created_by", "created_date", "currency", "description", "payment_method", 
                            "cheque_bank", "cheque_number", "paidin_by", "paidin_contact", "payment_uid"]
                    );
                }

                // append to the data array
                $data[] = $result;
            }

            return !empty($data) && $category_id ? $data[0] : $data;

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
            
            // get the preference of the client
            $preference = $this->iclient->client_preferences->labels;
            
            // begin transaction
            $this->db->beginTransaction();

            /** Get the checkout details */
            $params->clean_payment_info = true;
            $paymentRecord = $this->confirm_student_payment_record($params);

            /** If no allocation record was found */
            if(empty($paymentRecord)) {
                return ["code" => 203, "data" => "Sorry! An invalid checkout url was parsed for processing."];
            }

            /** Validate the amount */
            if(!$params->amount) {
                return ["code" => 203, "data" => "Sorry! The amount cannot be empty."];
            }

            /** Validate email address */
            if(!empty($params->email_address) && !filter_var($params->email_address, FILTER_VALIDATE_EMAIL)) {
                return ["code" => 203, "data" => "Sorry! A valid email address is required."];
            }

            // confirm if the data parsed is an array
            if(is_array($paymentRecord)) {

                // initials    
                $amount_due = 0;
                $total_amount_paid = 0;
                $balance = 0;

                $fees_list = [];
                $amount_paid = [];
                $paying = $params->amount;

                // loop through the allocations list
                foreach($paymentRecord as $fee) {

                    // add up to the values
                    $balance += $fee->balance;
                    $amount_due += $fee->amount_due;
                    $total_amount_paid += $fee->amount_paid;

                    // algorithm to get the items being paid for
                    if($paying > 0) {
                        if(($fee->balance < $paying) || ($fee->balance == $paying)) {
                            $paying = $paying - $fee->balance;
                            $fees_list[$fee->category_id] = 0;
                            // if the paid status is not equal to one
                            if($fee->balance != 0.00) {
                                $amount_paid[$fee->category_id] = $fee->balance;
                            }
                        } elseif($fee->balance > $paying) {
                            $n_value = $fee->balance - $paying;
                            $amount_paid[$fee->category_id] = $paying;
                            $fees_list[$fee->category_id] = $n_value;
                            $paying = 0;
                        } else {
                            $n_value = $fee->balance - $paying;
                            $amount_paid[$fee->category_id] = $paying;
                            $fees_list[$fee->category_id] = $n_value;
                            $paying -= $fee->balance; 
                        }
                    }
                }

                /* Outstanding balance calculator */
                $outstandingBalance = $balance - $params->amount;
                $totalPayment = $total_amount_paid + $params->amount;

                // set the paid status
                $paid_status = ((round($totalPayment) === round($outstandingBalance)) || (round($totalPayment) > round($outstandingBalance))) ? 1 : 2;
                
            } else {
                /* Outstanding balance calculator */
                $outstandingBalance = $paymentRecord->balance - $params->amount;
                $totalPayment = $paymentRecord->amount_paid + $params->amount;

                // set the paid status
                $paid_status = ((round($totalPayment) === round($paymentRecord->amount_due)) || (round($totalPayment) > round($paymentRecord->amount_due))) ? 1 : 2;
            }

            /* Confirm if the user has any credits */
            if($outstandingBalance < 0) {
                $creditBalance = $outstandingBalance * -1;
                $outstandingBalance = 0;
            }

            // get the currency
            $params->payment_method = isset($params->payment_method) ? ucfirst($params->payment_method) : "Cash";
            $currency = $defaultUser->client->client_preferences->labels->currency ?? null;

            // set this to boolean
            $append_sql = (bool) ($params->payment_method === "Cheque");

            // ensure that the bank_id and the cheque number are not empty
            if(!empty($append_sql) && (empty($params->bank_id) || empty($params->cheque_number))) {
                return ["code" => 203, "data" => "Sorry! The bank name and cheque number cannot be empty."];
            }

            // append additional sql
            $append_sql = !empty($append_sql) ? ", cheque_security='".($params->cheque_security ?? null)."', cheque_bank='{$params->bank_id}', cheque_number='{$params->cheque_number}'" : null;
            
            // if the payment method is momo or card payment
            $append_sql .= ", paidin_by='".($params->email_address ?? null)."', paidin_contact='".($params->contact_number ?? null)."'";

            /* Record the payment made by the user */
            if(!is_array($paymentRecord)) {

                // generate a unique id for the payment record
                $uniqueId = random_string('alnum', 15);
                $counter = $this->append_zeros(($this->itemsCount("fees_collection", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $receiptId = $this->iclient->client_preferences->labels->receipt_label.$counter;
                $receiptId = strtoupper($receiptId);

                // log the payment record
                $stmt = $this->db->prepare("INSERT INTO fees_collection
                    SET client_id = ?, item_id = ?, student_id = ?, department_id = ?, class_id = ?, 
                    category_id = ?, amount = ?, created_by = ?, academic_year = ?, academic_term = ?, 
                    description = ?, currency = ?, receipt_id = ?, payment_method = ? {$append_sql}
                ");
                $stmt->execute([
                    $params->clientId, $uniqueId, $paymentRecord->student_id, $paymentRecord->department_id ?? null, 
                    $paymentRecord->class_id, $paymentRecord->category_id, $params->amount, $params->userId, 
                    $paymentRecord->academic_year, $paymentRecord->academic_term, 
                    $params->description ?? null, $currency, $receiptId, $params->payment_method
                ]);
                /* Update the user payment record */
                $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
                    last_payment_date = now(), last_payment_id = '{$uniqueId}' ".($paid_status ? ", paid_status='{$paid_status}'" : "")."
                    WHERE checkout_url = ? AND client_id = ? LIMIT 1
                ");
                $stmt->execute([$totalPayment, $outstandingBalance, $params->checkout_url, $params->clientId]);

                /* Record the user activity log */
                $this->userLogs("fees_payment", $params->checkout_url, null, "{$params->userData->name} received an amount of <strong>{$params->amount}</strong> as Payment for <strong>{$paymentRecord->category_name}</strong> from <strong>{$paymentRecord->student_details["student_name"]}</strong>. Outstanding Balance is <strong>{$outstandingBalance}</strong>", $params->userId);
                
                // additional data
                $additional["payment"] = $this->confirm_student_payment_record($params);
                $additional["uniqueId"] = $uniqueId;

                // set the student name
                $student_name = $paymentRecord->student_details["student_name"];

            } else {

                // generate a new payment_id
                $payment_id = random_string('alnum', 15);

                // get the student name
                $student = $this->pushQuery("name AS student_name", "users", "item_id = '{$params->student_id}' AND academic_year='{$this->academic_year}' AND academic_term='{$this->academic_term}' LIMIT 1");
                $student_name = !empty($student) ? $student[0]->student_name : "Unknown";

                // loop through the payment record
                foreach($paymentRecord as $record) {

                    // loop through the items which were paid for
                    if(isset($amount_paid[$record->category_id])) {

                        // get the total amount paid
                        $total_paid = $amount_paid[$record->category_id];
                        $total_balance = ($record->balance - $total_paid);
                        $totalPayment = ($record->amount_paid + $total_paid);

                        // set the paid status
                        $paid_status = ((round($totalPayment) === round($record->amount_due)) || (round($totalPayment) > round($record->amount_due))) ? 1 : 2;

                        // generate a unique id for the payment record
                        $uniqueId = random_string('alnum', 15);
                        $counter = $this->append_zeros(($this->itemsCount("fees_collection", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                        $receiptId = $this->iclient->client_preferences->labels->receipt_label.$counter;
                        $receiptId = strtoupper($receiptId);

                        // insert the new record into the database
                        $stmt = $this->db->prepare("INSERT INTO fees_collection
                            SET client_id = ?, item_id = ?, student_id = ?, payment_id = ?, department_id = ?, class_id = ?, 
                            category_id = ?, amount = ?, created_by = ?, academic_year = ?, academic_term = ?, 
                            description = ?, currency = ?, receipt_id = ?, payment_method = ? {$append_sql}
                        ");
                        $stmt->execute([
                            $params->clientId, $uniqueId, $record->student_id, $payment_id,
                            $record->department_id ?? null, $record->class_id, $record->category_id, 
                            $total_paid, $params->userId, $record->academic_year, $record->academic_term, 
                            $params->description ?? null, $currency, $receiptId, $params->payment_method
                        ]);

                        /* Update the user payment record */
                        $stmt = $this->db->prepare("UPDATE fees_payments SET amount_paid = ?, balance = ?, 
                            last_payment_date = now(), last_payment_id = '{$uniqueId}' ".($paid_status ? ", 
                            paid_status='{$paid_status}'" : "")." WHERE checkout_url = ? AND client_id = ? LIMIT 1
                        ");
                        $stmt->execute([($record->amount_paid + $total_paid), $total_balance, $record->checkout_url, $params->clientId]);

                        /* Record the user activity log */
                        $this->userLogs("fees_payment", $record->checkout_url, null, "{$params->userData->name} received an amount of <strong>{$total_paid}</strong> as Payment for <strong>{$record->category_name}</strong> from <strong>{$student_name}</strong>. Outstanding Balance is <strong>{$total_balance}</strong>", $params->userId);
                        
                        // set a new parameter for the checkout and category id
                        $params->checkout_url = $record->checkout_url;
                    }
                }

                $last_info = $this->confirm_student_payment_record($params);
                $additional["payment"] = $last_info[count($last_info)-1];
                $additional["uniqueId"] = $uniqueId;

            }

            /* Update the student credit balance */
            if(isset($creditBalance)) {
                // update the user data
                $this->db->query("UPDATE users SET account_balance = (account_balance + $creditBalance) WHERE item_id = '{$params->student_id}' AND client_id = '{$params->clientId}' LIMIT 1");
            }

            // Log the transaction information
            if(isset($params->transaction_id) && isset($params->reference_id)) {
                // Insert the transaction
                $this->db->query("INSERT INTO transaction_logs SET client_id = '{$params->clientId}',
                    transaction_id = '{$params->transaction_id}', endpoint = 'fees', reference_id = '{$params->reference_id}', amount='{$params->amount}'
                ");
            }

            // send the receipt via sms
            if(isset($preference->send_receipt) && isset($params->contact_number)){
                
                // if the contact number is not empty
                if(strlen($params->contact_number) > 9 && preg_match("/^[0-9+]+$/", $params->contact_number)) {
                    
                    // append the message
                    $message = "Hello {$student_name},\nFees Payment was successfully processed.\nAmount Paid: {$params->amount}\nBalance: {$outstandingBalance}\n";
                    
                    // calculate the message text count
                    $chars = strlen($message);
                    $message_count = ceil($chars / $this->sms_text_count);
                    
                    // get the sms balance
                    $balance = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$params->clientId}' LIMIT 1");
                    $balance = $balance[0]->sms_balance ?? 0;

                    // return error if the balance is less than the message to send
                    if($balance > $message_count) {

                        //open connection
                        $ch = curl_init();

                        // set the field parameters
                        $fields_string = [
                            "key" => $this->mnotify_key,
                            "recipient" => [$params->contact_number],
                            "sender" => !empty($this->iclient->sms_sender) ? $this->iclient->sms_sender : $this->sms_sender,
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
                        
                        // if the sms was successful
                        if(!empty($result)) {
                            // reduce the SMS balance
                            $this->db->query("UPDATE smsemail_balance SET sms_balance = (sms_balance - {$message_count}), sms_sent = (sms_sent + {$message_count}) WHERE client_id = '{$params->clientId}' LIMIT 1");
                        }
                    }

                }
            }

            // commit the statements
            $this->db->commit();

            // return the success message
            return [
                "data" => "Fee payment was successfully recorded.",
                "additional" => $additional ?? true
            ];

        } catch(PDOException $e) {
            // Role Back the statement
            $this->db->rollBack();

            // return an unexpected error notice
            return $this->unexpected_error;
        }
        
	}

    /**
     * Confirm MoMo / Card Payment
     * 
     * @return Array
     */
    public function momocard_payment(stdClass $params) {

        try {
            
            // check if the transaction id already exits
            $transaction = $this->pushQuery("id", "transaction_logs", "transaction_id='{$params->transaction_id}' LIMIT 1");
            if(!empty($transaction)) {
                return ["code" => 203, "data" => "Sorry! This transaction has already been processed."];
            }

            // create a new payment object
            $payObject = load_class("payment", "controllers");

            /** Validate the amount */
            if(!$params->amount) {
                return ["code" => 203, "data" => "Sorry! The amount cannot be empty."];
            }

            // set the parameters
            $data = (object) [
                "route" => "verify",
                "reference" => $params->reference_id
            ];

            // confirm the payment
            $payment_check = $payObject->get($data);
            
            // if payment status is true
            if(!empty($payment_check["data"]) && isset($payment_check["data"]->status) && ($payment_check["data"]->status === true)) {
                // 
                $params->payment_method = "MoMo_Card";

                // process the payment
                return $this->make_payment($params);

            } else {
                return ["code" => 203, "data" => "Sorry! An error was encountered while processing the request."];
            }

        } catch(PDOException $e) {
            // return an unexpected error notice
            return $this->unexpected_error;
        }

    }

    /**
     * Save Allowance
     * 
     * @param String $params->description
     * @param String $params->category_id
     * @param String $params->name
     * @param String $params->type
     * 
     * @return Array
     */
    public function savecategory(stdClass $params) {
                
        $found = false;
        if(isset($params->category_id) && !empty($params->category_id)) {
            $category = $this->pushQuery("*", "fees_category", "id='{$params->category_id}' AND client_id='{$params->clientId}'");
            if(empty($category)) {
                return ["code" => 203, "data" => "Sorry! An invalid category id was parsed."];
            }
            $found = true;
        }

        if(!$found) {
            $stmt = $this->db->prepare("INSERT INTO fees_category SET amount = ?, name = ?, description = ?, code = ?, client_id = ?");
            $stmt->execute([$params->amount ?? null, $params->name, $params->description ?? null, $params->code ?? null, $params->clientId]);
            // log the user activity
            $this->userLogs("fees_category", $this->lastRowId("fees_category"), null, "<strong>{$params->userData->name}</strong> added a new category with name: {$params->name}", $params->userId);
        } else {
            $stmt = $this->db->prepare("UPDATE fees_category SET amount = ?, name = ?, description = ?, code = ? WHERE id = ? AND client_id = ?");
            $stmt->execute([$params->amount ?? null, $params->name, $params->description ?? null, $params->code ?? null, $params->category_id, $params->clientId]);
            // log the user activity
            $this->userLogs("fees_category", $params->category_id, null, "<strong>{$params->userData->name}</strong> updated the category: {$params->name}", $params->userId);
        }

        # set the output to return when successful
        $return = ["code" => 200, "data" => "Request was successfully executed.", "refresh" => 800];
        
        # append to the response
        $return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}fees-category"];

        // return the output
        return $return;
        
    }

    /**
     * Fees Receipt
     * 
     * @return Array
     */
    public function receipt(stdClass $params) {

        // init variable
        $student_data = [];
        $studentIsset = (bool) isset($params->getObject->student_id) && !empty($params->getObject->student_id);

        // if the receipt id was parsed
        if(!empty($params->receipt_id)) {
            $student_data = $params->data[0] ?? [];
        }

        if($studentIsset) {
            $student_data = $params->data[0] ?? [];
        }

        // get the client data
        $amount = 0;
        $data = $params->data;
        $client = $this->client_data($params->clientId);
        $clientPrefs = $client->client_preferences;

        // get the client logo content
        if(!empty($client->client_logo)) {
            $type = pathinfo($client->client_logo, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($client->client_logo);
            $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
        }

        $isPDF = (bool) isset($params->isPDF);

        // append the data
        $receipt = '
        <link rel="stylesheet" href="'.$this->baseUrl.'assets/css/app.min.css">
        <link rel="stylesheet" href="'.$this->baseUrl.'assets/css/style.css">
        <div style="margin:auto auto; '.($isPDF ? '' : "max-width:950px;").'">
            <div class="row mb-3">
                <div class="text-dark bg-white col-md-12" style="padding:30px">
                    <div align="center">
                        '.(!empty($client->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\">" : "").'
                        <h2 style="color:#6777ef;font-family:helvetica;padding:0px;margin:0px;">'.strtoupper($client->client_name).'</h2>
                        <div>'.$client->client_address.'</div>
                        '.(!empty($client->client_email) ? "<div>{$client->client_email}</div>" : "").'
                    </div>
                    <div style="background-color: #2196F3 !important;margin-top:5px;border-bottom: 1px solid #dee2e6 !important;height:3px;" class="pb-1 mb-3"></div>
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table width="100%">
                                    <tr>
                                    <td width="50%">
                                        <div class="invoice-title">
                                            <h2 style="margin-top:5px;margin-bottom:5px;">Official Receipt</h2>
                                            <span style="font-size:12px;"><strong>Date & Time:</strong> '.date("d-m-Y h:iA").'</span>
                                        </div>
                                    </td>
                                    <td align="right">
                                        '.(!empty($student_data) ? 
                                            "<strong>Academic Year & Term:</strong><br>{$student_data->academic_year} :: {$student_data->academic_term}<br>" : 
                                            "<strong>Academic Year & Term:</strong><br>{$client->client_preferences->academics->academic_year} :: {$client->client_preferences->academics->academic_term}<br>"  
                                            ).'
                                        '.(count($data) == 1 && !empty($student_data->receipt_id) ? "Receipt ID: <strong>{$student_data->receipt_id}</strong><br>" : null).'
                                    </td>
                                    </tr>
                                    </table>
                                    <hr class="pb-0 mb-2 mt-0">
                                    '.(!empty($student_data) && $isPDF ?
                                        '<table border="0" width="100%">
                                            <tr>
                                                <td width="50%">
                                                    <address>
                                                        <strong>To:</strong><br>
                                                        '.($student_data->student_info->name ?? null).'<br>
                                                        '.($student_data->student_info->unique_id ?? null).'<br>
                                                        '.($student_data->class_name ?? null).'<br>
                                                        '.($student_data->department_name ?? null).'<br>
                                                    </address>
                                                </td>
                                                <td align="right">
                                                '.(!empty($student_data->student_info->guardian_id) ? 
                                                    '<address>
                                                    <strong>Billed To:</strong><br>
                                                    '.(!empty($student_data->student_info->guardian_id[0]->fullname) ? $student_data->student_info->guardian_id[0]->fullname : null).'
                                                    '.(!empty($student_data->student_info->guardian_id[0]->address) ? "<br>" . $student_data->student_info->guardian_id[0]->address : null).'
                                                    '.(!empty($student_data->student_info->guardian_id[0]->contact) ? "<br>" . $student_data->student_info->guardian_id[0]->contact : null).'
                                                    '.(!empty($student_data->student_info->guardian_id[0]->email) ? "<br>" . $student_data->student_info->guardian_id[0]->email : null).'
                                                    </address>' : ''
                                                ).'
                                                </td>
                                            </tr>                                        
                                        </table>'
                                        : null
                                    ).'
                                    '.(!empty($student_data) && !$isPDF ?
                                    '<div class="row">
                                        <div class="col-md-6" '.($isPDF ? "style='text-align:left'" : null).'>
                                            <address>
                                                <strong>To:</strong><br>
                                                '.($student_data->student_info->name ?? null).'<br>
                                                '.($student_data->student_info->unique_id ?? null).'<br>
                                                '.($student_data->class_name ?? null).'<br>
                                                '.($student_data->department_name ?? null).'<br>
                                            </address>
                                        </div>
                                        '.(!empty($student_data->student_info->guardian_id) ?
                                        '<div class="col-md-6 text-md-right" '.($isPDF ? "style='text-align:right'" : null).'>
                                            <address>
                                            <strong>Billed To:</strong><br>
                                            '.(!empty($student_data->student_info->guardian_id[0]->fullname) ? $student_data->student_info->guardian_id[0]->fullname : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->address) ? "<br>" . $student_data->student_info->guardian_id[0]->address : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->contact) ? "<br>" . $student_data->student_info->guardian_id[0]->contact : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->email) ? "<br>" . $student_data->student_info->guardian_id[0]->email : null).'
                                            </address>
                                        </div>': '').'
                                    </div>': '').'
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table width="100%" '.($isPDF ? "cellpadding='5px'" : null).' class="table table-striped table-hover table-md" style="border: 1px solid #dee2e6; font-size:13px;">
                                            <tbody>
                                            <tr align="left">
                                                <th '.(!$isPDF ? 'style="width: 40px;"' : null).'>#</th>
                                                '.(empty($student_data) ? '<th>Name</th>' : '').'
                                                <th>Item</th>
                                                <th>Payment Method</th>
                                                <th>Description</th>
                                                <th>Record Date</th>
                                                <th align="right">Amount</th>
                                            </tr>';
                                            if(!empty($data)) {
                                                foreach($data as $key => $record) {
                                                    $amount += $record->amount;
                                                    $receipt .='<tr>
                                                        <td width="6%" '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>'.($key+1).'</td>
                                                        '.(empty($student_data) ? '<td '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>
                                                            '.$record->student_info->name.'
                                                        </td>' : '').'
                                                        <td width="15%" '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>'.$record->category_name.'</td>
                                                        <td width="15%" '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>
                                                            <strong>'.$record->payment_method.'</strong>
                                                            '.(
                                                                $record->payment_method === "Cheque" ? 
                                                                "<br><strong>".explode("::", $record->cheque_bank)[0]."</strong>
                                                                ".(!empty($record->cheque_number) ? "<br><strong>#{$record->cheque_number}</strong>" : null)."" : ""    
                                                            ).'
                                                        </td>
                                                        <td '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>'.($record->description ? $record->description : null).'</td>
                                                        <td width="15%" '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).'>'.$record->recorded_date.'</td>
                                                        <td '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).' width="10%" align="right"><strong>'.number_format($record->amount, 2).'</strong></td>
                                                    </tr>';
                                                }
                                            } else {
                                                $receipt .= '<tr><td '.($isPDF ? 'style="border: 1px solid #dee2e6;"' : null).' align="center" colspan="'.(empty($receipt_id) ? 7 : 6).'">No Record Found</td></tr>';
                                            }
                                        $receipt .= '
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8"></div>
                                        <div class="col-lg-4" align="right">
                                            <hr style="margin-bottom:20px">
                                            <div class="invoice-detail-item">
                                                <div class="invoice-detail-name">Total</div>
                                                <div class="invoice-detail-value invoice-detail-value-lg"><strong>'.(number_format($amount, 2)).'</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom" style="border: 2px solid #2196F3; margin-top:10px"></div>
                    <div align="center" style="font-size:12px;padding-top:10px;">
                        <strong>Location: </strong>'.$client->client_location.' | 
                        <strong>Contact:</strong> '.$client->client_contact.'
                        '.(!empty($client->client_secondary_contact) ? " | {$client->client_secondary_contact}" : "").'
                        | <strong>Address: </strong> '.(strip_tags($client->client_address)).'
                    </div>
                </div>
            </div>
        </div>';

        // append this section if download element was not parsed
        if(!isset($params->download)) {
            $receipt .= "<script>
                window.onload = (evt) => { window.print(); }
                window.onafterprint = (evt) => { window.close(); }
            </script>";
        }

        return $receipt;
    }

}