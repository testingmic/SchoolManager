<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Qr extends Myschoolgh {

    /**
     * Generate the QR Code for use using the Google Charts Api
     * 
     * @param String        $text       => This is the text to generate the QR Code on
     * @param String        $option    => This is an array of options to use
     * 
     * @return Mixed
     */
    public function generate($option = []) {

        // set the directory root
        $root_dir = ROOT_DIRECTORY . '/assets/uploads/qrcodes/' . (!empty($option['client_id']) ? md5($option['client_id']) . '/' : '');

        // set the filename to use in the creation of the code
        $filename = $root_dir . (!empty($option['filename']) ? md5($option['filename']) . '.png' : 'qrcode.png');

        // create directory if not already existing
        if (!is_dir($root_dir)) {
            mkdir($root_dir, 0755, true);
        }

        // check if the file already exists
        if(is_file($filename) && file_exists($filename)) {
            return str_ireplace(ROOT_DIRECTORY, "", $filename);
        }

        // create file if not already existing
        if (!is_file($filename) || !file_exists($filename)) {
            $file = fopen($filename, 'w');
            fclose($file);
        }

        // set the options for the QR Code
        $options = new QROptions([
            'version'      => 7,
            'scale'        => 10,
            'imageBase64'  => false,
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
        ]);

        // generate qr code
        $qrObj = new QRCode($options);
        $qrcode = $qrObj->render($option['text']);

        file_put_contents($filename, $qrcode);

        return str_ireplace(ROOT_DIRECTORY, "", $filename);
    }

    /**
     * Generate the path of the QR Code
     * 
     * @param String $item
     * @param String $record_id
     * @param String $client
     * @param String $other
     * 
     * @return String
     */
    public function makepath($item, $record_id, $client, $other = null) {

        // generate the qr code
        $qr_code = $this->generate([
            "filename" => "{$item}_{$record_id}.png", 
            "client_id" => $client, 
            "text" => "{$item}Id:[{$record_id}]/userType:[{$item}]" . (!empty($other) ? "/{$other}" : "")
        ]);

        return [
            'qrcode' => $qr_code,
            'download' => explode("qrcodes/", $qr_code)[1]
        ];
    }

    /**
     * Lookup the user by the user id
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function lookup($params = null) {
        // create a default object
        $busObject = load_class("buses", "controllers");
        return $busObject->user_lookup($params);
    }

    /**
     * Get the attendance logs
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function logs($params = null) {
        // create a default object
        $busObject = load_class("buses", "controllers");
        return $busObject->attendance_history($params);
    }

    /**
     * Confirm the QR Code
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function confirm($params = null) {

        if(empty($params->option) || empty($params->user)) {
            return [
                "status" => "error",
                "code" => 400,
                "message" => "The option and user are required."
            ];
        }

    }

    /**
     * Initialize the QR Code
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function initialize($params = null) {

        global $defaultUser;
        // check if the option and user are empty
        if(empty($params->option) || empty($params->user)) {
            return [
                "status" => "error",
                "code" => 400,
                "message" => "The option and user are required."
            ];
        }

        // request id
        $request_id = random_string("alnum", RANDOM_STRING);

        // check if the request already exists
        $checkRequest = $this->pushQuery("*", "buses_qr_request", "option='{$params->option}' AND user='{$params->user}' LIMIT 1");
        if(empty($checkRequest)) {
            // insert a new request
            $this->_save("buses_qr_request", [
                "unique_id" => $request_id,
                "option" => $params->option,
                "user" => $params->user,
                "status" => "pending",
                "created_at" => date("Y-m-d H:i:s")
            ]);
        } else {
            $request_id = $checkRequest[0]->unique_id;
        }

        // regenerate the qr code
        $qr_code = $this->qr_code_renderer($defaultUser->user_type, $params->user_row_id, $params->clientId, $defaultUser->name, true, "requestId:[{$request_id}]");

        return [
            "status" => "success",
            "code" => 200,
            "data" => "The QR Code request was successfully initiated.",
            "additional" => [
                "request_id" => $request_id,
                "qr_code" => $qr_code
            ]
        ];
    }

    /**
     * Save the attendance
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function save($params = null) {
        // create a default object
        $busObject = load_class("buses", "controllers");
        return $busObject->log_attendance($params);
    }
}
