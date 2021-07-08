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
    public $global_limit = 2000;
    public $formPreloader;
    public $sms_text_count = 155;
    public $underscores = "____";
    public $sms_sender = "MySchoolGH";

    /** This is used for generation of user ids */
	public $append_zeros = 5;

    /** This is the allowed number of hours which a user can delete an object */
    public $allowed_delete_range = 3;

    /** The allowed maximum attachment size in megabytes */
	public $max_attachment_size = 25;
    public $accepted_attachment_file_types;

    // instantiate the class
    public function __construct() {

        $this->payment_methods = [
            "cash" => "Cash",
            "cheque" => "Cheque",
            "momo_card" => "Mobile Money / Card Payment",
        ];

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
        ];

        $this->accepted_period = [
            "this_term" => [
                "title" => "This Term",
                "alt" => [
                    "key" => "last_term",
                    "value" => "Last Term"
                ]
            ],
            "last_term" => [
                "title" => "Last Term",
                "alt" => [
                    "key" => "last_term",
                    "value" => "Last Term"
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

        $this->event_audience = [
            "all" => "All Users",
            "student" => "Students",
            "teacher" => "Teaching Staff",
            "parent" => "Parents"
        ];

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

        $this->unexpected_error = ["code" => 203, "data" => "Sorry! An unexpected error occured.\nPlease contact the admin if problem persists"];

		$this->accepted_attachment_file_types = [
			'jpg', 'png', 'jpeg', 'txt', 'pdf', 'docx', 'doc', 
            'xls', 'xlsx', 'mpeg', 'ppt', 'pptx', 'csv', 'gif', 
            'pub',	'mpg', 'flv', 'webm', 'movie', 'mov', 
            'pjpeg', 'webp', 'mp4'
		];

        $this->fake_files = [
            "files" => [],
            "files_count" => 0,
            "files_size" => 0,
            "raw_size_mb" => 0
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

        $this->the_user_roles = [
            "parent" => [
                "_role_title" => "Parent",
                "report_key" => "parents_report"
            ],
            "student" => [
                "_role_title" => "Student",
                "report_key" => "students_report"
            ],
            "employee" => [
                "_role_title" => "Employee",
                "report_key" => "employees_report"
            ],
            "accountant" => [
                "_role_title" => "Accountant",
                "report_key" => "accountants_report"
            ],
            "teacher" => [
                "_role_title" => "Teacher",
                "report_key" => "teachers_report"
            ],
            "admin" => [
                "_role_title" => "Admin User",
                "report_key" => "admins_report"
            ]
        ];

        $this->user_status_list = [
            "0" => "Active",
            "1" => "Inactive"
        ];

		$this->permission_denied = "Sorry! You do not have the required permission to perform this action.";
        
        // set the javascript swal notifications
        $this->swal_notification = [
            "ajax_error" => "Sorry! There is an error while processing the request.",
            "cancel_promotion_log" => "Are you sure you want to cancel this promotion log. You can perform the operation again once cancelled.",
            "validate_promotion_log" => "Are you sure you want to validate this promotion. Once approved you cannot effect any changes.",
            "end_academic_term" => "Are you sure you want to end this academic term. You will be locked out from the system for not more than 10 minute to allow processing? Once confirmed, you cannot reverse the action."
        ];

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