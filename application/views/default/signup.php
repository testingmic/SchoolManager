<?php
// set some global variables
global $myClass;
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;
$user_current_url = current_url();

// if the user is not loggedin then show the login form
if(loggedIn()) { 
    header("location: {$myClass->dashboardPath}");
    exit;
}
$schools = $myClass->clients_list();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Guardian Signup - <?= $appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/select2/select2.css">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/favicon.ico">
  <meta name="theme-color" content="#2196F3">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="<?= $appName ?>">
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
                <h4>Guardian Signup</h4>
              </div>
              <div class="card-body">
                  <?= form_loader(); ?>
                <form method="POST" autocomplete="Off" action="<?= $baseUrl ?>api/auth/login" id="auth-form" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="school_id">School</label>
                    <select id="school_id" data-width="100%" class="form-control selectmpicker" name="school_id" required>
                      <option value="">Select School</option>
                      <?php foreach($schools as $school) { ?>
                        <?php if($school->setup !== 'School') continue; ?>
                        <option value="<?= $school->client_id ?>"><?= $school->client_name ?></option>
                      <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                      Please select your school to proceed
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="school_code">School Code</label>
                    <input id="school_code" type="text" maxlength="6" autocomplete="Off" placeholder="Enter your school signup code" class="form-control text-uppercase" name="school_code" required>
                    <div class="invalid-feedback">
                      Please fill in your school code
                    </div>
                  </div>
                  <div class="form-group hidden contact_number_group">
                    <label for="contact_number">Contact Number</label>
                    <input id="contact_number" maxlength="10" disabled type="text" autocomplete="Off" placeholder="Enter your contact number" class="form-control" name="contact_number">
                    <div class="invalid-feedback">
                      Please fill in your contact number
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="validate_code" tabindex="4">
                      Validate Code
                    </button>
                  </div>
                </form>
                <div class="text-center mt-4 mb-3"></div>
                <div class="form-results"></div>
              </div>
            </div>
            <div class="mt-3 mb-3 text-dark p-3 bg-white text-center">
              Don't have an account? <a href="<?= $baseUrl ?>register">Create One</a>
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
  <script> var baseUrl = "<?= $baseUrl ?>"; </script>
  <script src="<?= $baseUrl; ?>assets/js/app.min.js?v=<?= version() ?>"></script>
  <script src="<?= $baseUrl; ?>assets/js/scripts.js?v=<?= version() ?>"></script>
  <script src="<?= $baseUrl; ?>assets/bundles/select2/select2.js"></script>
  <script src="<?= $baseUrl ?>assets/js/auth.js?v=<?= version() ?>"></script>
</body>
</html>