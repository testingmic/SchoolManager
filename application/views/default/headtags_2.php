<?php
// global variables
global $usersClass, $accessObject, $medicsClass;

// base url
$baseUrl = config_item("base_url");
$appName = config_item("site_name");

// if the user is not loggedin then show the login form
if(!$usersClass->loggedIn()) { require "login.php"; exit(-1); }

// confirm that user id has been parsed
$loggedUserId = $session->userId;
$cur_user_id = (confirm_url_id(1)) ? xss_clean($SITEURL[1]) : $loggedUserId;

// the query parameter to load the user information
$i_params = (object) ["limit" => 1, "user_id" => $loggedUserId];

// get the user data
$userData = $usersClass->list($i_params)["data"][0];

// get the variables for the accessobject
$accessObject->userId = $loggedUserId;
$accessObject->userPermits = $userData->user_permissions;
$userPrefs = $userData->preferences;
$userPrefs->userId = $loggedUserId;

// user sidebar preference
$sidebar_pref = $userPrefs->sidebar_nav ?? null;
$theme_color = $userPrefs->theme_color ?? null;

// quick links
$quick_links = is_object($userPrefs->quick_links) ? (array) $userPrefs->quick_links : $userPrefs->quick_links;
$my_quick_links = is_array($quick_links) ? array_keys($quick_links) : $quick_links;

// auto close modal options
$auto_close_modal = (!isset($userPrefs->auto_close_modal) || (isset($userPrefs->auto_close_modal) && ($userPrefs->auto_close_modal == "allow"))) ? false : true;
$text_editor = (!isset($userPrefs->text_editor) || (isset($userPrefs->text_editor) && ($userPrefs->text_editor == "trix"))) ? "trix" : "ckeditor";

// user notifications
$userNotifications = [];

// set the current url in session
$user_current_url = current_url();
$session->user_current_url = $user_current_url;

// notification handler
$announcementNotice = $announcementClass->notice($userData);

// is this the current user?
$isAdmin = $userData->user_type == "admin" ? true : false;
$isTeacher = $userData->user_type == "teacher" ? true : false;
$isStudent = $userData->user_type == "student" ? true : false;
$isEmployee = $userData->user_type == "employee" ? true : false;

// user payment preference
$userPrefs->payments = isset($userPrefs->payments) ? $userPrefs->payments : (object) [];
$userPrefs->payments->default_payment = isset($userPrefs->payments->default_payment) ? $userPrefs->payments->default_payment : null;

