<?php function communication_menu() { global $baseUrl, $accessObject; ?>
    <?php if($accessObject->hasAccess("manage", "communication")) { ?>
        <li class="dropdown" data-parent_menu="communication">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-envelope"></i><span>Emails & SMS</span></a>
            <ul class="dropdown-menu">
                <?php if($accessObject->hasAccess("templates", "communication")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>sms_template">SMS Templates</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>email_template">Email Templates</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("send", "communication")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>sms_send">Send SMS</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>email_send">Send Email</a></li>
                <?php } ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>smsemail_report">SMS / Email Reports</a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
<?php function exeats_menu() { global $baseUrl, $clientFeatures, $accessObject; ?>
    <?php if(in_array("exeats", $clientFeatures)) { ?>
        <li class="menu-header text-black">Exeats Management</li>
        <li class="dropdown" data-parent_menu="exeats-management">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-dolly-flatbed"></i><span>Manage Exeats</span></a>
            <ul class="dropdown-menu">
                <?php if($accessObject->hasAccess("manage", "exeats")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>exeats">Exeats Dashboard</a></li>
                <?php } ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>exeats_log">Exeats Logs</a></li>
            </ul>
        </li>
    <?php } ?>
<?php } ?>
<?php function general_menu($isAdmin = false) { global $baseUrl, $accessObject, $clientFeatures, $academicSession, $isReadOnly; ?>
    <li class="dropdown" data-parent_menu="students">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>Students</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>students">Students List</a></li>
            <?php if($accessObject->hasAccess("add", "student") && !$isReadOnly) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>student_add">New Admission</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php if($accessObject->hasAccess("view", "guardian") || $accessObject->hasAccess("view", "delegates")) { ?>
    <li class="dropdown" data-parent_menu="guardians">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-clock"></i><span>Guardians</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("view", "guardian")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>guardians">Guardian List</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("view", "delegates")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>delegates">Manage Delegates</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <li class="dropdown" data-parent_menu="staffs">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>Staff</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>staffs">Staff List</a></li>
            <?php if(($accessObject->hasAccess("add", "teacher") || $accessObject->hasAccess("add", "accountant") || $accessObject->hasAccess("add", "employee")) && !$isReadOnly) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>staff_add">Add Staff</a></li>
            <?php } ?>
            <?php if(in_array("incidents", $clientFeatures)) { ?>
                <!-- <li><a class="nav-link" href="<?= $baseUrl ?>staff_weekly_reports">Weekly Reports</a></li> -->
            <?php } ?>
        </ul>
    </li>   
    <?php if($isAdmin) { ?>
    <?php if(in_array("attendance", $clientFeatures)) { ?>
    <li class="dropdown" data-parent_menu="attendance">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
            <?php if(!$isReadOnly) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance_report">Attendance Report</a></li>
            <?php if($accessObject->hasAccess("review", "attendance")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>attendance_log_history">Attendance Log History</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php } ?>
    <?php if(in_array("bulk_action", $clientFeatures)) { ?>
        <?php if(!$accessObject->hasAccess("change_status", "settings") && 
                !$accessObject->hasAccess("assign_class", "settings") && 
                !$accessObject->hasAccess("assign_section", "settings") &&
                !$accessObject->hasAccess("assign_department", "settings") &&
                !$accessObject->hasAccess("modify_student", "settings")
        ) { ?>

        <?php } else { ?> 
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i><span>Bulk Action</span></a>
            <ul class="dropdown-menu">
                <?php if($accessObject->hasAccess("change_status", "settings")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>assign_status">Modify Student State</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("assign_class", "settings")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>assign_class">Assign Class</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>reassign_class">Re-Assign Class</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("assign_department", "settings")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>assign_department">Assign Departments</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("assign_section", "settings")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>assign_section">Assign Sections</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("modify_student", "settings")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>assign_modify_student">Modify Student Record</a></li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    <?php } ?>
    <?php if($accessObject->hasAccess("view", "class")) { ?>
    <li class="menu-header text-black">Academics</li>
    <li class="dropdown" data-parent_menu="classes">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-graduation-cap"></i><span>Class & Departments</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>classes">List Classes</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>departments">List Departments</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>sections">List Sections</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if($isAdmin) { ?>
        <li class="dropdown" data-parent_menu="courses">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Subjects Manager</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>courses">List Subjects</a></li>
                <?php if(!$isReadOnly) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>course_add">Add Subject</a></li>
                <?php } ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Subjects Resources</a></li>
            </ul>
        </li>
        <?php if(in_array("e_learning", $clientFeatures)) { ?>
            <li class="dropdown" data-parent_menu="e-learning">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
                    <?php if(!$isReadOnly) { ?>
                        <li><a class="nav-link" href="<?= $baseUrl ?>e-learning_upload">Upload Resource</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    <?php } ?>
    <?php if($isAdmin) { ?>
        <?php if(in_array("timetable", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="timetable">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-clock"></i><span>Manage Timetable</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>timetable">Timetable</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>timetable-allocate">Allocate Timetable</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>list-rooms">Manage Rooms</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>timetable-view">View Timetable</a></li>
            </ul>
        </li>
        <?php } ?>
        <?php if(in_array("class_assessment", $clientFeatures)) { ?>
            <li class="dropdown" data-parent_menu="assessments">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= $baseUrl ?>assessments">List Assessments</a></li>
                    <?php if(!$isReadOnly) { ?>
                        <li><a class="nav-link" href="<?= $baseUrl ?>add-assessment">Create Assessment</a></li>
                        <li><a class="nav-link" href="<?= $baseUrl ?>log-assessment">Log Previous Assessment</a></li>
                    <?php } ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>gradebook">Gradebook</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if(in_array("reports_promotion", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="reports-upload">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Reports / Promotion</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>results-upload">Upload / Manage Results</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
                <?php if($accessObject->hasAccess("promote", "promotion")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>promotions">Students Promotion</a></li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    <?php } ?>
    <?php if(in_array("library", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="library">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>books_categories">Books Collection</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>books">Books List</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>books_issued">Issued Books</a></li>
                <?php if($accessObject->hasAccess("add", "library")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>books_stock">Stock Update</a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>

    <?php if($accessObject->hasAccess("view", "fees")) { ?>
    <li class="menu-header text-black">Finance & Accounting</li>
    <li class="dropdown" data-parent_menu="fees-setup">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-server"></i><span>Fees Setup</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("view", "fees_category")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-category">Fees Category</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("allocation", "fees")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-allocation">Fees Allocation</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>term_bills">Manage <?= $academicSession; ?> Bills</a></li>
                <!-- <li><a class="nav-link" href="<?= $baseUrl ?>fees-discount-policies">Discount Policies</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-student-discounts">Student Discounts</a></li> -->
            <?php } ?>
        </ul>
    </li>
    
    <li class="dropdown" data-parent_menu="fees-payment">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-door-open"></i><span>Payment & History</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("receive", "fees") || $accessObject->hasAccess("receive", "fees")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-payment">Collect Fees</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>arrears/apay">Arrears Payment</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>fees-history">List Payment History</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>debtors">Debtors List</a></li>
            <?php if($accessObject->hasAccess("send", "communication")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>reminders/send">Send Reminder</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("reports", "fees")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>fees-reports">Fees Report</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <li class="dropdown" data-parent_menu="accounting">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-archway"></i><span>Accounting</span></a>
        <ul class="dropdown-menu">
            <?php if($accessObject->hasAccess("account_type_head", "accounting")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>account_type">Chart of Accounts</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>incomes">Incomes</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>expenses">Expenses</a></li>
            <?php if($accessObject->hasAccess("deposits", "accounting")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>bank_deposits">Bank Deposits</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("bank_withdrawal", "accounting")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>bank_withdrawals">Bank Withdrawals</a></li>
            <?php } ?>
            <?php if($accessObject->hasAccess("accounts", "accounting")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>accounts">Bank & Cash Accounts</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>transactions">Transactions History</a></li>
            <?php if($accessObject->hasAccess("reports", "accounting")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>accounting">Financial Reports</a></li>
            <?php } ?>
        </ul>
    </li>
    <li class="menu-header text-black">Human Resource</li>
    <?php if($accessObject->hasAccess("view", "id_cards")) { ?>
        <li class="dropdown" data-parent_menu="id-cards">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-qrcode"></i><span>ID Cards Setup</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>card_generated">Generated Cards</a></li>
                <?php if($accessObject->hasAccess("settings", "id_cards")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>card_settings">ID Card Settings</a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
    <?php if($accessObject->hasAccess("view", "payslip")) { ?>
        <?php if(in_array("payroll", $clientFeatures)) { ?>
            <li class="dropdown" data-parent_menu="payroll">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= $baseUrl ?>payroll-category">Payroll Setup</a></li>
                    <li><a class="nav-link" href="<?= $baseUrl ?>payroll">Staff Payroll List</a></li>
                    <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Payslip List</a></li>
                    <?php if($accessObject->hasAccess("reports", "payslip")) { ?>
                        <li><a class="nav-link" href="<?= $baseUrl ?>payroll-reports">Payroll Reports</a></li>
                        <li><a class="nav-link" href="<?= $baseUrl ?>payroll-ssnit">SSNIT Contributions</a></li>
                        <li><a class="nav-link" href="<?= $baseUrl ?>payroll-taxes">Employee PAYE</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    <?php } ?>
    <?php if(in_array("front_office", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "admission_enquiry") || 
            $accessObject->hasAccess("view", "visitor_book") || $accessObject->hasAccess("view", "phone_call_log") ||
            $accessObject->hasAccess("view", "postal_dispatch") || $accessObject->hasAccess("view", "postal_receive")) { ?>
            <li class="dropdown" data-parent_menu="front-office">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-database"></i><span>Front Office</span></a>
                <ul class="dropdown-menu">
                    <?php if($accessObject->hasAccess("view", "admission_enquiry")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>office_enquiry">Admission Enquiry</a></li>
                    <?php } ?>
                    <?php if($accessObject->hasAccess("view", "visitor_book")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>office_visitors">Visitors Book</a></li>
                    <?php } ?>
                    <?php if($accessObject->hasAccess("view", "phone_call_log")) { ?>
                    <!-- <li><a class="nav-link" href="<?= $baseUrl ?>office_phonecall">Phone Call Log</a></li> -->
                    <?php } ?>
                    <?php if($accessObject->hasAccess("view", "postal_dispatch")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>office_postaldispatch">Postal Dispatch</a></li>
                    <?php } ?>
                    <?php if($accessObject->hasAccess("view", "postal_receive")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>office_postalreceive">Postal Receive</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    <?php } ?>
    <?php if(in_array("leave", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>leave" class="nav-link"><i class="far fa-check-square"></i><span>Leave Applications</span></a></li>
    <?php } ?>
    <?php if(in_array("documents_manager", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "documents") || $accessObject->hasAccess("add", "documents")) { ?>
            <li class="dropdown">
                <a href="<?= $baseUrl ?>documents" class="nav-link"><i class="fas fa-folder"></i><span>My Documents</span></a>
            </li>
        <?php } ?>
    <?php } ?>
    <?php if(in_array("bus_manager", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="shuttle-service">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-bus-alt"></i><span>Shuttle Service</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>buses">Manage Shuttles</a></li>
                <?php if($accessObject->hasAccess("bus_log", "attendance") && in_array("qr_code_scanner", $clientFeatures)) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>buses_attendance">Shuttle Attendance</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("financials", "buses")) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>bus_financials">Shuttle Financials</a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
    <?php if($isAdmin && in_array("online_applications", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="online-applications">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-friends"></i><span>Online Applications</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>applications">Application List</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>application_forms">Application Forms</a></li>
                <?php if($isAdmin) { ?>
                    <li><a class="nav-link" href="<?= $baseUrl ?>application_api_keys">Manage API Keys</a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
    <?= exeats_menu() ?>
    <li class="menu-header text-black">Communication</li>
    <?= communication_menu() ?>
    <?php if(in_array("events", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("update", "events")) { ?>
        <li class="dropdown" data-parent_menu="events-management">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar-check"></i><span> Events Management</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>events">List Events</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>events_category">Events Category</a></li>
            </ul>
        </li>
        <?php } else { ?>
        <li><a href="<?= $baseUrl ?>events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
        <?php } ?>
    <?php } ?>
<?php } ?>
<?php function admin_menu() { ?>
    <?php general_menu(true); ?>
<?php } ?>
<?php function accountant_menu() {?>
    <?php general_menu(); ?>
<?php } ?>
<?php function teacher_menu() { global $baseUrl, $accessObject, $clientFeatures, $isReadOnly; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-graduate"></i><span>My Students</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>students">Students List</a></li>
            
        </ul>
    </li>
    <?php if(in_array("attendance", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
            <?php if(!$isReadOnly) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>attendance_log">Log Attendance</a></li>
            <?php } ?>
            <li><a class="nav-link border-bottom" href="<?= $baseUrl ?>attendance_report">Attendance Report</a></li>
        </ul>
    </li>
    <?php } ?>
    <li class="menu-header text-black">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Subjects Manager</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>courses">List Subjects</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Subject Resources</a></li>
        </ul>
    </li>
    <?php if(in_array("e_learning", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
            <?php if(!$isReadOnly) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>e-learning_upload">Upload Resource</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("class_assessment", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>assessments">List Assessments</a></li>
            <?php if(!$isReadOnly) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>add-assessment">Create Assessment</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>log-assessment">Log Previous Assessment</a></li>
            <?php } ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>gradebook">Gradebook</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("reports_promotion", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Reports / Promotion</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>results-upload">Upload / Manage Results</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
            <?php if($accessObject->hasAccess("promote", "promotion")) { ?>
            <li><a class="nav-link" href="<?= $baseUrl ?>promotions">Students Promotion</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("timetable", $clientFeatures)) { ?>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <?php } ?>
    <?php if(in_array("library", $clientFeatures)) { ?>
    <li class="dropdown" data-parent_menu="library">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>books_categories">Books Collection</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books_issued">Issued Books</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("documents_manager", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "documents") || $accessObject->hasAccess("add", "documents")) { ?>
            <li class="dropdown">
                <a href="<?= $baseUrl ?>documents" class="nav-link"><i class="fas fa-folder"></i><span>My Documents</span></a>
            </li>
        <?php } ?>
    <?php } ?>
    <li class="menu-header text-black">Finance / HR Management</li>
    <?php if(in_array("payroll", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Staff Payslip List</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("leave", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>leave" class="nav-link"><i class="far fa-check-square"></i><span>Leave Applications</span></a></li>
    <?php } ?>
    <?php if(in_array("exeats", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "exeats")) { ?>
            <li><a href="<?= $baseUrl ?>exeats_log" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Exeats Manager</span></a></li>
        <?php } ?>
    <?php } ?>
    <li class="menu-header text-black">Communication</li>
    <?php if(in_array("events", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?php } ?>
    <?= communication_menu() ?>
<?php } ?>
<?php function parent_menu() { global $baseUrl, $accessObject, $session, $clientFeatures; ?>
    <?php if(in_array("incidents", $clientFeatures)) { ?>
        <li>
            <a href="<?= $baseUrl ?>student_reports" class="nav-link"><i class="fas fa-weight"></i><span>Incident Reports</span></a>
        </li>
    <?php } ?>
    <?php if(!empty($session->student_id)) { ?>
        <?php if(in_array("attendance", $clientFeatures)) { ?>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
                    <li><a class="nav-link" href="<?= $baseUrl ?>attendance_history">Attendance History</a></li>
                    <li><a class="nav-link" href="<?= $baseUrl ?>buses_attendance">Bus Attendance History</a></li>
                </ul>
            </li>
        <?php } ?>
        <li class="menu-header text-black">Academics</li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Subjects Manager</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>courses">List Subjects</a></li>
            </ul>
        </li>
        <?php if(in_array("timetable", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
        <?php } ?>
        <?php if(in_array("class_assessment", $clientFeatures)) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>assessments">List Assessments</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>gradebook">Gradebook</a></li>
            </ul>
        </li>
        <?php } ?>
    <?php } ?>
    <li><a class="nav-link" href="<?= $baseUrl ?>delegates"><i class="fas fa-users"></i> Manage Delegates</a></li>
    <?php if(in_array("library", $clientFeatures)) { ?>
    <li class="dropdown" data-parent_menu="library">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>books_categories">Books Collection</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books_issued">Issued Books</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("class_assessment", $clientFeatures)) { ?>
       <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Terminal Reports</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
            </ul>
        </li>
    <?php } ?>
    <li class="menu-header text-black">Finance</li>
    <li><a href="<?= $baseUrl ?>fees-history" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Fees History</span></a></li>
    <?php if(in_array("exeats", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>exeats_log" class="nav-link"><i class="fas fa-address-card"></i><span>Exeats Manager</span></a></li>
    <?php } ?>
    <li class="menu-header text-black">Communication</li>
    <?php if(in_array("events", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?php } ?>
    <?= communication_menu() ?>
<?php } ?>
<?php function student_menu() { global $baseUrl, $accessObject, $clientFeatures, $academicSession; ?>
    <?php if(in_array("class_assessment", $clientFeatures)) { ?>
        <li class="dropdown hidden">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-bookmark"></i><span>Exams Bank</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>exams_dashboard">Summary</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>exams_quizes">Available Questions</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>exams_scheduled">Scheduled Quizes</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>exams_performance">My Performance</a></li>
            </ul>
        </li>
    <?php } ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Logs</a></li>
        </ul>
    </li>                    
    <li class="menu-header text-black">Academics</li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book"></i><span>Subjects Manager</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>courses">List Subjects</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>list-resources">Subjects Resources</a></li>
        </ul>
    </li>
    <?php if(in_array("e_learning", $clientFeatures)) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-open"></i><span>E-Learning</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>e-learning">E-Books / Videos</a></li>
            </ul>
        </li>
    <?php } ?>
    <?php if(in_array("timetable", $clientFeatures)) { ?>
    <li><a href="<?= $baseUrl ?>timetable-view" class="nav-link"><i class="fas fa-clock"></i><span>Timetable</span></a></li>
    <?php } ?>
    <?php if(in_array("class_assessment", $clientFeatures)) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Class Assessment</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>assessments">List Assessments</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>gradebook">Grade Book</a></li>
            </ul>
        </li>
    <?php } ?>
    <?php if(in_array("library", $clientFeatures)) { ?>
        <li class="dropdown" data-parent_menu="library">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>books_categories">Books Collection</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>books">Books List</a></li>
                <li><a class="nav-link" href="<?= $baseUrl ?>books_issued">Issued Books</a></li>
            </ul>
        </li>
    <?php } ?>
    <?php if(in_array("class_assessment", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("generate", "results")) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-project-diagram"></i><span>Terminal Reports</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?= $baseUrl ?>results-generate">Generate Report</a></li>
            </ul>
        </li>
        <?php } ?>
    <?php } ?>
    <li class="menu-header text-black">Finance</li>
    <li><a href="<?= $baseUrl ?>fees-history" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Fees History</span></a></li>
    <li><a href="<?= $baseUrl ?>fees_bill" class="nav-link"><i class="fas fa-money-bill"></i><span>This <?= $academicSession; ?> Bill</span></a></li>
    <?php if(in_array("exeats", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "exeats") || $accessObject->hasAccess("add", "exeats")) { ?>
            <li><a href="<?= $baseUrl ?>exeats_log" class="nav-link"><i class="fas fa-dolly-flatbed"></i><span>Exeats Manager</span></a></li>
        <?php } ?>
    <?php } ?>
    <li class="menu-header text-black">Communication</li>
    <?php if(in_array("events", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?php } ?>
    <?= communication_menu() ?>
<?php } ?>
<?php function employee_menu() { global $baseUrl, $accessObject, $clientFeatures; ?>
    <?php if(in_array("attendance", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-ticket-alt"></i><span>Attendance</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>attendance">Attendance Summary</a></li>
            <?php if($accessObject->hasAccess("bus_log", "attendance")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>bus_logs">Bus Attendance</a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("library", $clientFeatures)) { ?>
    <li class="dropdown" data-parent_menu="library">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-landmark"></i><span>Library</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>books_categories">Books Collection</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books">Books List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>books_issued">Issued Books</a></li>
        </ul>
    </li>
    <?php } ?>
    <li class="menu-header text-black">Finance / HR Management</li>
    <?php if(in_array("front_office", $clientFeatures)) { ?>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-database"></i><span>Front Office</span></a>
            <ul class="dropdown-menu">
                <?php if($accessObject->hasAccess("view", "admission_enquiry")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>office_enquiry">Admission Enquiry</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("view", "visitor_book")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>office_visitors">Visitors Book</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("view", "phone_call_log")) { ?>
                <!-- <li><a class="nav-link" href="<?= $baseUrl ?>office_phonecall">Phone Call Log</a></li> -->
                <?php } ?>
                <?php if($accessObject->hasAccess("view", "postal_dispatch")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>office_postaldispatch">Postal Dispatch</a></li>
                <?php } ?>
                <?php if($accessObject->hasAccess("view", "postal_receive")) { ?>
                <li><a class="nav-link" href="<?= $baseUrl ?>office_postalreceive">Postal Receive</a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
    <?php if(in_array("payroll", $clientFeatures)) { ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-desktop"></i><span>Payroll</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>payslips">Staff Payslip List</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if(in_array("leave", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>leave" class="nav-link"><i class="far fa-check-square"></i><span>Leave Applications</span></a></li>
    <?php } ?>
    <?php if(in_array("documents_manager", $clientFeatures)) { ?>
        <?php if($accessObject->hasAccess("view", "documents") || $accessObject->hasAccess("add", "documents")) { ?>
            <li class="dropdown">
                <a href="<?= $baseUrl ?>documents" class="nav-link"><i class="fas fa-folder"></i><span>My Documents</span></a>
            </li>
        <?php } ?>
    <?php } ?>
    <li class="menu-header text-black">Communication</li>
    <?php if(in_array("events", $clientFeatures)) { ?>
        <li><a href="<?= $baseUrl ?>events" class="nav-link"><i class="fas fa-calendar-check"></i><span>Events</span></a></li>
    <?php } ?>
<?php } ?>
<?php function support_menu() { global $baseUrl, $accessObject; ?>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-users"></i><span>List Users</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>users">Manage Users</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>support_access_manager">Access Control</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>support_security_logs">Security Logs</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="<?= $baseUrl ?>support"><i class="fas fa-phone-volume"></i><span>Support Tickets</span></a>
    </li>
    <?php if($accessObject->hasAccess("payment_transactions", "settings")) { ?>
    <li class="dropdown">
        <a href="<?= $baseUrl ?>payment_transactions">
            <i class="fas fa-money-bill"></i><span>Payment Transactions</span>
        </a>
    </li>
    <?php } ?>
    <li class="dropdown">
        <a href="<?= $baseUrl ?>endpoints"><i class="fas fa-code"></i><span>API Endpoints</span></a>
    </li>
    <li class="dropdown">
        <a href="<?= $baseUrl ?>packages"><i class="fas fa-yin-yang"></i><span>Client Packages</span></a>
    </li>
    <li class="dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-book-reader"></i><span>Knowledgebase</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="<?= $baseUrl ?>knowledgebase">Articles List</a></li>
            <li><a class="nav-link" href="<?= $baseUrl ?>article">Add Article</a></li>
        </ul>
    </li>
<?php } ?>