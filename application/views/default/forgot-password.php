<?php
global $myClass;
$baseUrl = $myClass->baseUrl;
$user_current_url = current_url();

// remove the session value
$session->remove("redirect");

// if the user is not loggedin then show the login form
if(loggedIn()) { 
    header("location: {$myClass->dashboardPath}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Verify - <?= $myClass->appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link id="current_url" name="current_url" value="<?= $user_current_url ?>">
  <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/apple-touch-icon.png">
  <style>
  .bg {
    background-image: url('<?= $baseUrl; ?>assets/img/background_2.jpg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
  }
  </style>
  <?= $myClass->google_analytics_code ?>
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
                <h4>Recover Password</h4>
              </div>
              <div class="card-body">
                <?= form_loader(); ?>
                <form method="POST" action="<?= $baseUrl ?>api/auth/forgotten" id="auth-form" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required>
                    <div class="invalid-feedback">Please fill in your email Address</div>
                  </div>
                  <input type="hidden" name="recover" value="true" id="recover" hidden>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Recover Password</button>
                  </div>
                </form>
                <div class="text-center mt-4 mb-3"></div>
                <div class="form-results"></div>
              </div>
            </div>
            <div class="mt-3 mb-4 text-dark p-3 bg-white text-center">
              Already have an account? <a href="<?= $baseUrl ?>login">Login</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div class="app-foottag">
    <div class="d-flex justify-content-between">
        <div>&copy; Copyright <strong><a href="<?= $myClass->baseUrl ?>"><?= $myClass->appName ?></a></strong> &bull; All Rights Reserved</div>
        <div>By: <strong><?= config_item("developer") ?></strong></div>
    </div>
  </div>
  <script src="<?= $baseUrl; ?>assets/js/app.min.js?v=<?= version() ?>"></script>
  <script src="<?= $baseUrl; ?>assets/js/scripts.js?v=<?= version() ?>"></script>
  <script src="<?= $baseUrl ?>assets/js/auth.js?v=<?= version() ?>"></script>
</body>
</html>