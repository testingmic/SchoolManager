<?php

namespace App\Libraries;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Qr {

    /**
     * Generate the QR Code for use using the Google Charts Api
     * 
     * @param String        $text       => This is the text to generate the QR Code on
     * @param String        $option    => This is an array of options to use
     * 
     * @return Mixed
     */
    public function generate($text, $option = []) {

        // set the directory root
        $root_dir = ROOT_DIRECTORY . 'assets/uploads/qrcodes/';

        // set the filename to use in the creation of the code
        $filename = $root_dir . (!empty($option['filename']) ? $option['filename'] : 'qrcode.png');

        // create directory if not already existing
        if (!is_dir($root_dir)) {
            mkdir($root_dir, 0755, true);
        }

        // create file if not already existing
        if (!is_file($filename) || !file_exists($filename)) {
            $f = fopen($filename, 'w');
            fclose($f);
        }

        $options = new QROptions([
            'version'      => 7,
            'scale'        => 6,
            'imageBase64'  => false,
        ]);

        // generate qr code
        $qrObj = new QRCode($options);
        $qrcode = $qrObj->render($text);

        file_put_contents($filename, $qrcode);

        return true;
    }
}
