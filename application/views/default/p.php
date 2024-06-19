<?php 
// default variables
global $myClass;
$baseUrl = $myClass->baseUrl;

// get the client data
$payment_url = $SITEURL[1] ?? null;
$error = "Sorry! An invalid payment url was parsed.";

// if the payment url had not been set
if(empty($payment_url)) {
    invalid_route("Invalid Route!");
} else {
    // confirm the page url
    $payment = $myClass->pushQuery("expiry_date, checkout_url", "payment_urls", "short_url='{$payment_url}' AND status='1' LIMIT 1");

    // if the payment information is empty
    if(empty($payment)) {
        invalid_route("Invalid Route!");
    } else {
        // confirm if the payment url has not yet expired
        if(strtotime($payment[0]->expiry_date) < time()) {
            invalid_route("Payment link expired!", "Sorry! The payment url that you have supplied expired on {$payment[0]->expiry_date}. Kindly contact the accountant so as to generate a new checkout url for you.");
        } else {
            // redirect the user to the payment page
            $checkout_url = $payment[0]->checkout_url;

            // set the payment url into a session
            $session->cpayment_url = $payment_url;

            // redirect the user
            print "Payment URL successfully confirmed. Redirecting...";

            // redirect
            redirect("{$baseUrl}{$checkout_url}", "refresh:1000");
        }
    }
}
?>