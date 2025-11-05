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
  <title>Forgot Password - <?= $myClass->appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="description" content="You can with ease reset your password for <?= $myClass->appName ?>">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <link id="current_url" name="current_url" value="<?= $user_current_url ?>">
  <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/favicon.ico">
  <meta name="theme-color" content="#2196F3">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="App - <?= $myClass->appName ?>">
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-6YKXX6Z3QZ"></script>
  <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-6YKXX6Z3QZ');
  </script>
  <?= $myClass->trackingSnippet() ?>
  <!-- Paste this right before your closing </head> tag -->
  <script type="text/javascript">
    (function(e,c){if(!c.__SV){var l,h;window.mixpanel=c;c._i=[];c.init=function(q,r,f){function t(d,a){var g=a.split(".");2==g.length&&(d=d[g[0]],a=g[1]);d[a]=function(){d.push([a].concat(Array.prototype.slice.call(arguments,0)))}}var b=c;"undefined"!==typeof f?b=c[f]=[]:f="mixpanel";b.people=b.people||[];b.toString=function(d){var a="mixpanel";"mixpanel"!==f&&(a+="."+f);d||(a+=" (stub)");return a};b.people.toString=function(){return b.toString(1)+".people (stub)"};l="disable time_event track track_pageview track_links track_forms track_with_groups add_group set_group remove_group register register_once alias unregister identify name_tag set_config reset opt_in_tracking opt_out_tracking has_opted_in_tracking has_opted_out_tracking clear_opt_in_out_tracking start_batch_senders start_session_recording stop_session_recording people.set people.set_once people.unset people.increment people.append people.union people.track_charge people.clear_charges people.delete_user people.remove".split(" ");
    for(h=0;h<l.length;h++)t(b,l[h]);var n="set set_once union unset remove delete".split(" ");b.get_group=function(){function d(p){a[p]=function(){b.push([g,[p].concat(Array.prototype.slice.call(arguments,0))])}}for(var a={},g=["get_group"].concat(Array.prototype.slice.call(arguments,0)),m=0;m<n.length;m++)d(n[m]);return a};c._i.push([q,r,f])};c.__SV=1.2;var k=e.createElement("script");k.type="text/javascript";k.async=!0;k.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===
    e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";e=e.getElementsByTagName("script")[0];e.parentNode.insertBefore(k,e)}})(document,window.mixpanel||[])

    mixpanel.init('8412d28f71183debe03f3a9ac48756f9', {
      autocapture: true,
      record_sessions_percent: 100,
    })

  </script>
  <style>
    .bg {
      background-image: url('<?= $baseUrl; ?>assets/img/background_2.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }

    .glass-effect {
      backdrop-filter: blur(80px);
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
            <div class="text-left mb-2 p-2 bg-white rounded-2xl">
                <div><img align="left" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" width="60px" /></div>
                <div>
                    <div class="font-25 text-center font-weight-bold text-dark"><?= config_item('site_name') ?></div> 
                    <div class="text-dark text-center">Your advanced school management software.</div>
                </div>
            </div>
            <div class="rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2">
                <h4 class="text-white text-2xl font-bold text-center">Reset Password</h4>
                <p class="text-blue-100 text-center">Enter your email to receive reset instructions</p>
              </div>
              <div class="p-6 relative glass-effect">
                <?= form_loader(); ?>
                <form method="POST" action="<?= $baseUrl ?>api/auth/forgotten" id="auth-form" class="needs-validation space-y-6" novalidate="">
                  <div class="space-y-2">
                    <label for="email" class="block text-sm text-black mb-2">
                      <i class="fas fa-envelope mr-2"></i>Email Address
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                      </div>
                      <input id="email" type="email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" name="email" tabindex="1" required autofocus placeholder="Enter your email address">
                    </div>
                    <div class="invalid-feedback text-red-500 text-sm mt-1">
                      Please enter a valid email address
                    </div>
                  </div>
                  
                  <input type="hidden" name="recover" value="true" id="recover" hidden>
                  
                  <div class="space-y-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-2 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" tabindex="2">
                      <i class="fas fa-paper-plane mr-2"></i>
                      Send Reset Link
                    </button>
                  </div>
                </form>
                <div class="form-results mt-2"></div>
              </div>
            </div>
            <div class="mt-3 mb-3 text-dark p-3 mb-10 bg-white text-center rounded-2xl">
              Remember your password? <a href="<?= $baseUrl ?>login" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">Login</a>
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