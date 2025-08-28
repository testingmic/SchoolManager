<?php
// set some global variables
global $myClass, $fileCaption, $fileContent, $fileTitle;
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?= $fileTitle ?? "404 - Page Not Found" ?> - <?= $appName ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .bg {
      background-image: url('<?= $baseUrl; ?>assets/img/background_2.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }

    .glass-effect {
      backdrop-filter: blur(100px);
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    .floating {
      animation: float 3s ease-in-out infinite;
    }
  </style>
  <?= $myClass->google_analytics_code ?>
</head>

<body class="bg">
  <div class="loader"></div>
  <div id="app">
    <section class="section min-h-screen flex items-center">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div class="rounded-2xl shadow-2xl border border-gray-100 overflow-hidden glass-effect">
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-4">
                <h1 class="text-white text-4xl font-bold text-center mb-2"><?= $fileTitle ?? "404" ?></h1>
                <h2 class="text-white text-xl text-center"><?= $fileCaption ?? "Page Not Found" ?></h2>
              </div>
              
              <div class="p-8 text-center">
                <div class="floating mb-6">
                  <i class="fas fa-search text-6xl text-gray-400"></i>
                </div>
                
                <p class="text-lg text-gray-600 mb-6"><?= $fileContent ?? "Oops! The page you're looking for doesn't exist or has been moved." ?></p>
                
                <div class="space-y-4">
                  <a href="<?= $baseUrl ?>" class="inline-block w-full sm:w-auto bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                  </a>
                  
                  <div class="text-gray-500 mt-4">
                    <p>Here are some helpful links:</p>
                    <div class="mt-2 space-x-4">
                      <a href="<?= $baseUrl ?>dashboard" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                      </a>
                      <a href="<?= $baseUrl ?>support" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <i class="fas fa-question-circle mr-1"></i>Support
                      </a>
                    </div>
                  </div>
                </div>
              </div>
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
</body>

</html>