<?php
/**
 * This class will hold all variables
 */
class Models {

    /** Set the variables */
    public $form_modules;
    public $favicon_array;
    public $resource_tables;
    public $resource_parameters;
    public $permission_denied;
    public $global_limit = 2500;
    public $formPreloader;
    public $sms_text_count = 155;
    public $underscores = "____";
    public $sms_sender = "MySchoolGH";

    // this is a simple quoted figure that certain queries are limited to
    public $temporal_maximum = 200;
    public $extreme_maximum = 10000;
    
    /** This is used for generation of user ids */
	public $append_zeros = 4;

    /** This is the allowed number of hours which a user can delete an object */
    public $allowed_delete_range = 3;

    /** The allowed maximum attachment size in megabytes */
	public $max_attachment_size = 25;
    public $accepted_attachment_file_types;

    // instantiate the class
    public function __construct() {

        // set the global base url
        $this->baseUrl = config_item('base_url');

        // set the accepted payment methods
        $this->payment_methods = [
            "cash" => "Cash",
            "cheque" => "Cheque",
            "momo_card" => "Mobile Money / Card Payment",
        ];

        // set the current timestamp
        $this->current_timestamp = date("Y-m-d H:i:s");

        // set the form modules to be used
        $this->form_modules = [
            "course_unit_form" => "Lesson Planner: Course Unit",
            "course_lesson_form" => "Lesson Planner: Unit Lesson",
            "course_lesson_form_view" => "Lesson Planner: Unit Lesson Details",
            "incident_log_form" => "Log Incident",
            "incident_log_form_view" => "Incident Details",
            "incident_log_followup_form" => "Incident: Followups",
            "modify_guardian_ward" => "Modify Guardian Ward",
            "modify_ward_guardian" => "Modify Ward Guardian", 
            "course_link_upload" => "Lesson Planner: Course Link Upload",
            "course_file_upload" => "Lesson Planner: Course File Upload",
            "upload_assignment" => "Create Assignment",
            "update_assignment" => "Update Assignment Details",
            "document_create_folder" => "Manage Folder / Directory",
            "document_create_file" => "Manage PDF Document",
            "document_update_folder" => "Manage Folder / Directory",
            "document_update_file" => "Manage Files / Document",
            "document_upload_files" => "Files Upload",
            "document_copy_command" => "Copy Documents",
            "document_move_command" => "Move Files & Folders",
            "multiple_document_copy" => "Copy Multiple Files & Folders",
            "multiple_document_move" => "Move Multiple Files & Folders"
        ];

        $this->event_audience = [
            "all" => "All Users",
            "student" => "Students",
            "teacher" => "Teaching Staff",
            "parent" => "Parents",
            "admin" => "Administrators & Accountants",
        ];

        // default file attachments list
        $this->fake_files = [
            "files" => [],
            "files_count" => 0,
            "files_size" => 0,
            "raw_size_mb" => 0
        ];

        // files to be uploaded favicon globals
		$this->favicon_array = [
			'jpg' => 'fa fa-file-image', 'png' => 'fa fa-file-image',
			'jpeg' => 'fa fa-file-image', 'gif' => 'fa fa-file-image',
            'pjpeg' => 'fa fa-file-image', 'webp' => 'fa fa-file-image',
			'pdf' => 'fa fa-file-pdf', 'doc' => 'fa fa-file-word',
			'docx' => 'fa fa-file-word', 'mp3' => 'fa fa-file-audio',
			'mpeg' => 'fa fa-file-video', 'mpg' => 'fa fa-file-video',
            'mp4' => 'fa fa-file-video', 'mov' => 'fa fa-file-video', 
            'movie' => 'fa fa-file-video', 'webm' => 'fa fa-file-video',
			'qt' => 'fa fa-file-video', 'zip' => 'fa fa-archive',
			'txt' => 'fa fa-file-alt', 'csv' => 'fa fa-file-csv',
			'rtf' => 'fa fa-file-alt', 'xls' => 'fa fa-file-excel',
			'xlsx' => 'fa fa-file-excel', 'php' => 'fa fa-file-alt',
			'css' => 'fa fa-file-alt', 'ppt' => 'fa fa-file-powerpoint',
			'pptx' => 'fa fa-file-powerpoint', 'sql' => 'fa fa-file-alt',
            'flv' => 'fa fa-file-video', 'json' => 'fa fa-file-alt'
		];

        // these are the list of accepted file types
        $this->accepted_attachment_file_types = [
            'jpg', 'png', 'jpeg', 'txt', 'pdf', 'docx', 'doc', 'xls', 'xlsx', 
            'mpeg', 'ppt', 'pptx', 'csv', 'gif', 'pub',  'mpg', 'flv', 'webm', 
            'movie', 'mov', 'pjpeg', 'webp', 'mp4', 'rtf'
        ];

        $this->user_roles_list = [
            "teacher" => "Teacher",
            "employee" => "Employee",
            "accountant" => "Accountant",
            "admin" => "Admin"
        ];

        $this->all_user_roles_list = [
            "student" => "Student",
            "teacher" => "Teacher",
            "parent" => "Parent",
            "employee" => "Employee",
            "accountant" => "Accountant",
            "admin" => "Admin"
        ];      

        $this->incident_user_role = [
            "student" => "Student",
            "teacher" => "Teacher",
            "employee" => "Employee",
            "accountant" => "Accountant",
            "admin" => "Admin"
        ];

        // these are the academic sessions variable
        // used at the settings page
        $this->academic_sessions = ["Term" => "TERMS", "Semester" => "SEMESTERS"];

        $this->the_user_roles = [
            "parent" => [
                "_role_title" => "Parent",
                "report_key" => "parents_report",
                "link" => "guardian"
            ],
            "student" => [
                "_role_title" => "Student",
                "report_key" => "students_report",
                "link" => "student"
            ],
            "employee" => [
                "_role_title" => "Employee",
                "report_key" => "employees_report",
                "link" => "staff"
            ],
            "accountant" => [
                "_role_title" => "Accountant",
                "report_key" => "accountants_report",
                "link" => "staff"
            ],
            "teacher" => [
                "_role_title" => "Teacher",
                "report_key" => "teachers_report",
                "link" => "staff"
            ],
            "admin" => [
                "_role_title" => "Admin User",
                "report_key" => "admins_report",
                "link" => "staff"
            ]
        ];

        $this->user_status_list = [
            "0" => "Active",
            "1" => "Inactive"
        ];

        // unexpected error messages
        $this->unexpected_error = ["code" => 203, "data" => "Sorry! An unexpected error occured.\nPlease contact the admin if problem persists"];

		$this->permission_denied = "Sorry! You do not have the required permission to perform this action.";

        $this->swal_notification = [
            "ajax_error" => "Sorry! There is an error while processing the request.",
            "cancel_promotion_log" => "Are you sure you want to cancel this promotion log. You can perform the operation again once cancelled.",
            "validate_promotion_log" => "Are you sure you want to validate this promotion. Once approved you cannot effect any changes.",
            "end_academic_term" => "Are you sure you want to end this academic term. You will be locked out from the system for not more than 10 minute to allow processing? Once confirmed, you cannot reverse the action."
        ];

        $this->assessment_group = ["Classwork", "Homework", "GroupWork", "Quiz"];

        $this->assessment_color_group = [
            "Classwork" => "success",
            "Homework" => "warning",
            "GroupWork" => "secondary",
            "Quiz" => "primary",
            "MidTerm" => "dark"
        ];

        $this->support_sections = [
            "Account" => "Account Setup",
            "Account_Activation" => "Account Activation",
            "Account_Suspension" => "Account Suspension",
            "Students" => "Students",
            "Students_Fees_Allocation" => "Student > Fees Allocation",
            "Students_Fees_Payment" => "Student > Fees Payment",
            "Student_Incidents" => "Student > Incidents",
            "Courses" => "Courses",
            "Courses_Lesson_Planner" => "Courses > Lesson Planner",
            "Courses_Materials" => "Courses > Materials",
            "Courses_Downloads" => "Courses > Download",
            "Attendance_Log" => "Attendance > Log",
            "Attendance_Report" => "Attendance > Report",
            "Payments" => "Payments",
            "Timetable" => "Timetable",
            "Timetable_Generation" => "Timetable > Generation",
            "Timetable_Allocation" => "Timetable > Allocation",
            "Timetable_Download" => "Timetable > Download",
            "Assessments_List" => "Assessments List",
            "Assessments_Log" => "Assessments > Log",
            "Assessments_Creation" => "Assessments > Creation",
            "Assessments_Submission" => "Assessments > Submission",
            "Library_Management" => "Library Management",
            "Payroll_Management" => "Payroll Management",
            "Events_Management" => "Events Management",
            "Simple_Accounting" => "Simple Accounting",
        ];

        // app features list
        $this->features_list = [
            "library" => "Library Management",
            "e_learning" => "E-Learning",
            "class_assessment" => "Online Class Assessment",
            "reports_promotion" => "Terminal Reports / Promotion",
            "attendance" => "Attendance Manager",
            "timetable" => "Timetable",
            "payroll" => "Payroll Manager",
            "bus_manager" => "Bus Manager",
            "documents_manager" => "Documents Management",
            "inventory" => "Inventory Manager",
            "bulk_action" => "Bulk Action",
            "live_chat" => "Live Chat"            
        ];

        // quick student search form
        $this->quick_student_search_form = '
        <div class="mb-3" id="student_search_input">
            <label>Filter by Student Name or Reg. ID</label>
            <input type="search" placeholder="Search by fullname" name="student_fullname" class="form-control">
            <input type="hidden" value="true" name="auto_search">
        </div>';

        $this->readonly_mode = ["code" => 203, "data" => "Sorry! You are currently in a readonly mode hence cannot perform the request.\nLeave current mode to continue."];

        // these are the global error logs to be displayed on the pages
        $this->error_logs = [
            "account_not_set" => [
                "msg" => "Sorry! You have not setup a <strong>default payment account</strong> yet. It must first be setup before you can proceed to receive / issue payments.",
                "link" => '<button onclick="return loadPage(\''.$this->baseUrl.'accounts\');" class="btn anchor btn-warning mt-4">Setup Account</button>'
            ],
            "fees_category_not_set" => [
                "msg" => "Sorry! You have not setup the <strong>fees category list</strong> yet. Kindly set it up to proceed.",
                "link" => '<button onclick="return loadPage(\''.$this->baseUrl.'fees-category\');" class="btn anchor btn-warning mt-4">Setup Fees Category</button>'
            ], 
            "class_not_set" => [
                "msg" => "Sorry! You have not setup the <strong>classes list</strong> yet. Kindly set it up to proceed.",
                "link" => '<button onclick="return loadPage(\''.$this->baseUrl.'class_add\');" class="btn anchor btn-warning mt-4">Add New Class</button>'
            ],
            "readonly_mode" => [
                "msg" => $this->readonly_mode["data"],
                "link" => '<button onclick="return set_academic_year_term(\'revert\',\'revert\');" class="btn anchor btn-warning mt-4">Exit Review Mode</button>'
            ],
        ];

        // statuses that are allowed to login
        $this->allowed_login_status = ["Pending", "Active", "Graduated", "On Leave"];

        // the list statuses for students
        $this->student_statuses = ["Active", "Graduated", "Transferred", "Suspended", "Dismissed"];

        // this list will be used to query students list
        $this->default_allowed_status_users_list = "'Active','Suspended'";
        $this->default_allowed_status_users_array = ['Active','Suspended'];

        // the list of user status to list without a query
        $this->default_not_allowed_status_users_list = "'Deleted','Graduated','Transferred','Dismissed'";

        // User Statuses for Staff Members
        $this->staff_statuses = ["Active", "On Leave", "Transferred", "Suspended", "Dismissed"];

        // all available user status
        $this->default_statuses_list = ["Active", "Pending", "Deleted", "On Leave", "Graduated", "Transferred", "Suspended", "Dismissed"];

        // This is used to specify whether groupwork can be added to the lesson assessment panel and also exported.
        $this->append_groupwork_to_assessment = false;
    }

