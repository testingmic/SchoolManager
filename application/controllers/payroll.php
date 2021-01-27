<?php 

class Payroll extends Myschoolgh {

    public function __construct() {
        parent::__construct();

        global $accessObject;
        $this->hasit = $accessObject;
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
                $stmt = $this->db->prepare("INSERT INTO payslips_users_payroll SET 
                client_id = ?, employee_id = ?, basic_salary = ?, gross_salary = ?,
                allowances = ?, deductions = ?, net_allowance = ?, net_salary = ?");
                
                $stmt->execute([$params->clientId, $params->employee_id, $params->basic_salary, 
                    $gross_salary, $t_allowances, $t_deductions, $net_allowance, $net_salary]);
                
                // log the user activity
                $this->userLogs("salary_allowances", $params->employee_id, null, "<strong>{$params->userData->name}</strong> inserted the Salary Allowances of: <strong>{$the_user->name}</strong>", $params->userId);

            } else {

                /** update existing record */
                $stmt = $this->db->prepare("UPDATE payslips_users_payroll SET 
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
            
            /** Insert/Update the basic salary information */
            if(empty($the_user->basic_salary)) {
                /** Insert a new record */
                $stmt = $this->db->prepare("INSERT INTO payslips_users_payroll SET 
                client_id = ?, employee_id = ?, account_name = ?, account_number = ?,
                bank_name = ?, bank_branch = ?, ssnit_number = ?, tin_number = ?");
                
                $stmt->execute([$params->clientId, $params->employee_id, $params->account_name, 
                    $params->account_number, $params->bank_name, $params->bank_branch, $params->ssnit_number, $params->tin_number]);

                // log the user activity
                $this->userLogs("bank_details", $params->employee_id, null, "<strong>{$params->userData->name}</strong> inserted the Bank Details of: <strong>{$the_user->name}</strong>", $params->userId);
                
            } else {
                /** Insert a new record */
                $stmt = $this->db->prepare("UPDATE  payslips_users_payroll SET 
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
                                    $allowances_list .= "<option ".(($eachAllowance->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
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
                                <span class="remove-row cursor btn btn-outline-danger" data-type="allowance" data-value="'.$ii.'"><i class="fa fa-trash"></i></span>
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
                                $allowances_list .= "<option value=\"{$each->id}\">{$each->name}</option>";
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
                                    $deductions_list .= "<option ".(($eachDeduction->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
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
                                <span class="remove-row cursor btn btn-outline-danger" data-type="deductions" data-value="'.$ii.'"><i class="fa fa-trash"></i></span>
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
                                $deductions_list .= "<option value=\"{$each->id}\">{$each->name}</option>";
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
						<a href=\"{$this->baseUrl}dowload?py_id={$employeePayslip->id}&dw=true\" target=\"_blank\" class=\"btn btn-outline-danger\"><i class='fa fa-file-pdf-o'></i> Download</a> &nbsp; 
						<a href=\"{$this->baseUrl}download?py_id={$employeePayslip->id}&dw=false\" target=\"_blank\" class=\"btn btn-outline-primary\"><i class='fa fa-print'></i>  Print</a></div>
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
        global $usersClass, $accessObject;

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
            /** Insert the Payslip Record */
            $stmt = $this->db->prepare("INSERT INTO payslips SET client_id =?, employee_id=?, basic_salary=?, 
                total_allowance =?, total_deductions=?, net_salary=?, payslip_month = ?, payslip_month_id=?, 
                payslip_year=?, payment_mode =?, comments =?, gross_salary = ?, created_by = ?
            ");
            $stmt->execute([$params->clientId, $params->employee_id, $params->basic_salary, 
                $t_allowances, $t_deductions, $net_salary, $params->month_id,
                date("Y-m-t", strtotime("last day of {$params->month_id} {$params->year_id}")),
                $params->year_id, $params->payment_mode ?? null,
                $params->comments ?? null, $gross_salary, $params->userId
            ]);
            // get the last row generated
            $payslip_id = $this->lastRowId("payslips WHERE client_id='{$params->clientId}'");

            // log the user activity
            $this->userLogs("payslip", $params->employee_id, null, "<strong>{$params->userData->name}</strong> generated a payslip for: <strong>{$the_user->name}</strong> for the month: <strong>{$params->month_id} {$params->year_id}</strong>", $params->userId);

        } else {
            /** Payslip details */
            $payslip = $employeePayslip[0];

            /* Delete the employee allowance records and insert a new data */
            $stmt = $this->db->prepare("DELETE FROM payslips_details WHERE employee_id = ? AND client_id = ? AND payslip_month = ? AND payslip_year = ? LIMIT 20");
            $stmt->execute([$params->employee_id, $params->clientId, $params->month_id, $params->year_id]);

            /** Insert the Payslip Record */
            $stmt = $this->db->prepare("UPDATE payslips SET basic_salary=?, 
                total_allowance =?, total_deductions=?, net_salary=?, payment_mode =?, 
                comments =?, gross_salary = ? WHERE
                client_id =? AND employee_id=? AND payslip_month = ? AND payslip_year=?
            ");
            $stmt->execute([$params->basic_salary, 
                $t_allowances, $t_deductions, $net_salary, $params->payment_mode ?? null, 
                $params->comments ?? null, $gross_salary, 
                $params->clientId, $params->employee_id, $params->month_id, $params->year_id
            ]);

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

        return [
            "data" => "The Payslip of {$data->name} for {$params->month_id} {$params->year_id} was successfully generated."
        ];
    }

}