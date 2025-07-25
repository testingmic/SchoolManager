<?php 

class Events extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List the events
     * 
     * Apply all the possible filters available to ensure each user sees what they are supposed to see
     * 
     * @param
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // append the user type to the list of audience
        $params->audience = isset($params->the_user_type) ? "all,{$params->the_user_type}" : "all,{$params->userData->user_type}";

        // append the audience
        if(($params->userData->user_type == "admin")) {
            // list all events
            $params->audience = "all,student,teacher,parent,admin";
        }

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->audience) && !empty($params->audience)) ? " AND a.audience IN {$this->inList($params->audience)}" : null;
        $params->query .= (isset($params->event_type) && !empty($params->event_type)) ? " AND a.event_type='{$params->event_type}'" : null;
        $params->query .= (isset($params->holiday) && !empty($params->holiday)) ? " AND a.is_holiday='{$params->holiday}'" : "";
        $params->query .= (isset($params->event_date) && !empty($params->holiday)) ? " AND a.start_date='{$params->event_date}'" : null;
        $params->query .= (isset($params->event_id)) ? " AND a.item_id='{$params->event_id}'" : null;
        $params->query .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "start_date") : null;

        try {

            $stmt = $this->db->prepare("
                SELECT ".(isset($params->columns) ? $params->columns : "
                    a.*, et.name AS type_name, et.color_code AS color_code,
                    (SELECT 
                        CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email, 'NULL'),'|',b.image,'|',b.user_type) 
                        FROM users b WHERE b.item_id = a.created_by LIMIT 1
                    ) AS created_by_info")."
                FROM  events a
                LEFT JOIN events_types et ON et.item_id = a.event_type
                WHERE {$params->query} AND a.client_id = '{$params->clientId}' AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // description
                $result->description = !empty($result->description) ? custom_clean(htmlspecialchars_decode($result->description)) : null;

                // if the created by info was parsed
                if(isset($result->created_by_info)) {
                    // loop through the information
                    foreach(["created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","user_type"]);
                    }
                }

                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * View an event
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {
        if(empty($params->event_id)) {
            return ["code" => 400, "data" => "Sorry! An invalid event id was parsed."];
        }
        $params->event_id = $params->event_id;
        $record = $this->list($params);

        return $record['data'][0] ?? [];
    }

    /**
     * Add a new Event
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        /** Date mechanism */
        $date = explode(":", $params->date);
        $start_date = $date[0];
        $item_id = random_string("alnum", RANDOM_STRING);
        $end_date = isset($date[1]) ? $date[1] : $date[0];

        // global variables
        global $accessObject;
        $accessObject->userId = $params->userId;
        $accessObject->clientId = $params->clientId;
        $accessObject->userPermits = $params->userData->user_permissions;

        // check if the user has access to update events
        $params->hasEventUpdate = $accessObject->hasAccess("update", "events");

        /** Audience check */
        if(!in_array($params->audience, array_keys($this->event_audience))) {
            return ["code" => 400, "data" => "Sorry! Invalid audience was parsed - Accepted values are: ".implode(", ", array_keys($this->event_audience))];
        }

        if(!empty($params->holiday) && !in_array($params->holiday, ["yes", "no", "on", "not"])) {
            return ["code" => 400, "data" => "Sorry! Invalid holiday was parsed - Accepted values are: yes, no"];
        }

        if(!empty($params->holiday)) {
            $params->holiday = $params->holiday == "yes" ? "on" : (
                $params->holiday == "not" ? "off" : $params->holiday
            );
        }
        
        // confirm that a logo was parsed
        if(isset($params->event_image)) {
            // set the upload directory
            $uploadDir = "assets/img/events/";

            if(!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }

            // File path config 
            $file_name = basename($params->event_image["name"]); 
            $targetFilePath = $uploadDir . $file_name; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg','gif');

            // check if its a valid image
            if(!empty($file_name) && validate_image($params->event_image["tmp_name"])){
                // set a new file_name
                $image = $uploadDir . random_string("alnum", RANDOM_STRING).".{$fileType}";
                // Upload file to the server 
                if(move_uploaded_file($params->event_image["tmp_name"], $image)){}
            } else {
                return ["code" => 400, "data" => "Sorry! The event file must be a valid image."];
            }
        }

        // check if event exits
        $prev = $this->pushQuery("*", "events", 
            "client_id='{$params->clientId}' AND title='{$params->title}' 
            AND start_date='{$start_date}' AND end_date='{$end_date}' AND state = 'Pending' AND audience = '{$params->audience}'
            LIMIT 1"
        );
        if(!empty($prev)) {
            return ["code" => 400, "data" => "Sorry! An event with the same title and date already exists."];
        }

        if(!empty($params->status) && !in_array($params->status, ["Pending", "Ongoing", "Held", "Cancelled"])) {
            return ["code" => 400, "data" => "Sorry! An invalid status was parsed."];
        }

        /** Insert the record */
        $stmt = $this->db->prepare("INSERT INTO events SET 
            client_id = ?, item_id = ?, title = ?, description = ?, start_date = ?, end_date = ?,
            event_image = ?, audience = ?, is_holiday = ?, created_by = ?, is_mailable = ?, event_type = ?
            ".(isset($params->status) ? ",state = '{$params->status}'" : "")."
        ");
        $stmt->execute([
            $params->clientId, $item_id, $params->title, $params->description ?? null, 
            $start_date, $end_date, $image ?? null, $params->audience, $params->holiday ?? "not", 
            $params->userId, $params->is_mailable ?? null, $params->type
        ]);

        /** log the user activity */
        $this->userLogs("events", $item_id, null, "{$params->userData->name} created a new Event with title <strong>{$params->title}</strong> to be held on {$start_date}.", $params->userId);

        // set the event id
        $params->event_id = $item_id;

        // return the response
        return [
            "data" => "Event was successfully created.",
            "record" => $this->view($params),
            "additional" => [
                "clear" => true,
                "href" => "{$this->baseUrl}update-event/{$item_id}"
            ]
        ];
    }

    /**
     * Update an Event
     * 
     * @return Array
     */
    public function update(stdClass $params) {
        
        if(empty($params->event_id)) {
            return ["code" => 400, "data" => "Sorry! An invalid event id was parsed."];
        }

        /** Date mechanism */
        $item_id = $params->event_id;

        // if the date parameter was parsed
        if(!empty($params->date)) {
            $date = explode(":", $params->date);
            $start_date = $date[0];
            $end_date = isset($date[1]) ? $date[1] : $date[0];
        }

        // validate the status
        if(!empty($params->status) && !in_array($params->status, ["Pending", "Ongoing", "Held", "Cancelled"])) {
            return ["code" => 400, "data" => "Sorry! An invalid status was parsed."];
        }

        // global variables
        global $accessObject;
        $accessObject->userId = $params->userId;
        $accessObject->clientId = $params->clientId;
        $accessObject->userPermits = $params->userData->user_permissions;
        
        $params->hasEventUpdate = $accessObject->hasAccess("update", "events");

        // return access denied if not permitted
        if(!$params->hasEventUpdate) {
            return ["code" => 401, "data" => $this->permission_denied];
        }

        // get the assignment information
        $prev = $this->pushQuery("title,start_date,end_date,description,audience,event_type,is_holiday,state", 
            "events", "client_id='{$params->clientId}' AND item_id='{$item_id}' AND status = '1' LIMIT 1");

        // validate the record
        if(empty($prev)) {
            return ["code" => 400, "data" => "Sorry! An invalid event id was parsed."];
        }

        /** Audience check */
        if(!empty($params->audience) && !in_array($params->audience, array_keys($this->event_audience))) {
            return ["code" => 400, "data" => "Sorry! Invalid audience was parsed."];
        }
        
        // confirm that a logo was parsed
        if(isset($params->event_image)) {
            // set the upload directory
            $uploadDir = "assets/img/events/";

            if(!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }

            // File path config 
            $file_name = basename($params->event_image["name"]); 
            $targetFilePath = $uploadDir . str_ireplace("/[\s]/", "", $file_name);
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg','gif');

            // check if its a valid image
            if(!empty($file_name) && validate_image($params->event_image["name"])){
                // set a new file_name
                $image = $uploadDir . random_string("alnum", RANDOM_STRING).".{$fileType}";
                // Upload file to the server 
                if(move_uploaded_file($params->event_image["tmp_name"], $image)){}
            } else {
                return ["code" => 400, "data" => "Sorry! The event file must be a valid image."];
            }
        }

        // if the holiday parameter was parsed
        $params->holiday = isset($params->holiday) ? $params->holiday : "not";

        /** Insert the record */
        $stmt = $this->db->prepare("UPDATE events SET client_id = ?
            ".(!empty($start_date) ? ",start_date = '{$start_date}'" : "")."
            ".(!empty($end_date) ? ",end_date = '{$end_date}'" : "")."
            ".(!empty($params->title) ? ",title = '{$params->title}'" : "")."
            ".(!empty($params->description) ? ",description = '{$params->description}'" : "")."
            ".(!empty($params->is_mailable) ? ",is_mailable = '{$params->is_mailable}'" : "")."
            ".(!empty($params->audience) ? ",audience = '{$params->audience}'" : "")."
            ".(!empty($params->holiday) ? ",is_holiday = '{$params->holiday}'" : "")."
            ".(!empty($image) ? ",event_image = '{$image}'" : "")."
            ".(!empty($params->status) ? ",state = '{$params->status}'" : "")."
            ".(!empty($params->type) ? ",event_type = '{$params->type}'" : "")."
            WHERE client_id = ? AND item_id = ? LIMIT 1
        ");
        $stmt->execute([$params->clientId, $params->clientId, $item_id]);

        /** log the user activity */
        $this->userLogs("events", $item_id, null, "{$params->userData->name} updated the event details.", $params->userId);

        /** Save the changes applied to each column of the table */
        return [
            "data" => "Event was successfully updated.",
            "record" => $this->view($params),
            "additional" => [
                "href" => "{$this->baseUrl}update-event/{$item_id}"
            ]
        ];

    }

    /**
     * Delete an Event
     * 
     * @return Array
     */
    public function delete(stdClass $params) {

        // validate the event id
        if(empty($params->event_id)) {
            return ["code" => 400, "data" => "Sorry! An invalid event id was parsed."];
        }

        global $accessObject;
        $accessObject->userId = $params->userId;
        $accessObject->clientId = $params->clientId;
        $accessObject->userPermits = $params->userData->user_permissions;

        // check if the user has access to delete events
        $params->hasEventDelete = $accessObject->hasAccess("delete", "events");

        // return access denied if not permitted
        if(!$params->hasEventDelete) {
            return ["code" => 401, "data" => $this->permission_denied];
        }
        
        // get the assignment information
        $prev = $this->pushQuery("title,start_date,end_date,description,audience,event_type,is_holiday,state", 
            "events", "client_id='{$params->clientId}' AND item_id='{$params->event_id}' AND status = '1' LIMIT 1");

        // validate the record
        if(empty($prev)) {
            return ["code" => 400, "data" => "Sorry! An invalid event id was parsed."];
        }

        /** Delete the record */
        $this->quickQuery("UPDATE events SET status = '0' WHERE client_id = '{$params->clientId}' AND item_id = '{$params->event_id}' LIMIT 1");

        return [
            "data" => "Event was successfully deleted.",
            "additional" => [
                "href" => "{$this->baseUrl}events"
            ]
        ];
    }

    /**
     * Return the list of event types
     * 
     * @param String    $params->type_id
     * @param String    $params->clientId
     * 
     * @return Array
     */
    public function types_list(stdClass $params) {

        // columns to load
        $query = isset($params->type_id) && !empty($params->type_id) ? " AND item_id='{$params->type_id}'" : "";

        // make the request
        $events_types = $this->pushQuery("*", "events_types", "client_id = '{$params->clientId}' AND status='1' {$query} LIMIT {$this->temporal_maximum}");

        $data = [];

        // loop through the event types
        foreach($events_types as $type) {
            if(!empty($params->remote)) {
                $data[] = $type;
            } else {
                $data[$type->item_id] = $type;
            }
        }

        // return the list
        return $data;

    }

    /**
     * Save event type
     * 
     * Return the list of all event types after the query
     * 
     * @return Array
     */
    public function add_type(stdClass $params) {

        /** Push the record into the database */
        $item_id = random_string("alnum", RANDOM_STRING);

        /** Insert */
        $stmt = $this->db->prepare("INSERT INTO events_types SET 
            client_id = ?, item_id = ?, name = ?, slug = ?,
            description = ?, icon = ?, color_code = ?
        ");
        $stmt->execute([$params->clientId, $item_id, $params->name, create_slug($params->name), 
            $params->description ?? "", $params->icon ?? null, $params->color_code ?? null
        ]);

        /** Log the user activity */
        $this->userLogs("events_type", $item_id, null, "{$params->userData->name} created a new Event Type: {$params->name}", $params->userId);

        // return the response together with the types list
        return [
            "data" => "Event type was successfully created",
            "additional" => [
                "event_types" => $this->types_list($params)
            ]
        ];
    }

    /**
     * Update event type
     * 
     * Return the list of all event types after the query
     * 
     * @return Array
     */
    public function update_type(stdClass $params) {

        // old record
        $prevData = $this->pushQuery("*", "events_types", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
        }
        
        /** Push the record into the database */
        $item_id = $params->type_id;

        // global variables
        global $accessObject;
        $accessObject->userId = $params->userId;
        $accessObject->clientId = $params->clientId;
        $accessObject->userPermits = $params->userData->user_permissions;
        
        $params->hasEventDelete = $accessObject->hasAccess("delete", "events");
        $params->hasEventUpdate = $accessObject->hasAccess("update", "events");

        /** Insert */
        $stmt = $this->db->prepare("UPDATE events_types SET name = ?, slug = ?
            ".(isset($params->description) ? ",description = '".addslashes($params->description)."'" : "")."
            ".(isset($params->color_code) ? ",color_code = '{$params->color_code}'" : "")."
            ".(isset($params->icon) ? ",icon = '{$params->icon}'" : "")."
            WHERE client_id = ? AND item_id = ? LIMIT 1
        ");
        $stmt->execute([$params->name, create_slug($params->name), $params->clientId, $item_id]);


        /** Log the user activity */
        $this->userLogs("events_type", $item_id, $prevData[0], "{$params->userData->name} successfully updated the event type: {$params->name}", $params->userId);

        // unset the type id
        $params->type_id = null;

        // return the response together with the types list
        return [
            "data" => "Event type was successfully updated",
            "additional" => [
                "event_types" => $this->types_list($params)
            ]
        ];

    }

    /**
     * Format and submit events
     * 
     * @param Object        $defaultUser
     * 
     * @return Object
     */
    public function events_list($data = null) {

        // global variable
        global $defaultClientData;

        // init
        $birthday_list = [];

        // minified description
        $minified = (bool) isset($data->mini_description);
        $do_not_encode = (bool) isset($data->do_not_encode);

        // show birthday information if the user type is a teacher or admin
        if(in_array($data->the_user_type, ["admin", "accountant", "teacher"])) {

            // load user birthdays
            $birth_list = $this->pushQuery(
                "u.name, u.phone_number, u.email, u.image, u.item_id, u.unique_id, u.user_type,
                    DAY(u.date_of_birth) AS the_day, MONTH(u.date_of_birth) AS the_month, cl.name AS class_name", 
                "users u LEFT JOIN classes cl ON cl.id = u.class_id", "
                (
                    -- Normalize date_of_birth to the current year
                    DATE_FORMAT(u.date_of_birth, CONCAT(YEAR(CURDATE()), '-%m-%d')) 
                    BETWEEN 
                        DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), '%Y-%m-%d') 
                    AND 
                        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), '%Y-%m-%d')
                )
                 AND u.client_id = '{$data->client_id}' AND u.user_status='Active' AND u.status='1' AND u.deleted='0'
                AND u.user_type IN ('student','teacher','admin','accountant','admin')
                ORDER BY MONTH(u.date_of_birth), DAY(u.date_of_birth) ASC LIMIT {$this->temporal_maximum}", false
            );
            
            // loop through the users list
            foreach($birth_list as $ik => $user) {

                // configure the date to load
                $dob = date("Y")."-".$this->append_zeros($user->the_month,2)."-".$this->append_zeros($user->the_day,2);

                $user->name = random_names($user->name);
                
                // run this section if the description is not minified
                if(!$minified) {
                    $description = "
                        <div class='row'>
                            <div class='col-md-10'>
                                <div>
                                    This is the birthday of <strong>{$user->name}</strong>. 
                                    <a href='javascript:void(0)' class='anchor' onclick='load(\"email_send?user_id={$user->item_id}&name={$user->name}\")'>Click Here</a> 
                                    to send a Email or SMS message to the user.
                                </div>
                                <div class='mt-3'>
                                    ".(!empty($user->phone_number) ? "<p class='p-0 m-0'><i class='fa fa-phone'></i> {$user->phone_number}</p>" : "")."
                                    ".(!empty($user->email) ? "<p class='p-0 m-0'><i class='fa fa-envelope'></i> {$user->email}</p>" : "")."
                                </div>    
                            </div>
                            <div class='col-md-2'>
                                <img class='rounded-2xl cursor author-box-picture' width='60px' src='{$this->baseUrl}{$user->image}'>
                            </div>
                        </div>
                        <div class='modal-footer p-0'>
                            <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close</button>
                        </div>";
                } else {
                    $user->name = trim($user->name);
                    $description = $user;
                }

                // append to the array list
                $birthday_list[$ik] = [
                    "title" => $user->name,
                    "link" => $this->the_user_roles[$user->user_type]["link"],
                    "start" => "{$dob} 06:00:00",
                    "end" => "{$dob} 18:00:00"
                ];

                if(!empty($data->birthday_only)) {
                    $birthday_list[$ik]["date_of_birth"] = $dob;
                }

                if(empty($data->birthday_only)) {
                    $birthday_list[$ik]["description"] = $description;
                }
            }
        }

        // set the parameters
        $result = (object) [];

        if(!empty($data->birthday_only)) {
            return $birthday_list ?? [];
        }

        // append the birthday_list 
        $result->birthday_list =  !$do_not_encode ? json_encode($birthday_list) : $birthday_list;

        // append the holidays list
        $array_list = [
            "holidays_list" => ["holiday" => "on"], 
            "calendar_events_list" => ["holiday" => "not"]
        ];  

        // init the parameters for the events (holidays and other events)
        $params = (object) ["userData" => $data, "date_range" => $data->events_date_range ?? null, "clientId" => $data->client_id, "the_user_type" => $data->the_user_type];
        
        // loop through the query to use
        foreach($array_list as $key => $each) {

            // set the initial parameters
            $query_array = [];
            $params->holiday = $each["holiday"];
            $hol_list = $this->list($params)["data"];

            // confirm if admin
            $isAdmin = (bool) (in_array($data->the_user_type, ["admin", "accountant"]));

            // loop through the holidays list
            foreach($hol_list as $ekey => $event) {
                
                // run this section if the description is not minified
                if(!$minified) {
                    // set the description
                    $description = "
                    <div class='row'>
                        <div class='col-md-12'>
                            ".(!empty($event->event_image) && file_exists($event->event_image) ? "<div><img width='100%' src='{$this->baseUrl}{$event->event_image}'></div>" : "")."
                            <div>
                                $event->description
                            </div>
                            <div class='mt-3'>
                                ".(!empty($event->start_date) ? "<p class='p-0 m-0'><i class='fa fa-calendar'></i> <strong>Start Date:</strong> ".date("jS F Y", strtotime($event->start_date))."</p>" : "")."
                                ".(!empty($event->end_date) ? "<p class='p-0 m-0'><i class='fa fa-calendar-check'></i> <strong>End Date:</strong> ".date("jS F Y", strtotime($event->end_date))."</p>" : "")."
                                ".($isAdmin ? "<p class='p-0 m-0'><i class='fa fa-users'></i>  <strong>Audience:</strong> ".strtoupper($event->audience)."</p>" : "")."
                                ".(!empty($event->type_name) ? "<p class='p-0 m-0'><i class='fa fa-home'></i> <strong>Type:</strong> ".$event->type_name."</p>" : "")."
                                ".(!empty($event->state) ? "<p class='p-0 m-0'><i class='fa fa-air-freshener'></i> <strong>Status:</strong> ".$this->the_status_label($event->state)."</p>" : "")."
                            </div>    
                        </div>
                    </div>
                    <div class='modal-footer p-0'>
                        <button type='button' class='btn btn-sm btn-outline-secondary' data-dismiss='modal'>Close</button>
                        ".($isAdmin ? "
                            <a href='javascript:void(0)' onclick='return load_Event(\"{$this->baseUrl}update-event/{$event->item_id}\");' class='btn anchor btn-sm btn-outline-success'><i class='fa fa-edit'></i> Edit</a>
                            <a href='#' onclick='return delete_record(\"{$event->item_id}\", \"event\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>
                        ": "")."
                    </div>";
                } else {
                    $description = $event->description;
                }

                // append the array list
                $query_array[$ekey] = [
                    "title" => $event->title,
                    "start" => "{$event->start_date} 06:00:00",
                    "end" => "{$event->end_date} 18:00:00",
                    "description" => $description,
                    "backgroundColor" => "{$event->color_code}",
                    "borderColor" => "{$event->color_code}",
                ];

                // append to the array return list if the minified is parsed
                if($minified) {
                    $query_array[$ekey]["event_group"] = $key;
                    $query_array[$ekey]["end_date"] = $event->end_date;
                    $query_array[$ekey]["start_date"] = $event->start_date;
                    $query_array[$ekey]["item_id"] = $event->item_id;
                    $query_array[$ekey]["audience"] = $event->audience;
                    $query_array[$ekey]["event_type"] = $event->type_name;
                    $query_array[$ekey]["event_image"] = (!empty($event->event_image) && file_exists($event->event_image)) ? $event->event_image : null;
                }

                // if the user is an admin
                if($isAdmin) {
                    $query_array[$ekey]["is_editable"] = true;
                    $query_array[$ekey]["item_id"] = $event->item_id; 
                }
            }

            $result->{$key} = $query_array;

        }

        return $result;
        
    }

    /**
     * Get the birthdays list
     * 
     * @param Object    $params
     * 
     * @return Array
     */
    public function birthdays($params) {

        // global object
        global $accessObject, $defaultUser;

        // append the usertype
        $params->userData->the_user_type = $defaultUser->user_type;
        $params->userData->birthday_only = true;

        // get the events list
        $all_events = $this->events_list($params->userData);

        return $all_events;
    }

    /**
     * Preload Events
     * 
     * Refresh the events content
     * 
     * @return Bool
     */
    public function preload($params) {

        // global object
        global $accessObject, $defaultUser;

        // set the access object
        $params->hasEventDelete = $accessObject->hasAccess("delete", "events");
        $params->hasEventUpdate = $accessObject->hasAccess("update", "events");

        // append the the parameters
        $params->userData->hasEventDelete = $params->hasEventDelete;
        $params->userData->hasEventUpdate = $params->hasEventUpdate;
            
        // append the usertype
        $params->userData->the_user_type = $defaultUser->user_type;
        $params->birthday_only = false;

        $events_list = [];

        // get the events list
        $all_events = $this->events_list($params->userData);

        // loop through the events list
        foreach($all_events as $key => $event) {
            if($key == 'birthday_list') continue;
            $events_list = array_merge($events_list, $event);
        }

        // set the parameters for the events
        $param = (object) [
            "container" => "events_management",
            "events_list" => $events_list,
            "event_Sources" => "birthdayEvents,holidayEvents,calendarEvents"
        ];

        return [
            "raw_output" => true,
            "data" => $events_list
        ];

    }
    
}