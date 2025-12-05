<?php 

class Payroll extends Myschoolgh {

    
    public function __construct($params = null) {
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

        // get global variables
        global $accessObject, $defaultUser;

        if(empty($params->employee_id)) {
            if(!$accessObject->hasAccess("generate", "payslip") && !isset($params->payslip_id)) {
                $params->employee_id = $params->userId ?? $defaultUser->user_id;
            }
        }

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        
        $params->query .= !empty($params->year_id) ? " AND a.payslip_year ='{$params->year_id}'" : null;
        $params->query .= !empty($params->month_id) ? " AND a.payslip_month ='{$params->month_id}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id = '{$params->clientId}'" : null;
        $params->query .= !empty($params->employee_id) ? " AND a.employee_id = '{$params->employee_id}'" : null;
        $params->query .= !empty($params->payslip_id) ? " AND a.item_id = '{$params->payslip_id}'" : null;

        try {

            $payslipDetails = (bool) (isset($params->payslip_detail) && !empty($params->payslip_detail));
            $simpleData = (bool) (isset($params->simple_data) && !empty($params->simple_data));

            $stmt = $this->db->prepare("
                SELECT a.* ".(!$simpleData ? ", 
                    u.name AS emp_name, u.phone_number AS emp_phone, u.email AS emp_email, 
                    u.image AS emp_image, u.unique_id AS emp_unique_id, u.last_seen AS emp_last_seen, 
                    u.online AS emp_online, u.user_type AS emp_user_type, 
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.unique_id,'|',b.last_seen,'|',b.online,'|',b.user_type) 
                    FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info " : null)."
                FROM payslips a
                ".(!$simpleData ? " LEFT JOIN users u ON u.item_id = a.employee_id " : null)."
                WHERE {$params->query} AND a.deleted = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                if(!empty($result->created_by_info)) {
                    // loop through the information
                    foreach(["created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","unique_id","last_seen","online","user_type"]);
                    }
                }

                if(!$simpleData) {
                    $result->employee_info = (object) [
                        "name" => $result->emp_name,
                        "phone_number" => $result->emp_phone,
                        "email" => $result->emp_email,
                        "image" => $result->emp_image,
                        "user_type" => $result->emp_user_type,
                        "unique_id" => $result->emp_unique_id,
                    ];

                    // if the payslip details is parsed
                    if($payslipDetails) {
                        // load the payslip details
                        $detail = $this->pushQuery("a.*, at.name AS allowance_type", 
                            "payslips_details a LEFT JOIN payslips_allowance_types at ON at.id = a.allowance_id", 
                            "a.payslip_id='{$result->id}' AND a.employee_id='{$result->employee_id}'");
                        $result->payslip_details = [];
                        foreach($detail as $each) {
                            $result->payslip_details[$each->detail_type][] = $each;
                        }
                        $result->client_details = $this->pushQuery("a.*", "clients_accounts a", "a.client_id = '{$result->client_id}' AND a.client_status='1' LIMIT 1")[0];
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
            return ["code" => 400, "data" => "Sorry! You do not have the permissions to modify the details of this employee's salary information."];
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
        if(!empty($params->allowances) && is_array($params->allowances)) {
            // loop through the allowance list
            foreach($params->allowances as $key => $value) {
                // check if the key is not null
                if($key !== "null" && !empty($key)) {
                    // set the value
                    $allowances[] = [
                        'allowance_id' => (int) $key,
                        'allowance_amount' => !empty($value) ? $value : 0,
                        'allowance_type' => 'Allowance'
                    ];
                    $t_allowances += !empty($value) && preg_match("/^[0-9]+$/", $value) ? $value : 0;
                }
            }
        }

        // process the employee allowances
        if(!empty($params->deductions) && is_array($params->deductions)) {
            // loop through the allowance list
            foreach($params->deductions as $key => $value) {
                // check if the key is not null
                if($key !== "null" && !empty($key)) {
                    // set the value
                    $allowances[] = [
                        'allowance_id' => (int) $key,
                        'allowance_amount' => $value,
                        'allowance_type' => 'Deduction'
                    ];
                    $t_deductions += !empty($value) && preg_match("/^[0-9]+$/", $value) ? $value : 0;
                }
            }
        }

        $data = "Employee Bank Details was successfully updated";

        // set the employee allowances
        $params->_allowances = $allowances;

        /** if the gross salary is set */
        if(!empty($params->basic_salary)) {

            // another check
            if(empty($the_user->basic_salary)) {
                $employeePayslip = $this->pushQuery("*", "payslips_employees_payroll", "client_id='{$params->clientId}' AND employee_id='{$params->employee_id}' LIMIT 1");
            }

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
            if(empty($the_user->basic_salary) && empty($employeePayslip)) {

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

                
                if(empty($employeePayslip)) {
                    /** Data to save */
                    $log = "
                    <p class='mb-0 pb-0'><strong>Basic Salary:</strong> {$the_user->basic_salary} => {$params->basic_salary}</p>
                    <p class='mb-0 pb-0'><strong>Total Earnings:</strong> {$the_user->allowances} => {$t_allowances}</p>
                    <p class='mb-0 pb-0'><strong>Gross Salary:</strong> {$the_user->gross_salary} => {$gross_salary}</p>
                    <p class='mb-0 pb-0'><strong>Total Deductions:</strong> {$the_user->deductions} => {$t_deductions}</p>
                    <p class='mb-0 pb-0'><strong>Net Allowances:</strong> {$the_user->net_allowance} => {$net_allowance}</p>
                    <p class='mb-0 pb-0'><strong>Net Salary:</strong> {$the_user->net_salary} => {$net_salary}</p>";
    
                    // log the user activity
                    $this->userLogs("salary_allowances", $params->employee_id, $log, "<strong>{$params->userData->name}</strong> updated the Salary Allowances of: <strong>{$the_user->name}</strong>", $params->userId);
                }

            }
            $data = "Employee Allowances was successfully updated";
        }

        else if(!empty($params->account_name)) {

            // set the bank name
            $params->bank_name = $params->bank_name ?? null;

            // check if the employee payroll exists
            $employeePayslip = $this->pushQuery("*", "payslips_employees_payroll", "client_id='{$params->clientId}' AND employee_id='{$params->employee_id}' LIMIT 1");

            /** Insert/Update the basic salary information */
            if(empty($the_user->basic_salary) && empty($employeePayslip)) {
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
            "data" => $data,
            "additional" => ["clear" => true, "href" => "{$this->baseUrl}payroll-view/{$params->employee_id}/salary"]
        ];
    }

    /**
     * Load the Payslip of an Employee for a specific period
     * 
     * @param stdClass $params
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
            return ["code" => 400, "data" => "Sorry! You do not have the permissions to view the details of this payslip."];
        }

        if((strlen($params->year_id) !== 4)  || ($params->year_id === "null")) {
            return ["code" => 400, "data" => "Please select a valid year to load record."];
        }

        // return error
        if(strlen($params->month_id) < 3 || ($params->month_id === "null")) {
            return ["code" => 400, "data" => "Please select a valid month to load record."];
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
                <div class='d-flex justify-content-between'>
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
						<a href=\"{$this->baseUrl}download/payslip?pay_id={$employeePayslip->item_id}\" target=\"_blank\" class=\"btn btn-outline-primary\"><i class='fa fa-print'></i>  Print</a></div>
				";
			} else {
				$note = "<div class=\"text-danger mb-3 text-center\">
						This paylip was generated on <strong>{$employeePayslip->date_log}</strong> awaiting redemption. Any updates made will replace the current record.
						</div>
						<div class='d-flex justify-content-between'>
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
     * @param stdClass $params
     * @param String $params->employee_id
     * @param String $params->month_id
     * @param String $params->year_id
     * 
     * @return Array
     */
    public function generatepayslip(stdClass $params) {

        // global variable
        global $usersClass, $accessObject, $noticeClass;

        // if the bypass checks is not empty
        if(!empty($params->bypass_checks)) {
            if(!$accessObject->hasAccess("generate", "payslip")) {
                return ["code" => 400, "data" => "Sorry! You do not have the permissions to generate a payslip."];
            }

            if((strlen($params->year_id) !== 4)  || ($params->year_id === "null")) {
                return ["code" => 400, "data" => "Please select a valid year to load record."];
            }

            // return error
            if(strlen($params->month_id) < 3 || ($params->month_id === "null")) {
                return ["code" => 400, "data" => "Please select a valid month to load record."];
            }

            // confirm that the user_id does not already exist
            $i_params = (object) [
                "limit" => 1, 
                "user_payroll" => true, 
                "minified" => "minified_content",
                "user_id" => $params->employee_id, 
            ];
            $the_user = $usersClass->list($i_params)["data"];

            // get the user data
            if(empty($the_user)) {
                return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
            }

            $data = $the_user[0];
        } else {
            $data = $params->user_info;
        }

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
                $item_id = random_string("alnum", RANDOM_STRING);

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
                        $item_id, $params->clientId, $account_id, "payroll", 'Expense', "PaySlip - {$params->month_id} {$params->year_id} for {$data->name}", 
                        $net_salary, $params->userId, date("Y-m-d"), $payment_mode, "Auto Generation of PaySlip - {$params->month_id} {$params->year_id} for <strong>{$data->name}</strong>",
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
                    'content' => "Your Payslip for <strong>{$params->month_id} {$params->year_id}</strong> has been generated successfully. <a target=\"_blank\" href=\"{{APPURL}}download/payslip?pay_id={$item_id}&dw=true\">Click Here</a> to download it.",
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
                <p class='mb-0 pb-0'><strong>Total Earnings:</strong> {$payslip->total_allowance} => {$t_allowances}</p>
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
            return ["code" => 400, "data" => $e->getMessage()];
        }

    }

    /**
     * Bulk Validate Payslip
     * 
     * @return Array
     */
    public function bulkvalidatepayslip(stdClass $params) {

        try {

            global $accessObject;

            // if the user does not have the permissions to validate a payslip
            if(!$accessObject->hasAccess("validate", "payslip")) {
                return ["code" => 400, "data" => "Sorry! You do not have the permissions to validate a payslip."];
            }

            // if the payslip ids is empty
            if(empty($params->payslip_ids)) {
                return ["code" => 400, "data" => "Sorry! No payslip ids were parsed."];
            }

            // if the payslip ids is not an array
            if(!is_array($params->payslip_ids)) {
                return ["code" => 400, "data" => "Sorry! The payslip ids must be an array."];
            }

             // loop through the array list
            foreach($params->payslip_ids as $record_id) {

                // get the payslip record
                $payslip = $this->pushQuery("a.payslip_month, a.id, a.payslip_year, (SELECT b.name FROM users b WHERE b.item_id = a.employee_id ORDER BY b.id DESC LIMIT 1) AS employee_name", 
                    "payslips a", "a.client_id='{$params->clientId}' AND a.deleted='0' AND a.id='{$record_id}' AND a.validated='0' LIMIT 1");
                
                // if the payslip record is empty
                if(empty($payslip)) {
                    continue;
                }
                $this->db->query("UPDATE payslips SET validated='1', validated_date = now(), status='1' WHERE id='{$record_id}' LIMIT 1");

                // change the state of the transaction to approved
                $this->db->query("UPDATE accounts_transaction 
                    SET state='Approved', validated_date = now(), validated_by = '{$params->userId}' WHERE id='{$record_id}' AND state != 'Approved' LIMIT 3
                ");

                // log the user activity
                $this->userLogs("payslip", $record_id, null, "<strong>{$params->userData->name}</strong> validated the payslip: <strong>{$payslip[0]->employee_name}</strong> for the month: <strong>{$payslip[0]->payslip_month} {$payslip[0]->payslip_year}</strong>", $params->userId);

            }

            return ["code" => 200, "data" => "Payslip successfully validated.", "additional" => ["href" => "{$this->baseUrl}payslips"]];
        } catch(PDOException $e) {
            return ["code" => 400, "data" => $e->getMessage()];
        }
    }

    /**
     * Generate Payslips
     * 
     * Generate the payslips for the selected employees
     * 
     * @return Array
     */
    public function generatepayslips(stdClass $params) {
        
        try {

            // global variable
            global $usersClass, $accessObject, $noticeClass;

            if(!$accessObject->hasAccess("generate", "payslip")) {
                return ["code" => 400, "data" => "Sorry! You do not have the permissions to generate a payslip."];
            }

            if(empty($params->year_id) || empty($params->month_id) || empty($params->user_ids)) {
                return ["code" => 400, "data" => "Please select a valid year, month and employees to generate payslips."];
            }

            if((strlen($params->year_id) !== 4)  || ($params->year_id === "null")) {
                return ["code" => 400, "data" => "Please select a valid year to load record."];
            }
    
            // return error
            if(strlen($params->month_id) < 3 || ($params->month_id === "null")) {
                return ["code" => 400, "data" => "Please select a valid month to load record."];
            }

            // loop through the user ids
            foreach($params->user_ids as $each) {

                // payload for the payslip generation
                $payload = (object) [
                    'payment_mode' => 'Bank',
                    'userId' => $params->userId,
                    'year_id' => $params->year_id,
                    'clientId' => $params->clientId,
                    'month_id' => $params->month_id,
                    'userData' => $params->userData,
                    'employee_id' => $each['user_id'],
                    'basic_salary' => $each['basic_salary'],
                    'allowances' => [],
                    'deductions' => [],
                    'user_info' => (object)[
                        'name' => $each['user_name'],
                        'basic_salary' => $each['basic_salary'],
                        'unique_id' => $each['user_id']
                    ],
                    'bypass_checks' => true
                ];

                // get the allowances of the employee
                $allowances = $this->pushQuery("*", "payslips_employees_allowances", "employee_id='{$each['user_id']}' AND client_id='{$params->clientId}'");
                if(!empty($allowances)) {
                    foreach($allowances as $eachAllowance) {
                        if($eachAllowance->type == 'Allowance') {
                            $payload->allowances[$eachAllowance->id] = $eachAllowance->amount;
                        } else {
                            $payload->deductions[$eachAllowance->id] = $eachAllowance->amount;
                        }
                    }
                }
                $this->generatepayslip($payload);
            }

            return [
                "code" => 200, 
                "data" => "The payslips were successfully generated.",
                "additional" => ["href" => "{$this->baseUrl}payslips"]
            ];
            
        } catch(PDOException $e) {
            return ["code" => 400, "data" => $e->getMessage()];
        }

    }

    /**
     * Save Allowance
     * 
     * @param stdClass $params
     * @param String $params->description
     * @param String $params->allowance_id
     * @param String $params->name
     * @param String $params->type
     * 
     * @return Array
     */
    public function saveallowance(stdClass $params) {
        
        if(!in_array($params->allowance_type, ["Allowance", "Deduction"])) {
            return ["code" => 400, "data" => "Sorry! The type must either be Allowance or Deduction."];
        }
        
        $found = false;
        if(isset($params->allowance_id) && !empty($params->allowance_id)) {
            $allowance = $this->pushQuery("*", "payslips_allowance_types", "id='{$params->allowance_id}' AND client_id='{$params->clientId}'");
            if(empty($allowance)) {
                return ["code" => 400, "data" => "Sorry! An invalid allowance id was parsed."];
            }
            $found = true;
        }

        if(!empty($params->calculation_method) && !in_array($params->calculation_method, ["fixed_amount", "percentage_on_gross_total"])) {
            return ["code" => 400, "data" => "Sorry! An invalid calculation method was parsed."];
        }

        if(!$found) {

            // if the name is reserved
            if(in_array(strtoupper($params->name), ["SSNIT", "TIER 2", "PAYE"])) {
                return ["code" => 400, "data" => "Sorry! The name {$params->name} is reserved and cannot be used."];
            }

            $stmt = $this->db->prepare("INSERT INTO payslips_allowance_types 
                SET default_amount = ?, name = ?, description = ?, type = ?, client_id = ?, is_statutory = ?,
                    pre_tax_deduction = ?, calculation_method = ?, calculation_value = ?,
                    subject_to_paye = ?, subject_to_ssnit = ?
            ");
            $stmt->execute([
                $params->default_amount ?? null, $params->name, $params->description ?? null, 
                $params->allowance_type, $params->clientId, $params->is_statutory ?? 'No',
                $params->pre_tax_deduction ?? 'No', $params->calculation_method ?? null, 
                $params->calculation_value ?? null, $params->subject_to_paye ?? 'No', 
                $params->subject_to_ssnit ?? 'No'
            ]);
            // log the user activity
            $this->userLogs("payslip", $this->lastRowId("payslips_allowance_types"), null, "<strong>{$params->userData->name}</strong> added a new {$params->allowance_type} record under the payroll section", $params->userId);
        } else {
            $stmt = $this->db->prepare("UPDATE payslips_allowance_types 
                SET default_amount = ?, name = ?, description = ?, type = ?, is_statutory = ?
                ".(!empty($params->pre_tax_deduction) ? ", pre_tax_deduction = '{$params->pre_tax_deduction}'" : "")."
                ".(!empty($params->calculation_method) ? ", calculation_method = '{$params->calculation_method}'" : "")."
                ".(!empty($params->calculation_value) ? ", calculation_value = '{$params->calculation_value}'" : "")."
                ".(!empty($params->subject_to_paye) ? ", subject_to_paye = '{$params->subject_to_paye}'" : "")."
                ".(!empty($params->subject_to_ssnit) ? ", subject_to_ssnit = '{$params->subject_to_ssnit}'" : "")."
                WHERE id = ? AND client_id = ?");
            $stmt->execute([
                $params->default_amount ?? null, $params->name, $params->description ?? null, 
                $params->allowance_type, $params->is_statutory ?? 'No', $params->allowance_id, $params->clientId
            ]);
            // log the user activity
            $this->userLogs("payslip", $params->allowance_id, null, "<strong>{$params->userData->name}</strong> updated the {$params->allowance_type} record under the payroll section", $params->userId);
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
            return ["code" => 400, "data" => "Sorry! An invalid Payslip ID was parsed"];
        }
        $data = $payroll[0];

        $result = "";
        $payslipId = $data->id;
        $client = $data->client_details;

        $allowancesQuery = $data->payslip_details["Allowance"] ?? [];
        $deductionsQuery = $data->payslip_details["Deduction"] ?? [];

        $allowancesQuery = !empty($allowancesQuery) && is_array($allowancesQuery) ? $allowancesQuery : [];
        $deductionsQuery = !empty($deductionsQuery) && is_array($deductionsQuery) ? $deductionsQuery : [];

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
                                if(!empty($allowancesQuery) && is_array($allowancesQuery)) {
                                    foreach($allowancesQuery as $eachAllowance) {
                                        $result .= "<tr>
                                            <td style='padding:10px;'>{$eachAllowance->allowance_type}</td>
                                            <td align=\"right\">GH&cent;{$eachAllowance->amount}</td>
                                        </tr>";
                                    }
                                }
                                $result .= "<tr>
                                    <td style='padding:10px;'><strong>Gross Salary</strong></td>
                                    <td align=\"right\"><strong>GH&cent;".number_format(($data->basic_salary + array_sum(array_column(($data->payslip_details["Allowance"] ?? []), 'amount'))), 2)."</strong></td>
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
                                if(!empty($deductionsQuery) && is_array($allowancesQuery)) {
                                    foreach($deductionsQuery as $eachAllowance) {
                                        $result .= "<tr>
                                            <td style='padding:10px;'>{$eachAllowance->allowance_type}</td>
                                            <td style='padding:10px;' align=\"right\">GH&cent;{$eachAllowance->amount}</td>
                                        </tr>";
                                    }
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
                                    <td align=\"right\" width=\"50%\" style=\"background-color:#6777ef; padding:10px; color:#fff; padding:10px;font-weight:bolder;\"><strong>Net Salary</strong></td>
                                    <td align=\"right\" style=\"background-color:#f4f4f4;font-size:20px;padding:10px;font-weight:bolder;\">
                                        <strong>GH&cent;".number_format($total_salary, 2)."</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td width=\"50%\" style=\"background-color:#ccc;color:#000;\" align=\"right\"><strong>Amount in Words</strong></td>
                                    <td align=\"right\" style=\"background-color:#f4f4f4;font-size:16px;padding:10px;\">
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