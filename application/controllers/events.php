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
        $params->audience = "all,{$params->userData->user_type}";

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->audience)) ? " AND a.audience IN {$this->inList($params->audience)}" : null;
        $params->query .= (isset($params->event_type)) ? " AND a.event_type='{$params->event_type}'" : null;
        $params->query .= (isset($params->holiday)) ? " AND a.is_holiday='{$params->holiday}'" : null;
        $params->query .= (isset($params->event_date)) ? " AND a.start_date='{$params->event_date}'" : null;
        $params->query .= (isset($params->event_id)) ? " AND a.item_id='{$params->event_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT ".(isset($params->columns) ? $params->columns : "
                    a.*, (SELECT b.name FROM events_types b WHERE b.item_id = a.event_type LIMIT 1) AS type_name,
                    (SELECT 
                        CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) 
                        FROM users b WHERE b.item_id = a.created_by LIMIT 1
                    ) AS created_by_info")."
                FROM  events a
                WHERE {$params->query} AND a.client_id = '{$params->clientId}' AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // description
                $result->description = custom_clean(htmlspecialchars_decode($result->description));

                // if the created by info was parsed
                if(isset($result->created_by_info)) {
                    // loop through the information
                    foreach(["created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
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
     * Add a new Event
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        /** Date mechanism */
        $date = explode(":", $params->date);
        $start_date = $date[0];
        $item_id = random_string("alnum", 32);
        $end_date = isset($date[1]) ? $date[1] : $date[0];

        /** Audience check */
        if(!in_array($params->audience, ["all", "teacher", "student", "parent"])) {
            return ["code" => 203, "Sorry! Invalid audience was parsed."];
        }
        
        // confirm that a logo was parsed
        if(isset($params->event_image)) {
            // set the upload directory
            $uploadDir = "assets/img/events/";

            // File path config 
            $file_name = basename($params->event_image["name"]); 
            $targetFilePath = $uploadDir . $file_name; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');

            // check if its a valid image
            if(!empty($file_name) && in_array($fileType, $allowTypes)){
                // set a new file_name
                $image = $uploadDir . random_string('alnum', 10)."__{$file_name}";
                // Upload file to the server 
                if(move_uploaded_file($params->event_image["tmp_name"], $file_name)){}
            } else {
                return ["code" => 203, "Sorry! The event file must be a valid image."];
            }
        }

        /** Insert the record */
        $stmt = $this->db->prepare("INSERT INTO events SET 
            client_id = ?, item_id = ?, title = ?, description = ?, start_date = ?, end_date = ?,
            event_image = ?, audience = ?, is_holiday = ?, created_by = ?, is_mailable = ?, event_type = ?
        ");
        $stmt->execute([
            $params->clientId, $item_id, $params->title, $params->description ?? null, 
            $start_date, $end_date, $image ?? null, $params->audience, $params->holiday ?? null, 
            $params->userId, $params->is_mailable ?? null, $params->type
        ]);

        /** log the user activity */
        $this->userLogs("events", $item_id, null, "{$params->userData->name} created a new Event with title <strong>{$params->title}</strong> to be held on {$start_date}.", $params->userId);

        return [
            "data" => "Event was successfully created.",
            "additional" => [
                "clear" => true
            ]
        ];
    }

    /**
     * Update an Event
     * 
     * @return Array
     */
    public function update(stdClass $params) {
        
        /** Date mechanism */
        $date = explode(":", $params->date);
        $start_date = $date[0];
        $item_id = $params->event_id;
        $end_date = isset($date[1]) ? $date[1] : $date[0];

        // get the assignment information
        $prev = $this->pushQuery("title,start_date,end_date,description,audience,type,event_type,is_holiday", 
            "events", "client_id='{$params->clientId}' AND item_id='{$item_id}' LIMIT 1");

        // validate the record
        if(empty($prev)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }

        /** Audience check */
        if(!in_array($params->audience, ["all", "teacher", "student", "parent"])) {
            return ["code" => 203, "Sorry! Invalid audience was parsed."];
        }
        
        // confirm that a logo was parsed
        if(isset($params->event_image)) {
            // set the upload directory
            $uploadDir = "assets/img/events/";

            // File path config 
            $file_name = basename($params->event_image["name"]); 
            $targetFilePath = $uploadDir . $file_name; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');

            // check if its a valid image
            if(!empty($file_name) && in_array($fileType, $allowTypes)){
                // set a new file_name
                $image = $uploadDir . random_string('alnum', 10)."__{$file_name}";
                // Upload file to the server 
                if(move_uploaded_file($params->event_image["tmp_name"], $file_name)){}
            } else {
                return ["code" => 203, "Sorry! The event file must be a valid image."];
            }
        }

        /** Insert the record */
        $stmt = $this->db->prepare("UPDATE events SET title = ?, start_date = ?, end_date = ?
            ".(isset($params->description) ? ",description = '{$params->description}'" : "")."
            ".(isset($image) ? ",event_image = '{$image}'" : "")."
            ".(isset($params->audience) ? ",audience = '{$params->audience}'" : "")."
            ".(isset($params->holiday) ? ",is_holiday = '{$params->holiday}'" : "")."
            ".(isset($params->type) ? ",event_type = '{$params->type}'" : "")."
            ".(isset($params->is_mailable) ? ",is_mailable = '{$params->is_mailable}'" : "")."
            WHERE client_id = ? AND item_id = ? LIMIT 1
        ");
        $stmt->execute([
            $params->title, $start_date, $end_date, $params->clientId, $item_id
        ]);

        /** log the user activity */
        $this->userLogs("events", $item_id, null, "{$params->userData->name} updated the event details.", $params->userId);

        /** Save the changes applied to each column of the table */

        return [
            "data" => "Event was successfully updated.",
            "additional" => []
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
        $events_types = $this->pushQuery("*", "events_types", "client_id = '{$params->clientId}' AND status='1' {$query}");

        $data = [];

        // loop through the event types
        foreach($events_types as $type) {
            $data[] = $type;
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
        $item_id = random_string("alnum", 32);

        /** Insert */
        $stmt = $this->db->prepare("INSERT INTO events_types SET client_id = ?, item_id = ?, name = ?, description = ?, icon = ?");
        $stmt->execute([$params->clientId, $item_id, $params->name, $params->description ?? "", $params->icon ?? null]);

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
            return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
        }
        
        /** Push the record into the database */
        $item_id = $params->type_id;

        /** Insert */
        $stmt = $this->db->prepare("UPDATE events_types SET name = ?
            ".(isset($params->description) ? ",description = '{$params->description}'" : "")."
            ".(isset($params->icon) ? ",icon = '{$params->icon}'" : "")."
            WHERE client_id = ? AND item_id = ?
        ");
        $stmt->execute([$params->name, $params->clientId, $item_id]);

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

        // load user birthdays
        $birth_list = $this->pushQuery(
            "name, phone_number, email, image, item_id, unique_id, DAY(date_of_birth) AS the_day, MONTH(date_of_birth) AS the_month", 
            "users", "client_id = '{$data->client_id}' AND user_status='Active' AND status='1' AND deleted='0' LIMIT 200"
        );
        $birthday_list = [];
        
        // loop through the users list
        foreach($birth_list as $user) {
            $dob = date("Y")."-".$this->append_zeros($user->the_month,2)."-".$this->append_zeros($user->the_day,2);
            $birthday_list[] = [
                "title" => "{$user->name}",
                "start" => "{$dob}T06:00:00",
                "end" => "{$dob}T18:00:00",
                "description" => "
                    <div class='row'>
                        <div class='col-md-10'>
                            <div>
                                This is the birthday of <strong>{$user->name}</strong>. 
                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"{$this->baseUrl}compose?user_id={$user->item_id}&name={$user->name}\")'>Click Here</a> 
                                to send a Email or SMS message to the user.
                            </div>
                            <div class='mt-3'>
                                ".(!empty($user->phone_number) ? "<p class='p-0 m-0'><i class='fa fa-phone'></i> {$user->phone_number}</p>" : "")."
                                ".(!empty($user->email) ? "<p class='p-0 m-0'><i class='fa fa-envelope'></i> {$user->email}</p>" : "")."
                            </div>    
                        </div>
                        <div class='col-md-2'>
                            <img class='rounded-circle cursor author-box-picture' width='60px' src='{$this->baseUrl}{$user->image}'>
                        </div>
                    </div>"
            ];
        }

        // set the parameters
        $result = (object) [];
        $params = (object) ["userData" => $data, "clientId" => $data->client_id];

        // append the birthday_list 
        $result->birthday_list = json_encode($birthday_list);

        // append the holidays list
        $holidays_list = [];
        $params->holiday = "on";
        $params->columns = "title, description, start_date, end_date, event_image, audience, is_holiday, event_type";
        $hol_list = $this->list($params)["data"];

        // confirm if admin
        $isAdmin = (bool) ($data->user_type == "admin");

        // loop through the holidays list
        foreach($hol_list as $event) {

            // set the description
            $description = "
            <div class='row'>
                <div class='col-md-12'>
                    ".(!empty($event->event_image) ? "<div><img width='100%' src='{$this->baseUrl}{$event->event_image}'></div>" : "")."
                    <div>
                        $event->description
                    </div>
                    <div class='mt-3'>
                        ".(!empty($event->start_date) ? "<p class='p-0 m-0'><i class='fa fa-calendar'></i> Start Date: ".date("jS F Y", strtotime($event->start_date))."</p>" : "")."
                        ".(!empty($event->end_date) ? "<p class='p-0 m-0'><i class='fa fa-calendar-check'></i> End Date: ".date("jS F Y", strtotime($event->end_date))."</p>" : "")."
                        ".($isAdmin ? "<p class='p-0 m-0'><i class='fa fa-users'></i>  Audience: ".strtoupper($event->audience)."</p>" : "")."
                    </div>    
                </div>
            </div>";
            // append the array list
            $holidays_list[] = [
                "title" => "{$event->title}",
                "start" => "{$event->start_date}T06:00:00",
                "end" => "{$event->end_date}T18:00:00",
                "description" => $description
            ];
        }

        $result->holidays_list = json_encode($holidays_list);

        return $result;
        
    }
    
}