    public function accepted_period($n_session = "Term") {
        $this->accepted_period = [
            "this_term" => [
                "title" => "This {$n_session}",
                "alt" => [
                    "key" => "last_term",
                    "value" => "Last {$n_session}"
                ]
            ],
            "last_term" => [
                "title" => "Last {$n_session}",
                "alt" => [
                    "key" => "last_term",
                    "value" => "Last {$n_session}"
                ]
            ],
            "yesterday" => [
                "title" => "Yesterday",
                "alt" => [
                    "key" => "last_2days",
                    "value" => "Last 2 Days"
                ]
            ],
            "today" => [
                "title" => "Today",
                "alt" => [
                    "key" => "yesterday",
                    "value" => "Yesterday"
                ]
            ],
            "this_week" => [
                "title" => "This Week",
                "alt" => [
                    "key" => "last_week",
                    "value" => "Last Week"
                ]
            ], 
            "last_week" => [
                "title" => "Last Week",
                "alt" => [
                    "key" => "last_14days",
                    "value" => "Last"
                ]
            ], 
            "last_14days" => [
                "title" => "Last 14 Days",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "last_30days" => [
                "title" => "Last 30 Days",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "this_month" => [
                "title" => "This Month",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "last_month" => [
                "title" => "Last Month",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "last_3months" => [
                "title" => "Last 3 Months",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "last_6months" => [
                "title" => "Last 6 Months",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ], 
            "this_year" => [
                "title" => "This Year",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ],
            "last_year" => [
                "title" => "Last Year",
                "alt" => [
                    "key" => "",
                    "value" => ""
                ]
            ],
        ];

        return $this;
    }

    /**
     * Required Field
     * 
     * @return String
     */
    public function is_required($item = null) {
        return "Sorry! {$item} is required and cannot be empty.";
    }   


}
?>