// chat preferences
if(!isset($userPrefs->messages)) {
    $userPrefs->messages = (object)[
        "enter_to_send" => 1,
        "hide_online" => 0,
        "status" => ""
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $page_title ?? "Dashboard" ?> - <?= $appName ?></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
    <?php foreach($loadedCSS as $eachCSS) { ?>
        <link rel="stylesheet" href="<?= $baseUrl ?><?= $eachCSS ?>">
    <?php } ?>
    <link id="user_current_url" name="user_current_url" value="<?= $user_current_url ?>">
</head>
<body class="<?= $sidebar_pref ?> <?=  $theme_color ?>">
	<!-- <div class="loader"></div> -->
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i
                        class="fas fa-bars"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                        <i class="fas fa-expand"></i>
                    </a>
                    </li>
                </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                    class="nav-link nav-link-lg message-toggle beep"><i class="far fa-envelope"></i></a>
                    <div class="dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="dropdown-header">Messages
                        <div class="float-right">
                        <a href="javascript:void(0)" data-function="mark_as_read" data-item="messages">Mark All As Read</a>
                        </div>
                    </div>
                    <div class="dropdown-list-content dropdown-list-message">
                        
                        <a href="#" class="dropdown-item">
                        <span class="dropdown-item-avatar text-white">
                            <img alt="image" src="assets/img/users/user-5.png" class="rounded-circle">
                        </span>
                        <span class="dropdown-item-desc">
                            <span class="message-user">Jacob Ryan</span>
                            <span class="time messege-text">Your payment invoice is generated.</span>
                            <span class="time text-primary">12 Min Ago</span>
                        </span>
                        </a>

                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="<?= $baseUrl ?>messages">View All <i class="fas fa-chevron-right"></i></a>
                    </div>
                    </div>
                </li>
                <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
                    class="nav-link notification-toggle nav-link-lg beep"><i class="far fa-bell"></i></a>
                    <div class="dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="dropdown-header">Notifications
                        <div class="float-right">
                        <a href="javascript:void(0)" data-function="mark_as_read" data-item="notifications">Mark All As Read</a>
                        </div>
                    </div>
                    <div class="dropdown-list-content dropdown-list-icons">
                        <a href="#" class="dropdown-item dropdown-item-unread">
                        <span class="dropdown-item-icon bg-primary text-white">
                            <i class="fas fa-code"></i>
                        </span>
                        <span class="dropdown-item-desc">
                            Template update is available now!
                            <span class="time text-primary">2 Min Ago</span>
                        </span>
                        </a>
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="<?= $baseUrl ?>notifications">View All <i class="fas fa-chevron-right"></i></a>
                    </div>
                    </div>
                </li>
                <li class="dropdown"><a href="#" data-toggle="dropdown"
                    class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="assets/img/user.png" class="user-img-radious-style">
                    <span class="d-sm-none d-lg-inline-block"></span></a>
                    <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">Hello Sarah Smith</div>
                    <a href="profile.html" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profile
                    </a>
                    <a href="timeline.html" class="dropdown-item has-icon">
                        <i class="fas fa-bolt"></i> Activities
                    </a>
                    <a href="#" class="dropdown-item has-icon">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="auth-login.html" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    </div>
                </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="index.html">
                        <img alt="image" src="assets/img/logo.png" class="header-logo" />
                        <span class="logo-name">Ality</span>
                        </a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li class="dropdown active">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-home"></i><span>Dashboard</span></a>
                        <ul class="dropdown-menu">
                            <li class="active"><a class="nav-link" href="index.html">Dashboard V1</a></li>
                            <li><a class="nav-link" href="index2.html">Dashboard V2</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-broom"></i><span>Widgets</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="widget-chart.html">Chart Widgets</a></li>
                            <li><a class="nav-link" href="widget-data.html">Data Widgets</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fab fa-accusoft"></i><span>Apps</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="chat.html">Chat</a></li>
                            <li><a class="nav-link" href="portfolio.html">Portfolio</a></li>
                            <li><a class="nav-link" href="blog.html">Blog</a></li>
                            <li><a class="nav-link" href="calendar.html">Calendar</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="far fa-envelope"></i><span>Email</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="email-inbox.html">Inbox</a></li>
                            <li><a class="nav-link" href="email-compose.html">Compose</a></li>
                            <li><a class="nav-link" href="email-read.html">read</a></li>
                        </ul>
                        </li>
                        <li class="menu-header">UI Elements</li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-briefcase"></i><span>Basic
                            Components</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="alert.html">Alert</a></li>
                            <li><a class="nav-link" href="badge.html">Badge</a></li>
                            <li><a class="nav-link" href="breadcrumb.html">Breadcrumb</a></li>
                            <li><a class="nav-link" href="buttons.html">Buttons</a></li>
                            <li><a class="nav-link" href="collapse.html">Collapse</a></li>
                            <li><a class="nav-link" href="dropdown.html">Dropdown</a></li>
                            <li><a class="nav-link" href="checkbox-and-radio.html">Checkbox &amp; Radios</a></li>
                            <li><a class="nav-link" href="list-group.html">List Group</a></li>
                            <li><a class="nav-link" href="media-object.html">Media Object</a></li>
                            <li><a class="nav-link" href="navbar.html">Navbar</a></li>
                            <li><a class="nav-link" href="pagination.html">Pagination</a></li>
                            <li><a class="nav-link" href="popover.html">Popover</a></li>
                            <li><a class="nav-link" href="progress.html">Progress</a></li>
                            <li><a class="nav-link" href="tooltip.html">Tooltip</a></li>
                            <li><a class="nav-link" href="flags.html">Flag</a></li>
                            <li><a class="nav-link" href="typography.html">Typography</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-qrcode"></i><span>Advanced</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="avatar.html">Avatar</a></li>
                            <li><a class="nav-link" href="card.html">Card</a></li>
                            <li><a class="nav-link" href="modal.html">Modal</a></li>
                            <li><a class="nav-link" href="sweet-alert.html">Sweet Alert</a></li>
                            <li><a class="nav-link" href="toastr.html">Toastr</a></li>
                            <li><a class="nav-link" href="empty-state.html">Empty State</a></li>
                            <li><a class="nav-link" href="multiple-upload.html">Multiple Upload</a></li>
                            <li><a class="nav-link" href="pricing.html">Pricing</a></li>
                            <li><a class="nav-link" href="tabs.html">Tab</a></li>
                        </ul>
                        </li>
                        <li><a class="nav-link" href="blank.html"><i class="far fa-square"></i><span>Blank Page</span></a></li>
                        <li class="menu-header">Ality</li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="far fa-list-alt"></i><span>Forms</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="basic-form.html">Basic Form</a></li>
                            <li><a class="nav-link" href="forms-advanced-form.html">Advanced Form</a></li>
                            <li><a class="nav-link" href="forms-editor.html">Editor</a></li>
                            <li><a class="nav-link" href="forms-validation.html">Validation</a></li>
                            <li><a class="nav-link" href="form-wizard.html">Form Wizard</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fab fa-buromobelexperte"></i><span>Tables</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="basic-table.html">Basic Tables</a></li>
                            <li><a class="nav-link" href="advance-table.html">Advanced Table</a></li>
                            <li><a class="nav-link" href="datatables.html">Datatable</a></li>
                            <li><a class="nav-link" href="export-table.html">Export Table</a></li>
                            <li><a class="nav-link" href="editable-table.html">Editable Table</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-chart-line"></i><span>Charts</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="chart-amchart.html">amChart</a></li>
                            <li><a class="nav-link" href="chart-apexchart.html">apexchart</a></li>
                            <li><a class="nav-link" href="chart-echart.html">eChart</a></li>
                            <li><a class="nav-link" href="chart-chartjs.html">Chartjs</a></li>
                            <li><a class="nav-link" href="chart-sparkline.html">Sparkline</a></li>
                            <li><a class="nav-link" href="chart-morris.html">Morris</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Icons</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="icon-font-awesome.html">Font Awesome</a></li>
                            <li><a class="nav-link" href="icon-material.html">Material Design</a></li>
                            <li><a class="nav-link" href="icon-ionicons.html">Ion Icons</a></li>
                            <li><a class="nav-link" href="icon-weather-icon.html">Weather Icon</a></li>
                        </ul>
                        </li>
                        <li class="menu-header">Media</li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-camera-retro"></i><span>Gallery</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="light-gallery.html">Light Gallery</a></li>
                            <li><a href="gallery1.html">Gallery 2</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="far fa-clone"></i><span>Sliders</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="carousel.html">Bootstrap Carousel.html</a></li>
                            <li><a class="nav-link" href="owl-carousel.html">Owl Carousel</a></li>
                        </ul>
                        </li>
                        <li><a class="nav-link" href="timeline.html"><i class="fab fa-hubspot"></i><span>Timeline</span></a></li>
                        <li class="menu-header">Maps</li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fab fa-google"></i> <span>Google
                            Maps</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="gmaps-advanced-route.html">Advanced Route</a></li>
                            <li><a href="gmaps-draggable-marker.html">Draggable Marker</a></li>
                            <li><a href="gmaps-geocoding.html">Geocoding</a></li>
                            <li><a href="gmaps-geolocation.html">Geolocation</a></li>
                            <li><a href="gmaps-marker.html">Marker</a></li>
                            <li><a href="gmaps-multiple-marker.html">Multiple Marker</a></li>
                            <li><a href="gmaps-route.html">Route</a></li>
                            <li><a href="gmaps-simple.html">Simple</a></li>
                        </ul>
                        </li>
                        <li><a class="nav-link" href="vector-map.html"><i class="fas fa-map-marked-alt"></i><span>Vector
                            Map</span></a></li>
                        <li class="menu-header">Pages</li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-shield"></i><span>Auth</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="auth-forgot-password.html">Forgot Password</a></li>
                            <li><a href="auth-login.html">Login</a></li>
                            <li><a href="auth-register.html">Register</a></li>
                            <li><a href="auth-reset-password.html">Reset Password</a></li>
                            <li><a href="subscribe.html">Subscribe</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i
                            class="fas fa-exclamation-triangle"></i><span>Errors</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="errors-503.html">503</a></li>
                            <li><a class="nav-link" href="errors-403.html">403</a></li>
                            <li><a class="nav-link" href="errors-404.html">404</a></li>
                            <li><a class="nav-link" href="errors-500.html">500</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-map-signs"></i><span>Other Pages</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="create-post.html">Create Post</a></li>
                            <li><a class="nav-link" href="posts.html">Posts</a></li>
                            <li><a class="nav-link" href="profile.html">Profile</a></li>
                            <li><a class="nav-link" href="contact.html">Contact</a></li>
                            <li><a class="nav-link" href="invoice.html">Invoice</a></li>
                        </ul>
                        </li>
                        <li class="dropdown">
                        <a href="#" class="nav-link has-dropdown"><i class="fas fa-level-down-alt"></i><span>Multilevel</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Menu 1</a></li>
                            <li class="dropdown">
                            <a href="#" class="has-dropdown">Menu 2</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Child Menu 1</a></li>
                                <li class="dropdown">
                                <a href="#" class="has-dropdown">Child Menu 2</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Child Menu 1</a></li>
                                    <li><a href="#">Child Menu 2</a></li>
                                </ul>
                                </li>
                                <li><a href="#"> Child Menu 3</a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                    </ul>
                </aside>
            </div>
