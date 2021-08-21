<?php function communication_menu() { global $baseUrl, $accessObject; ?>
    <?php if($accessObject->hasAccess("manage", "communication")) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-envelope"></i><span>Emails & SMS</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>sms_send">Send SMS</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>email_send">Send Email</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>smsemail_report">SMS / Email Reports</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>sms_template">SMS Templates</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>email_template">Email Templates</a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
<?php function incidents_menu() { global $baseUrl, $accessObject; ?>
    <?php if($accessObject->hasAccess("view", "incident")) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-list"></i><span>Incidents</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>incidents_list">List Incidents</a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
<?php function general_menu($isAdmin = false) { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>Students</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-student">Students List</a></li>
            <?php if($accessObject->hasAccess("add", "student")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-student">New Admission</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php if($accessObject->hasAccess("view", "guardian")) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-clock"></i><span>Guardians</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-guardian">Guardian List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-guardian">Add Guardian</a></li>
        </ul>
    </li>
    <?php } ?>
    
    <?php if($isAdmin) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i><span>Bulk Action</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>assign-class">Assign Class</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>assign-username_password">Login Credentials</a></li>
            <!-- <li><a class="nav-link" href="<?= $baseUrl ?>assign-section">Assign Section</a></li> -->
        </ul>
    </li>
    <?php } ?>

    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>Staff</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-staff">Staff List</a></li>
            <?php if($accessObject->hasAccess("add", "teacher") || $accessObject->hasAccess("add", "accountant") || $accessObject->hasAccess("add", "employee")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-staff">Employ Staff</a></li>
            <?php } ?>
        </ul>
    </li>   
    <?php if($isAdmin) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_report">Attendance Report</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php incidents_menu(); ?>
    <?php if($accessObject->hasAccess("view", "class")) { ?>
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Structures</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-classes">List Classes</a></li>
            <?php if($isAdmin) { ?>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>add-class">Add Class</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-departments">List Departments</a></li>
            <?php if($isAdmin) { ?>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>add-department">Add Department</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-sections">List Sections</a></li>
            <?php if($isAdmin) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-section">Add Section</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php if($isAdmin) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-course">Add Course</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Course Resources</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>e-learning_upload">Upload Resource</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if($isAdmin) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-clock"></i><span>Manage Timetable</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>timetable">Timetable</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>timetable-allocate">Allocate Timetable</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-rooms">Manage Rooms</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>timetable-view">View Timetable</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assessment">List Assessments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-assessment">Create Assessment</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>log-assessment">Log Assessment</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Reports / Promotion</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>results-upload">Upload / Manage Results</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
            <?php if($accessObject->hasAccess("promote", "promotion")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>promote-students">Promote Students</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <?php if($accessObject->hasAccess("view", "fees")) { ?>
    <li class="menu-header">HR / Finance</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-dolly-flatbed"></i><span>School Fees</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-history">List History</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-search">Search Log / Generate</a></li>
            <?php if($accessObject->hasAccess("receive", "fees")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-payment">Receive Payment</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("view", "fees_category")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-category">Fees Category</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("allocation", "fees")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-allocation">Fees Allocation</a></li>
            <?php } ?>            
        </ul>
    </li>
    <?php } ?>
    <?php if($accessObject->hasAccess("view", "payslip")) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>payroll">Payroll</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Payslip List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>payroll-category">Allowance Category</a></li>
        </ul>
    </li>
    <?php } ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-archway"></i><span>Simple Accounting</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("accounts", "accounting")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>accounts">Accounts</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>deposits">Deposits</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>expenses">Expenses</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>transactions">All Transactions</a></li>
            <?php if($accessObject->hasAccess("account_type_head", "accounting")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>account_type">Account Type Head</a></li>
            <?php } ?>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-chart-line"></i><span>Reports</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("reports", "fees")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-reports">Fees Report</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>accounting">Financial Reports</a></li>
            <?php if($accessObject->hasAccess("view", "payslip")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>payroll-reports">HR & Payroll</a></li>
            <?php } ?>
        </ul>
    </li>

    <li class="menu-header">Communication</li>
    <?php if($accessObject->hasAccess("update", "events")) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar-check"></i><span> Events Management</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-events">List Events</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-events-category">Events Category</a></li>
        </ul>
    </li>
    <?php } else { ?>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?php } ?>
    <?= communication_menu() ?>
<?php } ?>
<?php function admin_menu() { global $baseUrl; ?>
    <?php general_menu(true); ?>
<?php } ?>
<?php function accountant_menu() { global $baseUrl, $accessObject; ?>
    <?php general_menu(); ?>
<?php } ?>
<?php function teacher_menu() { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>My Students</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-student">Students List</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_report">Attendance Report</a></li>
        </ul>
    </li>
    <?php incidents_menu(); ?> 
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Course Resources</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Reports / Promotion</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>results-upload">Upload / Manage Results</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
            <?php if($accessObject->hasAccess("promote", "promotion")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>promote-students">Promote Students</a></li>
            <?php } ?>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
        </ul>
    </li>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assessment">List Assessments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-assessment">Create Assessment</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>log-assessment">Log Assessment</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <li class="menu-header">HR / Finance</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Payslip List</a></li>
        </ul>
    </li>
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?= communication_menu() ?>
<?php } ?>
<?php function parent_menu() { global $baseUrl, $accessObject, $session; ?>
    <?php if(!empty($session->student_id)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Logs</a></li>
        </ul>
    </li>                        
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
        </ul>
    </li>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assessment">List Assessments</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <?php } ?>
    <li class="menu-header">Finance</li>
    <li><a href="<?= $baseUrl ?>fees-history" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Fees History</span></a></li>
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?= communication_menu() ?>
<?php } ?>
<?php function student_menu() { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Logs</a></li>
        </ul>
    </li>                        
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
        </ul>
    </li>
    <?php if($accessObject->hasAccess("generate", "results")) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Reports / Promotion</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
        </ul>
    </li>
    <?php } ?>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assessment">List Assessments</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <li class="menu-header">Finance</li>
    <li><a href="<?= $baseUrl ?>fees-history" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Fees History</span></a></li>
    
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?= communication_menu() ?>
<?php } ?>
<?php function employee_menu() { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <li class="menu-header">HR / Finance</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Payslip List</a></li>
        </ul>
    </li>
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
<?php } ?>
<?php function support_menu() { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>Clients</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>clients_list">List All Clients</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>clients_register">Register New Client</a></li>
        </ul>
    </li>
<?php } ?>