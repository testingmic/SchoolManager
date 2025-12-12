<?php

class Settings extends Myschoolgh {

    private $settingsArray = [
        "payroll_settings" => [
            "auto_generate_payslip", 
            "auto_calculate_paye", 
            "auto_calculate_ssnit", 
            "auto_calculate_tier_2", 
            "payroll_frequency", 
            "payment_day", 
            "auto_validate_payslips"
        ],
        "preschool_reporting_legend" => [
            "legend"
        ],
        "preschool_reporting_content" => [
            "sections"
        ],
        "preschool_reporting_classes" => [
            "classes"
        ]
    ];

    private $preschool_reporting_legend_list = [
        "legend_key", "legend_value"
    ];

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
        $settings = $this->pushQuery(
            "*", 
            "settings", 
            "client_id = '{$params->clientId}' AND setting_name IN ('".implode("', '", stringToArray($params->setting_name))."')"
        );

        if(empty($settings)) {
            return ["code" => 400, "data" => []];
        }

        // if the setting name is an array, then loop through the settings and set the data
        if(count(stringToArray($params->setting_name)) > 1) {
            $resultSet = [];
            foreach($settings as $setting) {
                $resultSet[$setting->setting_name] = json_decode($setting->setting_value, true);
            }
            $settings = $resultSet;
        } else {
            $settings = json_decode($settings[0]->setting_value, true);
        }

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

        // confirm that the setting name is valid
        if(empty($this->settingsArray[$params->setting_name])) {
            return ["code" => 400, "data" => "Sorry! An invalid setting name was parsed."];
        }

        // loop through the settings array and set the data
        foreach($this->settingsArray[$params->setting_name] as $item) {
            if(isset($params->{$item})) {
                if($item == "legend") {
                    if(!is_array($params->{$item})) {
                        return ["code" => 400, "data" => "Sorry! The legend must be an array."];
                    }
                    // loop through the legend array and set the data
                    foreach($params->{$item} as $key => $value) {
                        // confirm that the key and value are set
                        if(!isset($value['key']) || !isset($value['value'])) {
                            return ["code" => 400, "data" => "Sorry! The legend must be an array of objects with key and value properties."];
                        }
                        $data[$item][$key] = [
                            'key' => xss_clean(substr($value['key'], 0, 5)),
                            'value' => xss_clean(substr($value['value'], 0, 32)),
                        ];
                    }
                } else {
                    $data[$item] = $params->{$item};
                }
            }
        }

        // first delete the existing settings
        $this->db->query("DELETE FROM settings WHERE client_id = '{$params->clientId}' AND setting_name = '{$params->setting_name}'");

        // then insert the new settings
        $stmt = $this->db->prepare("INSERT INTO settings SET client_id = ?, setting_name = ?, setting_value = ?");
        $stmt->execute([$params->clientId, $params->setting_name, json_encode($data)]);
        
        return [
            "code" => 200,
            "data" => "The {$params->setting_name} settings were successfully saved."
        ];
    }
}
?>