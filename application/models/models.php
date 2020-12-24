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
    public $underscores = "____";

    /** This is used for generation of user ids */
	public $append_zeros = 5;

    /** This is the allowed number of hours which a user can delete an object */
    public $allowed_delete_range = 3;

    /** The allowed maximum attachment size in megabytes */
	public $max_attachment_size = 25;
    public $accepted_attachment_file_types;

    // instantiate the class
    public function __construct() {

        $this->form_modules = [
            "course_unit_form" => "Lesson Planner: Course Unit",
            "course_lesson_form" => "Lesson Planner: Unit Lesson",
            "course_lesson_form_view" => "Lesson Planner: Unit Lesson Details",
            "incident_log_form" => "Log Incident",
            "incident_log_form_view" => "Incident Details",
            "incident_log_followup_form" => "Incident: Followups",
            "modify_guardian_ward" => "Modify Guardian Ward",
            "course_link_upload" => "Lesson Planner: Course Link Upload",
            "course_file_upload" => "Lesson Planner: Course File Upload",
            "upload_assignment" => "Create Assignment",
            "update_assignment" => "Update Assignment Details",
        ];

		$this->favicon_array = [
			'jpg' => 'fa fa-file-image', 'png' => 'fa fa-file-image',
			'jpeg' => 'fa fa-file-image', 'gif' => 'fa fa-file-image',
            'pjpeg' => 'fa fa-file-image', 'webp' => 'fa fa-file-image',
			'pdf' => 'fa fa-file-pdf', 'doc' => 'fa fa-file-word',
			'docx' => 'fa fa-file-word', 'mp3' => 'fa fa-file-audio',
			'mpeg' => 'fa fa-file-video', 'mpg' => 'fa fa-file-video',
			'mov' => 'fa fa-file-video', 'movie' => 'fa fa-file-video',
			'webm' => 'fa fa-file-video', 'flv' => 'fa fa-file-video',
			'qt' => 'fa fa-file-video', 'zip' => 'fa fa-archive',
			'txt' => 'fa fa-file-alt', 'csv' => 'fa fa-file-csv',
			'rtf' => 'fa fa-file-alt', 'xls' => 'fa fa-file-excel',
			'xlsx' => 'fa fa-file-excel', 'php' => 'fa fa-file-alt',
			'css' => 'fa fa-file-alt', 'ppt' => 'fa fa-file-powerpoint',
			'pptx' => 'fa fa-file-powerpoint', 'sql' => 'fa fa-file-alt',
			'json' => 'fa fa-file-alt', 
		];

        $this->unexpected_error = ["code" => 203, "data" => "Sorry! An unexpected error occured.\nPlease contact the admin if problem persists"];

		$this->accepted_attachment_file_types = [
			'jpg', 'png', 'jpeg', 'txt', 'pdf', 'sql', 'docx', 'doc', 'xls', 'xlsx', 'mpeg',
			'ppt', 'pptx', 'php', 'css', 'csv', 'rtf', 'gif', 'pub', 'json', 'zip', 
			'mpg', 'flv', 'webm', 'movie', 'mov', 'qt', 'pjpeg', 'webp'
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

        $this->user_status_list = [
            "0" => "Active",
            "1" => "Inactive"
        ];

		$this->permission_denied = "Sorry! You do not have the required permission to perform this action.";
    }
    


}
?>