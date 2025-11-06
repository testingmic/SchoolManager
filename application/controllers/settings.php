<?php

class Settings extends Myschoolgh {

    public function __construct() {
		parent::__construct();
	}

    /**
     * Get Settings
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function getsettings(stdClass $params) {

        // global variables
        global $isAdminAccountant;

        if(!$isAdminAccountant) {
            return ["code" => 400, "data" => "Sorry! You do not have the permissions to get the settings."];
        }

        // confirm that the client id was parsed
        if(empty($params->clientId)) {
            return ["code" => 400, "data" => "Sorry! An invalid client id was parsed."];
        }

        // confirm that the setting name was parsed
        if(empty($params->setting_name)) {
            return ["code" => 400, "data" => "Sorry! An invalid setting name was parsed."];
        }

        // get the payroll settings
        $settings = $this->pushQuery("*", "settings", "client_id = '{$params->clientId}' AND setting_name = '{$params->setting_name}'");

        if(empty($settings)) {
            return ["code" => 400, "data" => []];
        }
        $settings = json_decode($settings[0]->setting_value, true);

        return [
            "code" => 200, 
            "data" => $settings
        ];
    }

    /**
     * Save Payroll Settings
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function savesettings(stdClass $params) {

        global $isAdminAccountant;

        if(!$isAdminAccountant) {
            return ["code" => 400, "data" => "Sorry! You do not have the permissions to save the settings."];
        }

        // confirm that the client id was parsed
        if(empty($params->clientId)) {
            return ["code" => 400, "data" => "Sorry! An invalid client id was parsed."];
        }

        // confirm that the setting name was parsed
        if(empty($params->setting_name)) {
            return ["code" => 400, "data" => "Sorry! An invalid setting name was parsed."];
        }

        $data = [
            'auto_generate_payslip' => $params->auto_generate_payslip ?? false,
            'auto_calculate_paye' => $params->auto_calculate_paye ?? false,
            'auto_calculate_ssnit' => $params->auto_calculate_ssnit ?? false,
            'auto_calculate_tier_2' => $params->auto_calculate_tier_2 ?? false,
            'payroll_frequency' => $params->payroll_frequency ?? 'Monthly',
            'payment_day' => $params->payment_day ?? 0
        ];

        // first delete the existing settings
        $this->db->query("DELETE FROM settings WHERE client_id = '{$params->clientId}' AND setting_name = '{$params->setting_name}'");

        // then insert the new settings
        $stmt = $this->db->prepare("INSERT INTO settings SET client_id = ?, setting_name = ?, setting_value = ?");
        $stmt->execute([$params->clientId, $params->setting_name, json_encode($data)]);
        

        return [
            "code" => 200,
            "data" => "The payroll settings were successfully saved."
        ];
    }
}
?>