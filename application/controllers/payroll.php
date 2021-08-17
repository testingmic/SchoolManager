<?php 

class Payroll extends Myschoolgh {

    private $iclient = [];

    public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;

        // set the colors to use for the loading of pages
        $this->color_set = [
            "#007bff", "#6610f2", "#6f42c1", "#e83e8c", "#dc3545", "#fd7e14", 
            "#ffc107", "#28a745", "#20c997", "#17a2b8", "#6c757d", "#343a40", 
            "#007bff", "#6c757d", "#28a745", "#17a2b8", "#ffc107", "#dc3545"
        ];

	}

    /**
     * List Payrolls
     * 
     * @return Array
     */
    public function paysliplist(stdClass $params) {

        
        global $accessObject;

        if(isset($params->employee_id)) {
            $params->employee_id = $params->employee_id;
        } else {
            if(!$accessObject->hasAccess("generate", "payslip") && !isset($params->payslip_id)) {
                $params->employee_id = $params->userId ?? $this->session->userId;
            }
        }

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        
        $params->query .= (isset($params->year_id)) ? " AND a.year_id='{$params->year_id}'" : null;
        $params->query .= (isset($params->month_id)) ? " AND a.month_id='{$params->month_id}'" : null;
        $params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->employee_id) && !empty($params->employee_id)) ? " AND a.employee_id='{$params->employee_id}'" : null;
        $params->query .= (isset($params->payslip_id) && !empty($params->payslip_id)) ? " AND a.item_id='{$params->payslip_id}'" : null;

        try {

            $payslipDetails = (bool) (isset($params->payslip_detail) && $params->payslip_detail);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(c.item_id,'|',c.name,'|',c.phone_number,'|',c.email,'|',c.image,'|',c.unique_id,'|',c.last_seen,'|',c.online,'|',c.user_type) FROM users c WHERE c.item_id = a.employee_id LIMIT 1) AS employee_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.unique_id,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM payslips a
                WHERE {$params->query} AND a.deleted = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["employee_info", "created_by_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","unique_id","last_seen","online","user_type"]);
                }

                if($payslipDetails) {
                    $detail = $this->pushQuery("a.*, at.name AS allowance_type", 
                        "payslips_details a LEFT JOIN payslips_allowance_types at ON at.id = a.allowance_id", 
                        "a.payslip_id='{$result->id}' AND a.employee_id='{$result->employee_id}'");
                    $result->payslip_details = [];
                    foreach($detail as $each) {
                        $result->payslip_details[$each->detail_type][] = $each;
                    }
                    $result->client_details = $this->pushQuery("a.*", "clients_accounts a", "a.client_id = '{$result->client_id}' AND a.client_status='1' LIMIT 1")[0];
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
     * Payment Details
     * 
     * Save the payment allowances and deductions of the employee
     * 
     * @return Array
     */
    public function paymentdetails(stdClass $params) {
        
        // global variable
        global $usersClass, $accessObject;

        if(!$accessObject->hasAccess("modify_payroll", "payslip")) {
            return ["code" => 203, "data" => "Sorry! You do not have the permissions to modify the details of this employee's salary information."];
        }

        // confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $params->employee_id, "user_payroll" => true, "minified" => "minified_content"];
		$the_user = $usersClass->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}
        $the_user = $the_user[0];

        // initialize the allowance calculator
        $allowances = [];
        $t_allowances = 0;
        $t_deductions = 0;
        
        // process the employee allowances
        if(isset($params->allowances) && !empty($params->allowances)) {
            // loop through the allowance list
            foreach($params->allowances as $key => $value) {
                // check if the key is not null
                if($key !== "null") {
                    // set the value
                    $allowances[] = [
                        'allowance_id' => (int) $key,
                        'allowance_amount' => $value,
                        'allowance_type' => 'Allowance'
                    ];
                    $t_allowances += $value;
                }
            }
        }

        // process the employee allowances
        if(isset($params->deductions) && !empty($params->deductions)) {
            // loop through the allowance list
            foreach($params->deductions as $key => $value) {
                // check if the key is not null
                if($key !== "null") {
                    // set the value
                    $allowances[] = [
                        'allowance_id' => (int) $key,
                        'allowance_amount' => $value,
                        'allowance_type' => 'Deduction'
                    ];
                    $t_deductions += $value;
                }
            }
        }

        $data = "Employee Bank Details was successfully updated";

        // set the employee allowances
        $params->_allowances = $allowances;

        /** if the gross salary is set */
        if(isset($params->basic_salary)) {

            /* Delete the employee allowance records and insert a new data */
            $stmt = $this->db->prepare("DELETE FROM payslips_employees_allowances WHERE employee_id = ? AND client_id = ?");
            $stmt->execute([$params->employee_id, $params->clientId]);

            /* Loop through the list of user allowances */
            foreach($params->_allowances as $eachAllowance) {
                // run this section if the request is allowance
                $stmt = $this->db->prepare("
                    INSERT INTO payslips_employees_allowances SET 
                    allowance_id = ?, employee_id = ?, amount = ?, type = ?, client_id = ?
                ");
                $stmt->execute([$eachAllowance['allowance_id'],$params->employee_id,
                    $eachAllowance['allowance_amount'], $eachAllowance['allowance_type'], $params->clientId
                ]);
            }

            /** Simple calculations */
            $net_salary = $params->basic_salary + $t_allowances - $t_deductions;
            $gross_salary = $params->basic_salary + $t_allowances;
            $net_allowance = $t_allowances - $t_deductions;
            
            /** Insert/Update the basic salary information */
            if(empty($the_user->basic_salary)) {

                /** Insert a new record */
                $stmt = $this->db->prepare("INSERT INTO payslips_employees_payroll SET 
                client_id = ?, employee_id = ?, basic_salary = ?, gross_salary = ?,
                allowances = ?, deductions = ?, net_allowance = ?, net_salary = ?");
                
                $stmt->execute([$params->clientId, $params->employee_id, $params->basic_salary, 
                    $gross_salary, $t_allowances, $t_deductions, $net_allowance, $net_salary]);
                
                // log the user activity
                $this->userLogs("salary_allowances", $params->employee_id, null, "<strong>{$params->userData->name}</strong> inserted the Salary Allowances of: <strong>{$the_user->name}</strong>", $params->userId);

            } else {

                /** update existing record */
                $stmt = $this->db->prepare("UPDATE payslips_employees_payroll SET 
                basic_salary = ?,  gross_salary = ?, allowances = ?, deductions = ?, net_allowance = ?, net_salary = ?
                WHERE client_id = ? AND employee_id = ? LIMIT 1");

                $stmt->execute([$params->basic_salary, $gross_salary, $t_allowances, $t_deductions, $net_allowance, 
                    $net_salary, $params->clientId, $params->employee_id]);

                /** Data to save */
                $log = "
                <p class='mb-0 pb-0'><strong>Basic Salary:</strong> {$the_user->basic_salary} => {$params->basic_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Allowances:</strong> {$the_user->allowances} => {$t_allowances}</p>
                <p class='mb-0 pb-0'><strong>Gross Salary:</strong> {$the_user->gross_salary} => {$gross_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Deductions:</strong> {$the_user->deductions} => {$t_deductions}</p>
                <p class='mb-0 pb-0'><strong>Net Allowances:</strong> {$the_user->net_allowance} => {$net_allowance}</p>
                <p class='mb-0 pb-0'><strong>Net Salary:</strong> {$the_user->net_salary} => {$net_salary}</p>";

                // log the user activity
                $this->userLogs("salary_allowances", $params->employee_id, $log, "<strong>{$params->userData->name}</strong> updated the Salary Allowances of: <strong>{$the_user->name}</strong>", $params->userId);
            }
            $data = "Employee Allowances was successfully updated";
        }

        else if(isset($params->account_name)) {

            // set the bank name
            $params->bank_name = $params->bank_name ?? null;
            
            /** Insert/Update the basic salary information */
            if(empty($the_user->basic_salary)) {
                /** Insert a new record */
                $stmt = $this->db->prepare("INSERT INTO payslips_employees_payroll SET 
                client_id = ?, employee_id = ?, account_name = ?, account_number = ?,
                bank_name = ?, bank_branch = ?, ssnit_number = ?, tin_number = ?");
                
                $stmt->execute([$params->clientId, $params->employee_id, $params->account_name, 
                    $params->account_number, $params->bank_name, $params->bank_branch, $params->ssnit_number, $params->tin_number]);

                // log the user activity
                $this->userLogs("bank_details", $params->employee_id, null, "<strong>{$params->userData->name}</strong> inserted the Bank Details of: <strong>{$the_user->name}</strong>", $params->userId);
                
            } else {
                /** Insert a new record */
                $stmt = $this->db->prepare("UPDATE  payslips_employees_payroll SET 
                account_name = ?, account_number = ?, bank_name = ?, bank_branch = ?, ssnit_number = ?, tin_number = ?
                WHERE client_id = ? AND employee_id = ? LIMIT 1");
                
                $stmt->execute([$params->account_name,  $params->account_number, $params->bank_name, 
                    $params->bank_branch, $params->ssnit_number, $params->tin_number, $params->clientId, $params->employee_id]);
                
                /** Data to save */
                $log = "
                <p class='mb-0 pb-0'><strong>Account Name:</strong> {$the_user->account_name} => {$params->account_name}</p>
                <p class='mb-0 pb-0'><strong>Account Number:</strong> {$the_user->account_number} => {$params->account_number}</p>
                <p class='mb-0 pb-0'><strong>Bank Name:</strong> {$the_user->bank_name} => {$params->bank_name}</p>
                <p class='mb-0 pb-0'><strong>Branch:</strong> {$the_user->bank_branch} => {$params->bank_branch}</p>
                <p class='mb-0 pb-0'><strong>SSNIT No.:</strong> {$the_user->ssnit_number} => {$params->ssnit_number}</p>
                <p class='mb-0 pb-0'><strong>Tin No.:</strong> {$the_user->tin_number} => {$params->tin_number}</p>";
            
                // log the user activity
                $this->userLogs("bank_details", $params->employee_id, $log, "<strong>{$params->userData->name}</strong> updated the Bank Details of: <strong>{$the_user->name}</strong>", $params->userId);

            }
        }

        return [
            "data" => $data
        ];
    }

    /**
     * Load the Payslip of an Employee for a specific period
     * 
     * @param String $params->employee_id
     * @param String $params->month_id
     * @param String $params->year_id
     * 
     * @return Array
     */
    public function payslipdetails(stdClass $params) {
        
        // global variable
        global $usersClass, $accessObject;

        if(!$accessObject->hasAccess("view", "payslip")) {
            return ["code" => 203, "data" => "Sorry! You do not have the permissions to view the details of this payslip."];
        }

        if((strlen($params->year_id) !== 4)  || ($params->year_id === "null")) {
            return ["code" => 203, "data" => "Please select a valid year to load record."];
        }

        // return error
        if(strlen($params->month_id) < 3 || ($params->month_id === "null")) {
            return ["code" => 203, "data" => "Please select a valid month to load record."];
        }

        // set the employee_id
        $params->employee_id = empty($params->employee_id) ? $params->userId : $params->employee_id;

        // confirm that the user_id does not already exist
		$i_params = (object) [
            "limit" => 1, "user_id" => $params->employee_id, 
            "user_payroll" => true, "minified" => "minified_content"
        ];
		$the_user = $usersClass->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}
        $data = $the_user[0];

        // fetch the allowances of the employee
        $allowances_list = "";
        $deductions_list = "";
        $employeeAllowances = $data->_allowances;
        $employeeDeductions = $data->_deductions;

        // load the allowance for the specified month and year
        $employeePayslip = $this->pushQuery("*", "payslips", "payslip_month='{$params->month_id}' AND payslip_year='{$params->year_id}' AND client_id='{$params->clientId}' AND deleted='0' AND employee_id='{$params->employee_id}'");
        if(!empty($employeePayslip)) {
            $employeeAllowances = $this->pushQuery("*", "payslips_details", "detail_type='Allowance' AND payslip_month='{$params->month_id}' AND payslip_year='{$params->year_id}' AND client_id='{$params->clientId}' AND employee_id='{$params->employee_id}'");
            $employeeDeductions = $this->pushQuery("*", "payslips_details", "detail_type='Deduction' AND payslip_month='{$params->month_id}' AND payslip_year='{$params->year_id}' AND client_id='{$params->clientId}' AND employee_id='{$params->employee_id}'");
        }

        // fetch all allowances
        $allowances_types = $this->pushQuery('*', "payslips_allowance_types", "type='Allowance' AND status='1' AND client_id='{$params->clientId}'");
        $deductions_types = $this->pushQuery('*', "payslips_allowance_types", "type='Deduction' AND status='1' AND client_id='{$params->clientId}'");

        // initials
        $generated = 0;
		$totalAllowance = 0;
		$totalDeduction = 0;

        // count the number of rows found
        if(!empty($employeeAllowances)) {
            $ii = 0;
            // loop through the list of allowances
            foreach($employeeAllowances as $eachAllowance) {
                //: Button control
				$delButton = '';
				if($ii == 0) {
					$delButton = "<button class=\"btn btn-outline-info add-allowance\"><i class=\"fa fa-plus\"></i></button>";
				}
                // Increment 
                $ii++;
                $totalAllowance += $eachAllowance->amount;
                // append to the list
                $allowances_list .= '
                <div class="initial mb-2" data-row="'.$ii.'">
                    <div class="row">
                        <div class="col-lg-6">
                            <select data-width="100%" name="allowance[]" id="allowance_'.$ii.'" class="form-control selectpicker">
                                <option value="null">Please Select</option>';
                                foreach($allowances_types as $each) {
                                    $allowances_list .= "<option data-default_value='{$each->default_amount}' ".(($eachAllowance->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                            $allowances_list .= '
                            </select>
                        </div>
                        <div class="col-lg-5 mb-2 col-md-5">
                            <input value="'.$eachAllowance->amount.'" min="0" max="20000" placeholder="Amount" class="form-control" type="number" name="allowance_amount[]" id="allowance_amount_'.$ii.'">
                        </div>';
                        $allowances_list .= $delButton;
                        if($ii > 1) {
                            $allowances_list .= '
                            <div class="text-center">
                                <button class="cursor btn btn-outline-danger" onclick="return removeRow(\'allowance\',\''.$ii.'\');"><i class="fa fa-trash"></i></button>
                            </div>';
                        }
                $allowances_list .= '</div></div>';
            }
        } else {
            $allowances_list = '
            <div class="initial mb-2" data-row="1">
                <div class="row">
                    <div class="col-lg-7 mb-2 col-md-7">
                        <select data-width="100%" name="allowance" id="allowance_1" class="form-control selectpicker">
                            <option value="null">Please Select</option>';
                            foreach($allowances_types as $each) {
                                $allowances_list .= "<option data-default_value='{$each->default_amount}' value=\"{$each->id}\">{$each->name}</option>";
                            }
                            $allowances_list .= '
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="number" name="allowance_amount_1" id="allowance_amount_1">
                    </div>
                </div>
            </div>';
        }

        // count the number of rows found
        if(!empty($employeeDeductions)) {
            $ii = 0;
            // loop through the list of allowances
            foreach($employeeDeductions as $eachDeduction) {
                //: Button control
				$delButton = '';
				if($ii == 0) {
					$delButton = "<button class=\"btn btn-outline-info add-deductions\"><i class=\"fa fa-plus\"></i></button>";
				}
                // Increment 
                $ii++;
                $totalDeduction += $eachDeduction->amount;
                // append to the list
                $deductions_list .= '
                <div class="initial mb-2" data-row="'.$ii.'">
                    <div class="row">
                        <div class="col-lg-6">
                            <select data-width="100%" name="deductions[]" id="deductions_'.$ii.'" class="form-control selectpicker">
                                <option value="null">Please Select</option>';
                                foreach($deductions_types as $each) {
                                    $deductions_list .= "<option data-default_value='{$each->default_amount}' ".(($eachDeduction->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                            $deductions_list .= '
                            </select>
                        </div>
                        <div class="col-lg-5 mb-2">
                            <input value="'.$eachDeduction->amount.'" min="0" max="20000" placeholder="Amount" class="form-control" type="number" name="deductions_amount[]" id="deductions_amount_'.$ii.'">
                        </div>';
                        $deductions_list .= $delButton;
                        if($ii > 1) {
                            $deductions_list .= '
                            <div class="text-center">
                                <button class="cursor btn btn-outline-danger" onclick="return removeRow(\'deductions\',\''.$ii.'\');"><i class="fa fa-trash"></i></button>
                            </div>';
                        }
                $deductions_list .= '</div></div>';
            }
        } else {
            $deductions_list = '
            <div class="initial mb-2" data-row="1">
                <div class="row">
                    <div class="col-lg-7 mb-2 col-md-7">
                        <select data-width="100%" name="deductions" id="deductions_1" class="form-control selectpicker">
                            <option value="null">Please Select</option>';
                            foreach($deductions_types as $each) {
                                $deductions_list .= "<option data-default_value='{$each->default_amount}' value=\"{$each->id}\">{$each->name}</option>";
                            }
                            $deductions_list .= '
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="number" name="deductions_amount_1" id="deductions_amount_1">
                    </div>
                </div>
            </div>';
        }

        //: if the payslip is empty then query the employee information for basic salary
		if(empty($employeePayslip)) {

			$employeePayslip = [];

			//: Assign variables
			$employeePayslip['basic_salary'] = $data->basic_salary;
			$employeePayslip['total_allowance'] = $totalAllowance;
			$employeePayslip['total_deductions'] = $totalDeduction;
			$employeePayslip['total_incentives'] = 0.00;
			$employeePayslip['total_takehome'] = (($employeePayslip['basic_salary'] + $employeePayslip['total_allowance'] + $employeePayslip['total_incentives']) - $employeePayslip['total_deductions']);

			$employeePayslip['payment_mode'] = 'null';
			$employeePayslip['status'] = 0;
			
			$note = "
                <div class=\"text-primary mb-3 text-center\">
                    You are about to generate a Payslip for <strong>{$params->month_id} {$params->year_id}</strong>.
                </div>
                <div class='row justify-content-between'>
                    <div><button onclick='return cancelPayslip()' type=\"reset\" class=\"btn btn-outline-danger\"><i class='fa fa-exclamation-circle'></i>  Cancel</a></div>
                    <div><button onclick='return generate_payslip()' data-action=\"generate\" type=\"submit\" class=\"btn btn-outline-success\"><i class='fa fa-save'></i> Generate Payslip</button></div>
                </div>";
		} else {
			$employeePayslip = $employeePayslip[0];

			if($employeePayslip->status == 1) {
				$note = "<div class=\"text-success mb-3 text-center\">
						This Payslip has already been redeemed.</div>
						<div class=\"text-center\">
						<a href=\"{$this->baseUrl}download/payslip?pay_id={$employeePayslip->item_id}&dw=true\" target=\"_blank\" class=\"btn btn-outline-danger\"><i class='fa fa-file-pdf-o'></i> Download</a> &nbsp; 
						<a href=\"{$this->baseUrl}download/payslip?pay_id={$employeePayslip->item_id}&dw=false\" target=\"_blank\" class=\"btn btn-outline-primary\"><i class='fa fa-print'></i>  Print</a></div>
				";
			} else {
				$note = "<div class=\"text-danger mb-3 text-center\">
						This paylip was generated on <strong>{$employeePayslip->date_log}</strong> awaiting redemption. Any updates made will replace the current record.
						</div>
						<div class='row justify-content-between'>
							<div><button onclick='return cancelPayslip()' type=\"reset\" class=\"btn btn-outline-danger\"><i class='fa fa-exclamation-circle'></i>  Cancel</a></div>
							<div><button onclick='return generate_payslip()' data-action=\"update\" type=\"submit\" class=\"btn btn-outline-success\"><i class='fa fa-save'></i> Update Payslip</button></div>
						</div>";
			}
        }

        return [
            "code" => 200,
            "data" => [
                'payslip_data' => empty($employeePayslip) ? [] : $employeePayslip,
                'allowance_data' => $allowances_list,
                'deductions_data' => $deductions_list,
                'note' => $note,
                'generated' => $generated
            ]
		];  

    }

    /**
     * Generate the PaySlip and Save the Record
     * 
     * @param String $params->employee_id
     * @param String $params->month_id
     * @param String $params->year_id
     * 
     * @return Array
     */
    public function generatepayslip(stdClass $params) {

        // global variable
        global $usersClass, $accessObject, $noticeClass;

        if(!$accessObject->hasAccess("generate", "payslip")) {
            return ["code" => 203, "data" => "Sorry! You do not have the permissions to generate a payslip."];
        }

        if((strlen($params->year_id) !== 4)  || ($params->year_id === "null")) {
            return ["code" => 203, "data" => "Please select a valid year to load record."];
        }

        // return error
        if(strlen($params->month_id) < 3 || ($params->month_id === "null")) {
            return ["code" => 203, "data" => "Please select a valid month to load record."];
        }

        // confirm that the user_id does not already exist
		$i_params = (object) [
            "limit" => 1, "user_id" => $params->employee_id, 
            "user_payroll" => true, "minified" => "minified_content"
        ];
		$the_user = $usersClass->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}
        $data = $the_user[0];


        // inits for the allowances
        $allowances = [];
        $t_allowances = 0;
        $t_deductions = 0;
        $params->basic_salary = isset($params->basic_salary) ? (int) $params->basic_salary : $data->basic_salary;

        try {

            // Begin the transaction
            $this->db->beginTransaction();
        
            // process the employee allowances
            if(isset($params->allowances) && !empty($params->allowances)) {
                // loop through the allowance list
                foreach($params->allowances as $key => $value) {
                    // check if the key is not null
                    if($key !== "null") {
                        // set the value
                        $allowances[] = [
                            'allowance_id' => (int) $key,
                            'allowance_amount' => $value,
                            'allowance_type' => 'Allowance'
                        ];
                        $t_allowances += $value;
                    }
                }
            }

            // process the employee allowances
            if(isset($params->deductions) && !empty($params->deductions)) {
                // loop through the allowance list
                foreach($params->deductions as $key => $value) {
                    // check if the key is not null
                    if($key !== "null") {
                        // set the value
                        $allowances[] = [
                            'allowance_id' => (int) $key,
                            'allowance_amount' => $value,
                            'allowance_type' => 'Deduction'
                        ];
                        $t_deductions += $value;
                    }
                }
            }

            // set the employee allowances
            $params->_allowances = $allowances;

            /** Simple calculations */
            $net_salary = $params->basic_salary + $t_allowances - $t_deductions;
            $gross_salary = $params->basic_salary + $t_allowances;

            // load the allowance for the specified month and year
            $employeePayslip = $this->pushQuery("*", "payslips", "payslip_month='{$params->month_id}' AND payslip_year='{$params->year_id}' AND client_id='{$params->clientId}' AND deleted='0' AND employee_id='{$params->employee_id}' LIMIT 1");
            $payslip_id = !empty($employeePayslip) ? $employeePayslip[0]->id : null;
            
            /** If there is already a record */
            if(empty($payslip_id)) {
                
                /** Create new record id */
                $item_id = random_string("alnum", 32);

                /** Insert the Payslip Record */
                $stmt = $this->db->prepare("INSERT INTO payslips SET item_id = ?, client_id =?, employee_id=?, basic_salary=?, 
                    total_allowance =?, total_deductions=?, net_salary=?, payslip_month = ?, payslip_month_id=?, 
                    payslip_year=?, payment_mode =?, comments =?, gross_salary = ?, created_by = ?
                ");
                $stmt->execute([
                    $item_id, $params->clientId, $params->employee_id, $params->basic_salary, 
                    $t_allowances, $t_deductions, $net_salary, $params->month_id,
                    date("Y-m-t", strtotime("{$params->month_id} {$params->year_id}")),
                    $params->year_id, $params->payment_mode ?? null,
                    $params->comments ?? null, $gross_salary, $params->userId
                ]);
                // get the last row generated
                $payslip_id = $this->lastRowId("payslips WHERE client_id='{$params->clientId}'");

                // log the data in the statement account
                $check = $this->pushQuery("item_id, balance", "accounts", "client_id='{$params->clientId}' AND status='1' AND default_account='1' LIMIT 1");
                
                // if the account is not empty
                if(!empty($check)) {

                    // get the account unique id
                    $account_id = $check[0]->item_id;
                    $payment_mode = isset($params->payment_mode) ? strtolower($params->payment_mode) : "cheque";
                    
                    // log the transaction record
                    $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                        item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                        reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                        description = ?, academic_year = ?, academic_term = ?, balance = ?
                    ");
                    $stmt->execute([
                        $item_id, $params->clientId, $account_id, "payroll", 'Expense', null, $net_salary, $params->userId, 
                        date("Y-m-d"), $payment_mode, "Auto Generation of PaySlip - {$params->month_id} {$params->year_id} for <strong>{$data->name}</strong>",
                        $this->academic_year ?? null, $this->academic_term ?? null, ($check[0]->balance - $net_salary)
                    ]);

                    // add up to the expense
                    $this->db->query("UPDATE accounts SET total_debit = (total_debit + {$net_salary}), balance = (balance - {$net_salary}) WHERE item_id = '{$account_id}' LIMIT 1");

                }

                // log the user activity
                $this->userLogs("payslip", $params->employee_id, null, "<strong>{$params->userData->name}</strong> generated a payslip for: <strong>{$data->name}</strong> for the month: <strong>{$params->month_id} {$params->year_id}</strong>", $params->userId);

                // form the notification parameters
                $item_param = (object) [
                    '_item_id' => $item_id,
                    'user_id' => $params->employee_id,
                    'subject' => "PaySlip for: {$params->month_id} {$params->year_id}",
                    'username' => $data->name,
                    'remote' => false, 
                    'message' => "Your Payslip for <strong>{$params->month_id} {$params->year_id}</strong> has been generated successfully. Visit the payslips page to view it.",
                    'notice_type' => 12,
                    'userId' => $params->userId,
                    'clientId' => $params->clientId,
                    'initiated_by' => 'system'
                ];

                // add a new notification
                $noticeClass->add($item_param);

            } else {
                /** Payslip details */
                $payslip = $employeePayslip[0];

                /* Delete the employee allowance records and insert a new data */
                $stmt = $this->db->prepare("DELETE FROM payslips_details WHERE employee_id = ? AND client_id = ? AND payslip_month = ? AND payslip_year = ? LIMIT 20");
                $stmt->execute([$params->employee_id, $params->clientId, $params->month_id, $params->year_id]);

                /** Insert the Payslip Record */
                $stmt = $this->db->prepare("UPDATE payslips SET basic_salary=?, 
                    total_allowance =?, total_deductions=?, net_salary=?, payment_mode =?, 
                    comments =?, gross_salary = ?, payslip_month_id=? WHERE
                    client_id =? AND employee_id=? AND payslip_month = ? AND payslip_year=?
                ");
                $stmt->execute([$params->basic_salary, 
                    $t_allowances, $t_deductions, $net_salary, $params->payment_mode ?? null, 
                    $params->comments ?? null, $gross_salary, date("Y-m-t", strtotime("{$params->month_id} {$params->year_id}")),
                    $params->clientId, $params->employee_id, $params->month_id, $params->year_id
                ]);

                // log the data in the statement account
                $check = $this->pushQuery("item_id, balance", "accounts", "client_id='{$params->clientId}' AND status='1' AND default_account='1' LIMIT 1");
                
                // run this query if the net salary has changed.
                // This will affect the balance in the database. It has changed then reverse the previous
                // transaction and log a new one.
                if(round($payslip->net_salary) !== round($net_salary)) {

                    // if the account is not empty
                    if(!empty($check)) {

                        // transaction record
                        $check_2 = $this->pushQuery("payment_medium, account_id, record_date, item_id, amount, 
                            balance, academic_year, academic_term, description", "accounts_transaction", 
                            "item_id='{$payslip->item_id}' LIMIT 1");
                        
                        // if there is an existing payslip record
                        if(!empty($check_2)) {
                            
                            // log the transaction record
                            $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                                item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                                reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                                description = ?, academic_year = ?, academic_term = ?, balance = ?, state='Approved'
                            ");
                            $stmt->execute([
                                $payslip->item_id, $params->clientId, $check_2[0]->account_id, "payroll", "Deposit", null, $check_2[0]->amount, 
                                $params->userId, $check_2[0]->record_date, $check_2[0]->payment_medium, 
                                "{$check_2[0]->description}: Reversed due to Change of Amount.",
                                $check_2[0]->academic_year, $check_2[0]->academic_term, ($check[0]->balance + $check_2[0]->amount)
                            ]);

                            // add up to the income
                            $this->db->query("UPDATE accounts SET total_credit = (total_credit + {$check_2[0]->amount}), balance = (balance + {$check_2[0]->amount}) WHERE item_id = '{$check_2[0]->account_id}' LIMIT 1");
                        }

                        // get the account unique id
                        $account_id = $check[0]->item_id;
                        $payment_mode = isset($params->payment_mode) ? strtolower($params->payment_mode) : "cheque";
                        
                        // log the transaction record
                        $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                            item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                            reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                            description = ?, academic_year = ?, academic_term = ?, balance = ?
                        ");
                        $stmt->execute([
                            $payslip->item_id, $params->clientId, $account_id, "payroll", "Expense", null, $net_salary, $params->userId, 
                            date("Y-m-d"), $payment_mode, "Auto Generation of PaySlip - {$params->month_id} {$params->year_id} for <strong>{$data->name}</strong>",
                            $this->academic_year ?? null, $this->academic_term ?? null, ($check[0]->balance - $net_salary)
                        ]);

                        // add up to the expense
                        $this->db->query("UPDATE accounts SET total_debit = (total_debit + {$net_salary}), balance = (balance - {$net_salary}) WHERE item_id = '{$account_id}' LIMIT 1");

                    }
                }

                /** Data to save */
                $log = "
                <p class='mb-0 pb-0'><strong>Basic Salary:</strong> {$payslip->basic_salary} => {$params->basic_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Allowances:</strong> {$payslip->total_allowance} => {$t_allowances}</p>
                <p class='mb-0 pb-0'><strong>Gross Salary:</strong> {$payslip->gross_salary} => {$gross_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Deductions:</strong> {$payslip->total_deductions} => {$t_deductions}</p>
                <p class='mb-0 pb-0'><strong>Net Salary:</strong> {$payslip->net_salary} => {$net_salary}</p>";

                // log the user activity
                $this->userLogs("payslip", $params->employee_id, $log, "<strong>{$params->userData->name}</strong> updated the payslip for: <strong>{$data->name}</strong> for the month: <strong>{$params->month_id} {$params->year_id}</strong>", $params->userId);
            }

            /* Loop through the list of user allowances */
            foreach($params->_allowances as $key => $eachAllowance) {
                // if the allowance id is not empty
                if($eachAllowance['allowance_id']) {
                    // run this section if the request is allowance
                    $stmt = $this->db->prepare("
                        INSERT INTO 
                            payslips_details
                        SET 
                            allowance_id = '{$eachAllowance['allowance_id']}', 
                            employee_id = ?, amount = '{$eachAllowance['allowance_amount']}', 
                            detail_type = '{$eachAllowance['allowance_type']}', 
                            client_id = ?, payslip_id = ?,
                            payslip_month = ?, payslip_year = ?
                    ");
                    $stmt->execute([$params->employee_id, $params->clientId, $payslip_id, $params->month_id, $params->year_id]);
                }
            }

            $this->db->commit();

            return [
                "data" => "The Payslip of {$data->name} for {$params->month_id} {$params->year_id} was successfully generated."
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return ["code" => 203, "data" => $e->getMessage()];
        }

    }

    /**
     * Save Allowance
     * 
     * @param String $params->description
     * @param String $params->allowance_id
     * @param String $params->name
     * @param String $params->type
     * 
     * @return Array
     */
    public function saveallowance(stdClass $params) {
        
        if(!in_array($params->type, ["Allowance", "Deduction"])) {
            return ["code" => 203, "data" => "Sorry! The type must either be Allowance or Deduction."];
        }
        
        $found = false;
        if(isset($params->allowance_id) && !empty($params->allowance_id)) {
            $allowance = $this->pushQuery("*", "payslips_allowance_types", "id='{$params->allowance_id}' AND client_id='{$params->clientId}'");
            if(empty($allowance)) {
                return ["code" => 203, "data" => "Sorry! An invalid allowance id was parsed."];
            }
            $found = true;
        }

        if(!$found) {
            $stmt = $this->db->prepare("INSERT INTO payslips_allowance_types SET default_amount = ?, name = ?, description = ?, type = ?, client_id = ?");
            $stmt->execute([$params->default_amount ?? null, $params->name, $params->description ?? null, $params->type, $params->clientId]);
            // log the user activity
            $this->userLogs("payslip", $this->lastRowId("payslips_allowance_types"), null, "<strong>{$params->userData->name}</strong> added a new {$params->type} record under the payroll section", $params->userId);
        } else {
            $stmt = $this->db->prepare("UPDATE payslips_allowance_types SET default_amount = ?, name = ?, description = ?, type = ? WHERE id = ? AND client_id = ?");
            $stmt->execute([$params->default_amount ?? null, $params->name, $params->description ?? null, $params->type, $params->allowance_id, $params->clientId]);
            // log the user activity
            $this->userLogs("payslip", $params->allowance_id, null, "<strong>{$params->userData->name}</strong> updated the {$params->type} record under the payroll section", $params->userId);
        }

        # set the output to return when successful
        $return = ["code" => 200, "data" => "Request was successfully executed.", "refresh" => 800];
        
        # append to the response
        $return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}payroll-category"];

        // return the output
        return $return;
        
    }

    /**
     * Draw Table
     * 
     * This is a full detail of the payroll information
     * 
     * @return Array
     */
    public function draw(stdClass $params) {

        $printing = true;
        $border = "border='0px'";
        $params->payslip_detail = true;
        $payroll = $this->paysliplist($params)["data"];

        if(empty($payroll)) {
            return ["code" => 203, "data" => "Sorry! An invalid Payslip ID was parsed"];
        }
        $data = $payroll[0];

        $result = "";
        $payslipId = $data->id;
        $client = $data->client_details;

        $allowancesQuery = $data->payslip_details["Allowance"] ?? [];
        $deductionsQuery = $data->payslip_details["Deduction"] ?? [];

        // get the client logo content
        if(!empty($client->client_logo)) {
            $type = pathinfo($client->client_logo, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($client->client_logo);
            $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
        }

        // set the header content
        $result .= "<div style=\"width: 95%; margin: auto auto;\">
                    <table width=\"100%\" style=\"background: #ffffff none repeat scroll 0 0;border-bottom: 2px solid #f4f4f4;position: relative;box-shadow: 0 1px 2px #acacac;width:100%;font-family: open sans; width:100%;margin-bottom:2px\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">
                    <tbody>";
                    $result .= '<tr style="padding: 5px; border-bottom: solid 1px #ccc;">
                            <td colspan="4" align="center" style="padding: 10px;">
                                '.(!empty($client->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\"><br>" : "").'
                                <h2 style="font-size:30px;color:#6777ef;font-family:helvetica;padding:0px;margin:0px;">'.strtoupper($client->client_name).'</h2>
                                <span style="padding:0px; margin:0px;">'.$client->client_address.'</span><br>
                                '.(!empty($client->client_email) ? "<div><strong>Email:</strong> {$client->client_email}</div>" : "").'
                                <span style="padding:0px; margin:0px;"><strong>Tel:</strong> '.$client->client_contact.' '.(!$client->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null).'</span>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" colspan="2" align="left" style="padding-bottom: 10px; font-family: Calibri Light;font-size: 12px;">';
                            if($printing) {
                                $result .= '<strong style="font-size: 15px;">'.$data->employee_info->name.'</strong><br>'.(!empty($data->employee_info->unique_id) ? "<strong>Employee ID:</strong> {$data->employee_info->unique_id}<br>" : null).''.(!empty($data->employee_info->phone_number) ? "<strong>Contact:</strong> {$data->employee_info->phone_number}<br>" : null).''.(!empty($data->employee_info->email) ? "<strong>Email:</strong> {$data->employee_info->email}<br>" : null).'';
                            }
                            $result .= '</td>
                            <td colspan="2" valign="top">
                                <div align="center" style="font-family: Calibri Light; font-size: 12px">
                                Payslip ID #: <strong>'.$payslipId.'</strong><br>
                                <strong>Month / Year</strong>: '.$data->payslip_month.' '.$data->payslip_year.'<br>
                                <strong>Date Created</strong>: '.date("d M Y h:ia", strtotime($data->date_log)).'<br>
                                </div>
                                '.(($printing) ? '<hr style="border: dashed 1px #ccc;">' : null).'
                            </td>
                        </tr>';
                        $result .= "
                    </tbody>
                    </table>";
                    
        if(!$printing) {
            $result .= "<table border=\"0\" width=\"100%\" cellpadding=\"20px\" cellspacing=\"0px\">
                <tbody>
                    <tr>
                        <td valign=\"top\">&nbsp;<strong></strong></td>
                    </tr>
                </tbody>
            </table>";
        }

        $result .= "
        <table border=\"0\" width=\"100%\" cellpadding=\"10px\" cellspacing=\"0px\">
            <tbody>
                <tr>
                    <td style=\"background-color:#f4f4f4\" valign=\"top\">&nbsp;<strong></strong></td>
                </tr>
            </tbody>
        </table>";
        $result .= "<table width=\"100%\" style=\"border:1px solid #ccc;\" border=\"0\">
            <tbody>
                <tr>
                    <td width=\"50%\" style=\"border:1px solid #ccc;\">
                        <div class=\"row justify-content-between\">
                            <table width=\"100%\" class=\"table\" border=\"0\" cellpadding=\"5px\">
                                <tr>
                                    <td colspan=\"2\" align=\"center\">
                                    <span style=\"font-size:16px\"><strong>EARNINGS</strong></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding:10px;'><strong>Basic Salary</strong></td>
                                    <td align=\"right\">GH&cent;{$data->basic_salary}</td>
                                </tr>";
                                foreach($allowancesQuery as $eachAllowance) {
                                    $result .= "<tr>
                                        <td style='padding:10px;'>{$eachAllowance->allowance_type}</td>
                                        <td align=\"right\">GH&cent;{$eachAllowance->amount}</td>
                                    </tr>";
                                }
                                $result .= "<tr>
                                    <td style='padding:10px;'><strong>Gross Salary</strong></td>
                                    <td align=\"right\"><strong>GH&cent;".number_format(($data->basic_salary + array_sum(array_column($data->payslip_details["Allowance"], 'amount'))), 2)."</strong></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td valign=\"top\" style=\"border:1px solid #ccc;\" width=\"50%\" align=\"right\">
                        <div class=\"row justify-content-between\">
                            <table width=\"100%\" class=\"table\" border=\"0\" cellpadding=\"5px\">
                                <tr>
                                    <td colspan=\"2\" align=\"center\">
                                    <span style=\"font-size:16px\"><strong>DEDUCTIONS</strong></span>
                                    </td>
                                </tr>";
                                foreach($deductionsQuery as $eachAllowance) {
                                    $result .= "<tr>
                                        <td style='padding:10px;'>{$eachAllowance->allowance_type}</td>
                                        <td style='padding:10px;' align=\"right\">GH&cent;{$eachAllowance->amount}</td>
                                    </tr>";
                                }
                                $result .= "<tr>
                                    <td style='padding:10px;'><strong>Total Deductions</strong></td>
                                    <td align=\"right\"><strong>GH&cent;".number_format(array_sum(array_column($deductionsQuery, 'amount')), 2)."</strong></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
                
                // summation
                $total_allowance = array_sum(array_column($allowancesQuery, 'amount'));
                $total_deductions = array_sum(array_column($deductionsQuery, 'amount'));
                $total_salary = $data->basic_salary + $total_allowance - $total_deductions;

                $result .= "<tr>
                    <td colspan=\"1\"></td>
                    <td align=\"right\">
                        <div class=\"row justify-content-between\">
                            <table width=\"100%\" cellpadding=\"10px\" class=\"table\" {$border}>
                                <tr>";
                                    $result .= "
                                    <td align=\"right\" width=\"50%\" style=\"background-color:#6777ef; padding:10px; color:#fff; padding:10px;font-weight:bolder;text-transform:uppercase\"><strong>Net Salary</strong></td>
                                    <td align=\"right\" style=\"background-color:#f4f4f4;font-size:20px;padding:10px;font-weight:bolder;text-transform:uppercase\">
                                        <strong>GH&cent;".number_format($total_salary, 2)."</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td width=\"50%\" style=\"background-color:#ccc;color:#000;\" align=\"right\"><strong>AMOUNT IN WORDS</strong></td>
                                    <td align=\"right\" style=\"background-color:#f4f4f4;font-size:16px;padding:10px;text-transform:uppercase\">
                                        ".$this->amount_to_words($total_salary)."
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>";
        $result .= '<table width="100%" align="center" border="0">
            <tbody style="text-align: center;">
                <tr>
                    <td colspan="4">
                        <p style="font-size:12px">
                            <strong>Slogan: </strong>'.$client->client_slogan.'
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>';

        return [
            "code" => 200,
            "data" => $result
        ];

    }

}