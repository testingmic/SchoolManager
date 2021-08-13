<?php
$baseUrl = $config->base_url();
$user_current_url = current_url();

if(!empty($session->redirect)) {
    header("Location: {$session->redirect}");
    exit;
}

// verify header
$page_title = "Account Verification";
$key = "";

// if password was set
if(isset($_GET["dw"]) && ($_GET["dw"] == "account")) {
    $key = "verify_account";
}

// if password was set
elseif(isset($_GET["dw"]) && ($_GET["dw"] == "password")) {
    $page_title = "Reset Password";
    $key = "reset_password";
}

// if password was set
elseif(isset($_GET["dw"]) && ($_GET["dw"] == "user")) {
    $page_title = "Activate User Account";
    $key = "verify_user";
}

// ensure the user does not over use the page
$session->refresh_page = empty($session->refresh_page) ? 1 : ($session->refresh_page + 1);

// end the page query if the user tries to refresh more than 10 times
if($session->refresh_page >= 15) {
  header("Location: {$baseUrl}main");
  exit;
}
// print an error page
if($session->refresh_page >= 7) {
print '
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
<p>You don\'t have permission to access this resource.</p>
<hr>
<address>Apache/2.4.46 (Win64) OpenSSL/1.1.1h PHP/7.3.26 Server at localhost Port 80</address>
</body></html>';
exit;
}

