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
    <title><?= $page_title ?? "Dashboard" ?> : <?= $appName ?></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/gallery.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendors/trix/trix.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/select2/select2.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/fullcalendar/fullcalendar.min.css">
    <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
    <?php foreach($loadedCSS as $eachCSS) { ?>
        <link rel="stylesheet" href="<?= $baseUrl ?><?= $eachCSS ?>">
    <?php } ?>
    <link id="user_current_url" name="user_current_url" value="<?= $user_current_url ?>">
</head>
<body class="<?= $sidebar_pref ?> <?=  $theme_color ?>">
	<div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <div class="progress-bar"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i class="fas fa-expand"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg hidden" id="history-refresh" title="Reload Page"><i class="fas fa-redo-alt"></i></a></li>
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
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown"
                        class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                        <img alt="image" src="<?= $baseUrl ?><?= $userData->image ?>" class="user-img-radious-style">
                        <span class="d-sm-none d-lg-inline-block"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">Hello <?= $userData->name ?></div>
                    <a href="<?= $baseUrl ?>profile" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profile
                    </a>
                    <a href="<?= $baseUrl ?>timeline" class="dropdown-item has-icon">
                        <i class="fas fa-bolt"></i> Activities
                    </a>
                    <a href="<?= $baseUrl ?>settings" class="dropdown-item has-icon">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" onclick="return logout()" class="dropdown-item anchor has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    </div>
                </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    
                    <div class="sidebar-brand">
                        <a href="<?= $baseUrl ?>">
                            <img alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" />
                            <span class="logo-name">Ality</span>
                        </a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>

                        <li>
                            <a href="<?= $baseUrl ?>" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>Students</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-student">Students List</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-student">Add Student</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-clock"></i><span>Guardians</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-guardian">Guardian List</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-guardian">Add Guardian</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>Staff</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-staff">Staff List</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-staff">Add Staff</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= $baseUrl ?>attendance" class="nav-link"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a></li>
                        <li class="menu-header">Academics</li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-classes">List Classes</a></li>
                                <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>add-class">Add Class</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-departments">List Departments</a></li>
                                <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>add-department">Add Department</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-sections">List Sections</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-section">Add Section</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-course">Add Course</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Course Resources</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= $baseUrl ?>timetable" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Assignments</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-assignments">List Assignments</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-assignment">Create Assignment</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>submit-assignment">Submit Assignment</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-books">List Books</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>add-book">Add New Book</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>issue-book">Issue Book</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>return-book">Return Book</a></li>
                            </ul>
                        </li>
                        <li class="menu-header">Finance</li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-dolly-flatbed"></i><span>Fees</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>fees-payment">Receive Payment</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>fees-history">List History</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>fees-category">Category</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>fees-allocation">Allocation</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>fees-reports">Reports</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>HR/Payroll</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>hr-payroll">Payroll</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>hr-history">List History</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>hr-category">Category</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>hr-expenditure">Expenditure</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>hr-reports">Reports</a></li>
                            </ul>
                        </li>
                        <li class="menu-header">Communication</li>
                        <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Event Management</span></a></li>
                        <li class="dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-envelope"></i><span>Emails</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="<?= $baseUrl ?>list-emails">List Mails</a></li>
                                <li><a class="nav-link" href="<?= $baseUrl ?>compose-email">Compose</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= $baseUrl ?>chat" class="nav-link"><i class="fas fa-envelope-open-text"></i><span>Live Chat</span></a></li>

                    </ul>
                </aside>
            </div>
