<?php
$baseUrl = $config->base_url();
$user_current_url = current_url();

if(!empty($session->redirect)) {
    header("Location: {$session->redirect}");
    exit;
}

// if the user is not loggedin then show the login form
if($usersClass->loggedIn()) { 
    header("location: {$config->base_url("main")}");
    exit;
}

// verify header
$page_title = "Account Verification";
$content = "Verify your Account";

// if password was set
if(isset($_GET["password"])) {
    $page_title = "Reset Password";
    $content = "Recover Password";
}

// set the token variable
$token = (isset($_GET["token"]) && strlen($_GET["token"]) > 40) ? xss_clean($_GET["token"]) : null; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login - <?= config_item("site_name") ?></title>
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
            <div class="text-left border-radius mb-2 p-2 bg-white">
                <div><img align="left" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" width="80px" /></div>
                <div>
                    <div class="font-25px text-center font-weight-bold text-dark"><?= config_item('site_name') ?></div> 
                    <div class="text-dark text-center">Your advanced school management system.</div>
                </div>
            </div>
            <div class="card card-primary">
              <div class="card-header">
                <h4>Verify</h4>
              </div>
              <div class="card-body">
              <?php if($token && ($page_title == "Reset Password")) { ?>
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
                <?php } ?>
                </div>
            </div>
            <div class="mt-5 text-white text-center">
              Already have an account? <a href="<?= $baseUrl ?>login">Login</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
  <script src="<?= $baseUrl; ?>assets/js/scripts.js"></script>
  <script src="<?= $baseUrl ?>assets/js/auth.js"></script>
</body>
</html>