// set the token variable
$token = (isset($_GET["token"]) && strlen($_GET["token"]) > 40) ? xss_clean($_GET["token"]) : null; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?= $page_title ?> - <?= config_item("site_name") ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link id="current_url" name="current_url" value="<?= $user_current_url ?>">
  <style>
  .bg {
    background-image: url('<?= $baseUrl; ?>assets/img/background_2.jpg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
  }
  </style>
</head>
<body class="bg">
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="text-left mb-2 p-2 bg-white">
                <div><img align="left" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" width="70px" /></div>
                <div>
                    <div class="font-25px text-center font-weight-bold text-dark"><?= config_item('site_name') ?></div> 
                    <div class="text-dark text-center">Your advanced school management system.</div>
                </div>
            </div>
            <div class="card card-primary">
              <div class="card-header">
                <h4><?= $page_title ?></h4>
              </div>
              <div class="card-body">
                <?php if($token && ($key == "reset_password")) { ?>
                  <?= form_loader(); ?>
                    <?php
                    // reset the expiry
                    $expiry_time = 60*60*6;
                    
                    // verify user account
                    $stmt = $myschoolgh->prepare("SELECT * FROM users_reset_request WHERE request_token=? AND token_status=? LIMIT 1");
                    $stmt->execute([$token, 'PENDING']);
                    
                    // failed
                    $count = $stmt->rowCount();
                    
                    // if not empty
                    if($count) {
                        // get the results
                        $data = $stmt->fetch(PDO::FETCH_OBJ);
                        // time check
                        $expiry_time = $data->expiry_time;
                        $username = $data->username;
                        $user_id = $data->user_id;
                    }
                    // confirm that the token hasnt yet expired
                    if(!$count || ($expiry_time < time())) {
                        print "<div class='alert alert-danger'>Sorry! The token could not be authenticated or has expired.</div>";
                    }
                    // present the user with the form to reset password
                    if($count) {
                    ?>
                      <form method="POST" action="<?= $baseUrl ?>api/auth" id="auth-form" class="needs-validation" novalidate="">
                        <div class="form-group">
                          <label for="password">Password</label>
                          <input id="password" type="password" class="form-control" name="password" tabindex="1" required>
                        </div>
                        <div class="form-group">
                          <label for="password_2">Confirm Password</label>
                          <input id="password_2" type="password" class="form-control" name="password_2" tabindex="1" required>
                        </div>
                        <input type="hidden" name="reset_token" value="<?= $token ?>" id="reset_token" hidden>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Reset Password</button>
                        </div>
                      </form>
                      <div class="text-center mt-4 mb-3"></div>
                      <div class="form-results"></div>                
                  <?php } ?>
                <?php } elseif($token && ($key == "verify_account" || $key == "verify_user")) {

                  // verify user account
                  $stmt = $myschoolgh->prepare("SELECT 
                      a.username, a.client_id, a.item_id, a.item_id AS user_id, 
                      u.client_preferences, a.changed_password, a.username
                    FROM users a 
                    LEFT JOIN 
                      clients_accounts u ON u.client_id = a.client_id
                    WHERE 
                      a.verify_token=? LIMIT 1
                  ");
                  $stmt->execute([$token]);
                  $result = $stmt->fetch(PDO::FETCH_OBJ);

                  // confirm that the token hasnt yet expired
                  if(!isset($result->client_id)) {
                      print "<div class='alert alert-danger'>Sorry! An invalid verification code was submitted for processing.</div>";
                  } else {
                    // convert the preferences to an object
                    $prefs = json_decode($result->client_preferences);

                    // set the expiry date to one month from activation
                    $prefs->account->verified_date = date("Y-m-d h:iA");
                    $prefs->account->expiry = date("Y-m-d h:iA", strtotime("+3 months"));
                    
                    // confirm that the verification code matches
                    if(($key == "verify_account") && ($prefs->account->activation_code !== $token)) {
                      print "<div class='alert alert-danger'>Sorry! An invalid verification code was submitted for processing.</div>";
                    } else {
     
                      // activate the user account
                      $stmt = $myschoolgh->prepare("UPDATE users SET verify_token = ?, token_expiry = ?, status = ?, user_status = ?, verified_email = ?, verified_date = now() WHERE item_id = ? AND client_id = ? LIMIT 1");
                      $stmt->execute([NULL, NULL, 1, "Active", "Y", $result->user_id, $result->client_id]);

                      // if the request is to verify a user account
                      if($key == "verify_account") {
                        $stmt = $myschoolgh->prepare("UPDATE clients_accounts SET client_state = ?, client_preferences = ? WHERE client_id = ? LIMIT 1");
                        $stmt->execute(['Activated', json_encode($prefs), $result->client_id]);
                      }

                      // log the user activity
                      $myClass->userLogs("verify_account", $result->user_id, null, "{$result->username}'s - account was successfully activated.", $result->user_id, $result->client_id, "Account was manually activated using the Activation link.");
                      $session->remove("refresh_page");

                      // print the success notification
                      print "<div class='alert alert-success text-center'>Congrats! Your account was successfully activated. You can now login to continue.</div>";

                      // generate a new password for the user
                      if(($key == "verify_user") && !$result->changed_password) {

                        // create the user agent
                        $user_agent = load_class('user_agent', 'libraries');

                        // create the reset password token
                        $random_string = random_string("alnum", 32);
                        $request_token = random_string('alnum', mt_rand(60, 75));

                        // the agent
                        $ip = $user_agent->ip_address();
                        $br = $user_agent->browser()." ".$user_agent->platform();

                        // set the token expiry time to 2 hour from the moment of request
                        $expiry_time = time()+(60*60*2);

                        // generate the
                        $stmt = $myschoolgh->prepare("INSERT INTO users_reset_request SET
                          item_id = '{$random_string}', username='{$result->username}', user_id='{$result->user_id}', 
                          request_token='{$request_token}', user_agent='{$br}|{$ip}', expiry_time='{$expiry_time}',
                          client_id = '{$result->client_id}'
                        ");
                        $stmt->execute();

                        // redirect the user to the reset password page
                        redirect("{$baseUrl}verify?dw=password&token={$request_token}","refresh:3000");
                      }
                  ?>

                <?php
                    } 
                  }
                } else { ?>
                <div class="alert alert-danger text-center">
                  Sorry! An invalid verification code was submitted for processing.
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="mt-3 text-dark p-3 bg-white text-center">
              Already have an account? <a href="<?= $baseUrl ?>login">Login</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div class="app-foottag">
    <div class="d-flex justify-content-between">
        <div>&copy; Copyright <strong><a href="<?= config_item("site_url") ?>"><?= config_item("site_name") ?></a></strong> &bull; All Rights Reserved</div>
        <div>By: <strong><?= config_item("developer") ?></strong></div>
    </div>
  </div>
  <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
  <script src="<?= $baseUrl; ?>assets/js/scripts.js"></script>
  <script src="<?= $baseUrl ?>assets/js/auth.js"></script>
</body>
</html>