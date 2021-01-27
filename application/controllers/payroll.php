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
        global $usersClass;

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
                // set the value
                $allowances[] = [
                    'allowance_id' => (int) $key,
                    'allowance_amount' => $value,
                    'allowance_type' => 'Allowance'
                ];
                $t_allowances += $value;
            }
        }

        // process the employee allowances
        if(isset($params->deductions) && !empty($params->deductions)) {
            // loop through the allowance list
            foreach($params->deductions as $key => $value) {
                // set the value
                $allowances[] = [
                    'allowance_id' => (int) $key,
                    'allowance_amount' => $value,
                    'allowance_type' => 'Deduction'
                ];
                $t_deductions += $value;
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
                <p class='mb-0 pb-0'><strong>Gross Salary:</strong> {$the_user->basic_salary} => {$params->basic_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Allowances:</strong> {$the_user->allowances} => {$t_allowances}</p>
                <p class='mb-0 pb-0'><strong>Gross Salary:</strong> {$the_user->gross_salary} => {$gross_salary}</p>
                <p class='mb-0 pb-0'><strong>Total Deductions:</strong> {$the_user->deductions} => {$t_deductions}</p>
                <p class='mb-0 pb-0'><strong>Total Allowances:</strong> {$the_user->net_allowance} => {$net_allowance}</p>
                <p class='mb-0 pb-0'><strong>Basic Salary:</strong> {$the_user->net_salary} => {$net_salary}</p>";

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

}