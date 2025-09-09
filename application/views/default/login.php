<?php
// set some global variables
global $myClass;
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;
$user_current_url = current_url();

// if the user is not loggedin then show the login form
if (loggedIn()) {
  header("location: {$myClass->dashboardPath}");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login - <?= $appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link id="current_url" name="current_url" value="<?= $baseUrl ?>main">
  <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/favicon.ico">
  <meta name="theme-color" content="#2196F3">
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="App - <?= $appName ?>">
  <style>
    .bg {
      background-image: url('<?= $baseUrl; ?>assets/img/background_2.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }

    .glass-effect {
      backdrop-filter: blur(100px);
      background: rgba(255, 255, 255, 1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    /* Remove focus outlines for all interactive elements */
    input:focus,
    button:focus,
    select:focus,
    textarea:focus,
    a:focus {
      outline: none !important;
      box-shadow: none !important;
    }
  </style>
  <?= $myClass->google_analytics_code ?>
  <?= $myClass->trackingSnippet() ?>
</head>

<body class="bg">
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="text-left mb-2 p-2 bg-white  rounded-2xl">
              <div><img align="left" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" width="60px" /></div>
              <div>
                <div class="font-25 text-center font-weight-bold text-dark"><?= config_item('site_name') ?></div>
                <div class="text-dark text-center">Your advanced school management software.</div>
              </div>
            </div>
            <div class="rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-2">
                <h4 class="text-white text-2xl font-bold text-center">Welcome Back</h4>
                <p class="text-blue-100 text-center">Sign in to your account</p>
              </div>
              <div class="p-6 pb-0 relative glass-effect">
                <?= form_loader(); ?>
                <form method="POST" autocomplete="Off" action="<?= $baseUrl ?>api/auth/login" id="auth-form" class="needs-validation space-y-6" novalidate="">
                  <div class="space-y-2">
                    
                    <label for="username" class="block text-sm text-black mb-2">
                      <i class="fas fa-user mr-2"></i>Username or Email
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                      <input id="username" type="text" autocomplete="Off" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" name="username" tabindex="1" required autofocus placeholder="Enter your username">
                    </div>
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                      Please fill in your username
                    </div>
                  </div>
                  <div class="space-y-2">
                    <div class="flex justify-between items-center mb-2">
                      <label for="password" class="block text-sm text-black mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                      </label>
                      <a href="<?= $baseUrl ?>forgot-password" class="text-sm text-black hover:text-blue-800 transition-colors duration-200">
                        Forgot Password?
                      </a>
                    </div>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                      </div>
                      <input id="password" type="password" autocomplete="Off" class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 text-gray-900" name="password" tabindex="2" required placeholder="Enter your password">
                      <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                      Please fill in your password
                    </div>
                  </div>
                  <div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-2 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" tabindex="4">
                      <i class="fas fa-sign-in-alt mr-2"></i>
                      Sign In
                    </button>
                  </div>
                </form>
                <div class="form-results mt-3 pb-3"></div>
              </div>
            </div>
            <div class="mt-3 mb-10 text-dark p-3 bg-white text-center  rounded-2xl">
              Don't have an account? <a class="text-blue-600 hover:text-blue-800 transition-colors duration-200" href="https://www.cognitoforms.com/Speedwaev/MySchoolGH1MonthFreeTrial">Create One</a>
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