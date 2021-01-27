<?php function general_menu($isAdmin = false) { global $baseUrl; ?>
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
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">List Attendance</a></li>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
        </ul>
    </li>                        
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
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
    <?php if($isAdmin) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-course">Add Course</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Course Resources</a></li>
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
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Assignments</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assignments">List Assignments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-assignment">Create Assignment</a></li>
        </ul>
    </li>
    <?php } else { ?>
    <li><a href="<?= $baseUrl ?>timetable" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <?php } ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <li class="menu-header">HR / Finance</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-dolly-flatbed"></i><span>Fees</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-history">List History</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-payment">Receive Payment</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-category">Fees Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-allocation">Fees Allocation</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-reports">Reports</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-payroll">Payroll</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-payslip">Payslip List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-category">Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-expenditure">Expenditure</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-reports">Reports</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-tie"></i><span>HR</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-leave_manager">Leave Management</a></li>
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
<?php } ?>
<?php function admin_menu() { global $baseUrl; ?>
    <?php general_menu(true); ?>
<?php } ?>
<?php function accountant_menu() { global $baseUrl; ?>
    <?php general_menu(); ?>
<?php } ?>
<?php function teacher_menu() { global $baseUrl; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>My Students</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-student">Students List</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">List Attendance</a></li>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
        </ul>
    </li>                        
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-classes">List Classes</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-departments">List Departments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-sections">List Sections</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Lesson Planner</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-courses">List Courses</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Course Resources</a></li>
        </ul>
    </li>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Assignments</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assignments">List Assignments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>add-assignment">Create Assignment</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
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
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-payroll">Payroll</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-history">List History</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-leave_manager">Leave Management</a></li>
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
<?php } ?>
<?php function parent_menu() { global $baseUrl, $session; ?>
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
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Assignments</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assignments">List Assignments</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
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
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Event Management</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-envelope"></i><span>Emails</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-emails">List Mails</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>compose-email">Compose</a></li>
        </ul>
    </li>
<?php } ?>
<?php function student_menu() { global $baseUrl; ?>
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
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Assignments</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-assignments">List Assignments</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-books-category">Books Category</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>issued-books">Issued Books</a></li>
        </ul>
    </li>
    <li class="menu-header">Finance</li>
    <li><a href="<?= $baseUrl ?>fees-history" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Fees History</span></a></li>
    
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Event Management</span></a></li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-envelope"></i><span>Emails</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-emails">List Mails</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>compose-email">Compose</a></li>
        </ul>
    </li>
<?php } ?>
<?php function employee_menu() { global $baseUrl; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">List Attendance</a></li>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
        </ul>
    </li>                        
    <li class="menu-header">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Academics</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>list-classes">List Classes</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-departments">List Departments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-sections">List Sections</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Library</span></a>
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
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-payroll">Payroll</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-history">List History</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>hr-leave_manager">Leave Management</a></li>
        </ul>
    </li>
    <li class="menu-header">Communication</li>
    <li><a href="<?= $baseUrl ?>list-events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Event Management</span></a></li>
<?php } ?>