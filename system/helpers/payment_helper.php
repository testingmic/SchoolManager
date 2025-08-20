<?php
/**
 * Validate the url and the parameters parsed
 * 
 * @return Bool
 */
function check_url($params) {
    // global 
    global $SITEURL, $accepted_url;

    // get the parameters
    if(!isset($SITEURL[2])) {
        return true;
    }

    // if the url is accepted
    if(!isset($accepted_url[$SITEURL[2]])) {
        return true;
    }

    // confirm the parameters
    $accepted = $accepted_url[$SITEURL[2]];

    // loop through the parsed variables and check if found in the accepted list
    foreach($_GET as $key => $value) {
        if(!in_array($key, $accepted)) {
            return true;
        }
    }
}

/**
 * Prepare the payment form using the checkout url
 * 
 * @param String $getObject->checkout_url
 * @param String $getObject->class_id
 * @param String $getObject->student_id
 * 
 * @return String
 */
function pay_student_fees_checkout() {

    // make use of some global variables
    global $client, $myschoolgh, $myClass, $paymentObj, $getObject, $clientPref, $defaultUser;
    
    // get the payment information
    $pay_info = $paymentObj->confirm_student_payment_record($getObject);

    // payment information
    if(empty($pay_info)) {
        return "<div class='text-center text-danger'>Sorry! The checkout url parsed is either incorrect or has expired.</div>";
    }

    // get the payment information
    $isMultiple = (bool) (count($pay_info) == 1);

    // get the payment form
    $payInit = $pay_info[0];

    // get the balance payable
    $balance = 0;

    // loop through the payment info to get the balance payable
    foreach($pay_info as $pay) {
        $balance += $pay->balance;
    }

    // payment form
    $payment_form = "";
    $payment_form .= "<div class='font-15 mb-2 text-uppercase'><strong>Student Name:</strong> {$payInit->student_details["student_name"]}</div>";
    $payment_form .= "<div class='font-15 mb-2 text-uppercase'><strong>Student ID:</strong> {$payInit->student_details["unique_id"]}</div>";
    $payment_form .= "<div class='font-15 mb-0 text-uppercase'><strong>Student Class:</strong> {$payInit->class_name}</div>";
    $payment_form .= "<div class='font-15 mb-2 text-uppercase'><strong>Outstanding Balance: </strong><span class='font-20'>{$clientPref?->labels?->currency}".number_format($balance, 2)."</span></div>";
    
    // if the item was specified
    if($getObject->item_specified) {
        $payment_form .= "<div class='font-15 mb-2 text-uppercase'><strong>Payment For: </strong>{$payInit->category_name}</div>";
    }

    // set the contact number of email address
    $contact_number = $payInit->student_details["phone_number"] ?? null;
    $email_address = $payInit->student_details["email"] ?? null;

    // get the parent contact and email address
    if(!empty($defaultUser->phone_number)) {
        $contact_number = $defaultUser->phone_number;
    }

    // set the email address
    if(!empty($defaultUser->email)) {
        $email_address = $defaultUser->email;
    }

    // append to the payment form
    $payment_form .= "<div class='form-group mb-1 border-top pt-2 border-primary'>";

    if($balance > 0) {
        $payment_form .= "<label>Email Address <span class='required'>*</span></label>";
        $payment_form .= "<input value='{$email_address}' maxlength='60' type='email' placeholder='Please enter your email address' class='form-control' id='email' name='email'>";
        $payment_form .= "</div>";
        $payment_form .= "<div class='form-group mb-1'>\n";
        $payment_form .= "<input class='form-control' disabled hidden type='hidden' name='payment_param' value='".json_encode($getObject)."'>\n";
        $payment_form .= "<label>Phone Number</label>";
        $payment_form .= "<input value='{$contact_number}' maxlength='15' type='text' placeholder='Please phone number (optional)' class='form-control' id='contact' name='contact'>";
        $payment_form .= "</div>";
        $payment_form .= "<div class='form-group mb-1'>";
        $payment_form .= "<div class='row'>";
        $payment_form .= "<div class='col-md-8'>";
        $payment_form .= "<label>Amount <span class='required'>*</span></label>";
        $payment_form .= "<input maxlength='10' type='number' placeholder='Enter amount (eg. 3.50)' class='form-control' id='amount' name='amount'>";
        $payment_form .= "<input type='hidden' value='{$balance}' disabled hidden class='form-control' id='outstanding' name='outstanding'>";
        $payment_form .= "</div>";
        $payment_form .= "<div class='col-md-4'>";
        $payment_form .= "<label>&nbsp;</label>";
        $payment_form .= "<button onclick='return make_fee_payment()' class='btn btn-block btn-primary'>Pay</button>";
        $payment_form .= "</div>";
        $payment_form .= "</div>";
        $payment_form .= "<input type='hidden' hidden id='client_subaccount' name='client_subaccount' disabled value='{$client->client_account}'>";
    } else {
        $payment_form .= "<div class='text-center font-18 text-success'>Current Outstanding Balance is 0. Hence there is no fee to pay for.</div>";
    }

    $payment_form .= "</div>";

    return $payment_form;
}