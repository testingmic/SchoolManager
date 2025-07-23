<?php
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
  <title>Create Account - <?= $myClass->appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="description" content="Create your account to get started with <?= $myClass->appName ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link id="current_url" name="current_url" value="<?= $baseUrl ?>main">
  <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/favicon.ico">
  <meta name="theme-color" content="#2196F3">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="App - <?= $myClass->appName ?>">
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
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-8 offset-xl-2">
            <div class="text-left mb-2 p-2 bg-white rounded-2xl">
                <div><img align="left" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" width="70px" /></div>
                <div>
                    <div class="font-25px text-center font-weight-bold text-dark"><?= $myClass->appName ?></div> 
                    <div class="text-dark text-center">Your advanced school management system.</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2">
                <h4 class="text-white text-2xl font-bold text-center">Create New Account</h4>
                <p class="text-blue-100 text-center">Join our school management platform</p>
              </div>
              <div class="p-8">
                <?= form_loader(); ?>
                <form method="POST" autocomplete="Off" action="<?= $baseUrl ?>api/auth" id="auth-form" class="needs-validation space-y-6" novalidate="">
                  <div class="space-y-2">
                    <label for="school_name" class="block text-sm font-semibold text-gray-700 mb-2">School Name</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-school text-gray-400"></i>
                      </div>
                      <input id="school_name" type="text" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="school_name" tabindex="1" required autofocus placeholder="Enter school name">
                    </div>
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                      Please enter your school name
                    </div>
                  </div>
                  
                  <div class="space-y-2">
                    <label for="school_address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                      </div>
                      <input id="school_address" type="text" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="school_address" tabindex="2" required placeholder="Enter school address">
                    </div>
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                      Please enter your school address
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="school_contact" class="block text-sm font-semibold text-gray-700 mb-2">Primary Contact <span class="text-red-500">*</span></label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input id="school_contact" type="text" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="school_contact" tabindex="3" required placeholder="Primary contact number">
                      </div>
                      <div class="invalid-feedback text-red-500 text-sm mt-1">
                        Please enter primary contact
                      </div>
                    </div>
                    
                    <div class="space-y-2">
                      <label for="school_contact_2" class="block text-sm font-semibold text-gray-700 mb-2">Secondary Contact</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input id="school_contact_2" type="text" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="school_contact_2" tabindex="4" placeholder="Secondary contact number (optional)">
                      </div>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="email" tabindex="5" required placeholder="Enter email address">
                      </div>
                      <div class="invalid-feedback text-red-500 text-sm mt-1">
                        Please enter a valid email address
                      </div>
                    </div>
                    
                    <div class="space-y-2">
                      <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="password" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white" name="password" tabindex="6" required placeholder="Create a password">
                      </div>
                      <div class="invalid-feedback text-red-500 text-sm mt-1">
                        Please create a password
                      </div>
                    </div>
                  </div>
                  
                  <input id="plan" value="<?= isset($_GET["plan"]) ? xss_clean($_GET["plan"]) : "basic"; ?>" type="hidden" class="form-control" name="plan">
                  <input type="hidden" name="portal_registration" value="true" id="portal_registration" hidden>
                  
                  <div class="space-y-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-2 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" tabindex="7">
                      <i class="fas fa-user-plus mr-2"></i>
                      Create Account
                    </button>
                  </div>
                </form>
                <div class="form-results mt-4"></div>
              </div>
            </div>
            <div class="mt-3 mb-3 text-dark p-3 bg-white text-center rounded-2xl">
              Already have an account? <a href="<?= $baseUrl ?>login" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">Login</a>
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
  <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
  <script src="<?= $baseUrl; ?>assets/js/scripts.js"></script>
  <script src="<?= $baseUrl ?>assets/js/auth.js"></script>
</body>
</html>