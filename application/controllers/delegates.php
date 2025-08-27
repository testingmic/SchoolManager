<?php
class Delegates extends Myschoolgh {

    // accepted columns
    public $accepted_column;
    public $readonly_mode;

    public function __construct($params = null) {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Cards
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list($params = null) {
        
        try {

            global $isWardParent, $defaultUser;

            // if the user is a ward parent, set the guardian id to the user id
            if($isWardParent) {
                $params->guardian_id = $defaultUser->user_id;
            }

            // append some filters to apply to the query
            $query = !empty($params->guardian_id) ? " AND a.guardian_ids LIKE '%{$params->guardian_id}%'" : "";
            $query .= !empty($params->delegate_id) ? " AND a.id='{$params->delegate_id}'" : "";

            // get the list of users based on the request 
            $stmt = $this->db->prepare("SELECT a.* 
            FROM delegates a 
            WHERE a.client_id='{$params->clientId}' {$query} AND a.status='1' ORDER BY a.id DESC");
			$query = $stmt->execute();

            // get the data
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            if(!empty($data) && !empty($params->append_guardian)) {
                // get the guardian ids
                $guardian_ids = !empty($data) ? array_column($data, "guardian_ids") : [];
                $guardian_ids = !empty($guardian_ids) ? array_filter($guardian_ids, function($each) {
                    return !empty($each);
                }) : [];
                $guardian_ids = !empty($guardian_ids) ? array_unique($guardian_ids) : [];

                // get the list of guardians
                $guardians = !empty($guardian_ids) ? $this->pushQuery(
                    "id, item_id, unique_id, phone_number, firstname, lastname, email, gender, residence, image", 
                    "users", 
                    "client_id='{$params->clientId}' AND item_id IN {$this->inList($guardian_ids)} AND user_type='parent' AND status = '1'") : [];

                $regroup = [];
                foreach($guardians as $each) {
                    $regroup[$each->item_id] = $each;
                }

                foreach($data as $key => $each) {
                    $explodes = explode("|", $each->guardian_ids);
                    foreach($explodes as $explode) {
                        if(isset($regroup[$explode])) {
                            $regroup[$explode]->delegate_id = $each->id;
                            $data[$key]->guardians_list[] = $regroup[$explode];
                        }
                    }
                }
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
     * Create Delegate
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function create($params) {

        // get a default client data
        global $accessObject, $isWardParent, $defaultUser;

        // check permission
        if(!$accessObject->hasAccess("add", "delegates") && !$isWardParent) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        foreach(['firstname', 'lastname', 'phone', 'gender', 'relationship'] as $each) {
            if(empty($params->{$each})) {
                return ["code" => 400, "data" => "{$each} is required"];
            }
        }

        $params->phone = trim($params->phone);
        if(strlen($params->phone) > 12) {
            return ["code" => 400, "data" => "Phone number must be less than 12 characters"];
        }

        $delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND phonenumber='{$params->phone}' AND status = '1'");
        if(!empty($delegate)) {
            
            // get the guardian ids
            $guardian_ids = $delegate[0]->guardian_ids;

            // if the guardian id is not set and the user is a ward parent, set the guardian id to the user id
            if(empty($params->guardian_id) && $isWardParent) {
                $params->guardian_id = $defaultUser->userId;
            }

            // append the guardian id to the guardian ids
            if(!empty($guardian_ids) && strpos($guardian_ids, "{$params->guardian_id}") === false) {
                $guardian_ids = rtrim($guardian_ids, "|") . "|{$params->guardian_id}";
            }

            // update the delegate
            $this->quickUpdate("guardian_ids='{$guardian_ids}'", "delegates", "id='{$delegate[0]->id}'");

            return [
                "code" => 200,
                "data" => "Delegate updated successfully.",
                "additional" => [
                    "href" => $this->baseUrl . "guardian/{$params->guardian_id}/delegates"
                ]
            ];

        }
        else {

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO delegates 
                (client_id, firstname, lastname, phonenumber, gender, relationship, guardian_ids, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // execute the statement
            $stmt->execute([
                $params->clientId, $params->firstname, $params->lastname, 
                $params->phone, $params->gender, $params->relationship, 
                $params->guardian_id ?? null, $params->userId
            ]);

            $insertId = $this->db->lastInsertId();
            $delegate_id = "DEL" . $insertId;

            // update the unique id of the delegate
            $this->db->query("UPDATE delegates SET unique_id='{$delegate_id}' WHERE id='{$insertId}' LIMIT 1");

            $href = !empty($params->guardian_id) && !$isWardParent ? "guardian/{$params->guardian_id}/delegates" : "delegates";

            return [
                "code" => 200,
                "data" => "Delegate created successfully.",
                "additional" => [
                    "href" => $this->baseUrl . $href
                ]
            ];
            
        }

    }

    /**
     * Update Delegate
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update($params) {

        // get a default client data
        global $accessObject, $isWardParent;

        // check permission
        if(!$accessObject->hasAccess("update", "delegates") && !$isWardParent) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        foreach(['firstname', 'lastname', 'phone', 'gender', 'relationship'] as $each) {
            if(empty($params->{$each})) {
                return ["code" => 400, "data" => "{$each} is required"];
            }
        }

        $delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND id='{$params->delegate_id}'");
        if(empty($delegate)) {
            return ["code" => 400, "data" => "Delegate not found."];
        }

        $this->quickUpdate("firstname='{$params->firstname}', lastname='{$params->lastname}', phonenumber='{$params->phone}', gender='{$params->gender}', relationship='{$params->relationship}'", "delegates", "id='{$params->delegate_id}'");

        return [
            "code" => 200,
            "data" => "Delegate updated successfully."
        ];
    
    }

    /**
     * Delegate Guardians List
     * 
     * @param Array $guardians_list
     * 
     * @return String
     */
    public function delegate_guardians_list($guardians_list, $canupdate = false) {

        // initialize
		$delegates_list = "";

		// loop through the array list
		foreach($guardians_list as $delegate) {
			// convert to object
            $delegate = (object) $delegate;

			$imageToUse = "<img src=\"{$this->baseUrl}{$delegate->image}\" class='rounded-2xl cursor author-box-picture' width='50px'>";
			if($delegate->image == "assets/img/avatar.png") {
				$imageToUse = "
				<div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
					<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
				</div>";
			}

			// append to the list
			$delegates_list .= "
				<div class=\"col-12 col-md-6 load_delegate_information col-lg-6\" data-delegate_id=\"{$delegate->id}\">
					<div class=\"card card-success\">
						<div class=\"card-header pr-2 pl-2\" style=\"border-bottom:0px;\">
							<div class=\"d-flex gap-4 justify-content-start\">
								<div class='mr-2'>{$imageToUse}</div>
								<div>
									<h4 class=\"mb-0 pb-0 font-16 pr-0 mr-0 text-uppercase\">".limit_words($delegate->firstname . " " . $delegate->lastname, 3)."</h4>
									<span class=\"text-primary\">{$delegate->unique_id}</span><br>
									".(!empty($delegate->relationship) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-user'></i> {$delegate->relationship}</p>" : "")."
									".(!empty($delegate->gender) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-user'></i> {$delegate->gender}</p>" : "")."
                                    <p class=\"mb-0 pb-0\"><i class='fa fa-envelope'></i>".(!empty($delegate->email) ? " {$delegate->email} " : " N/A ")."</p>
                                    <p class=\"mb-0 pb-0\"><i class='fa fa-map-marker'></i>".(!empty($delegate->residence) ? " {$delegate->residence} " : " N/A ")."</p>
									<p class=\"mb-0 pb-0\"><i class='fa fa-phone'></i>".(!empty($delegate->phone_number) ? " {$delegate->phone_number} " : " N/A ")."</p>
								</div>
							</div>
						</div>
						".($canupdate ? 
							"<div class=\"border-top p-2\">
								<div class=\"d-flex justify-content-between\">
									<div>
										<button onclick=\"return load('guardian/{$delegate->item_id}/view')\" class=\"btn btn-sm btn-outline-success\" title=\"View guardian details\"><i class=\"fa fa-eye\"></i> View</button>
									</div>
									<div>
										<a href=\"#\" onclick='return modifyGuardianWard(\"{$delegate->delegate_id}_{$delegate->item_id}\", \"remove\", \"delegate\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Remove</a>
									</div>
								</div>
							</div>" : ""
						)."
					</div>
				</div>
			";
		}

		return $delegates_list;

    }
        
}