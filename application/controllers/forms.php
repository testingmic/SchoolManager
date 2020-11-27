<?php 

class Forms extends Myschoolgh {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Users form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function users_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->country) ? true : false;

        $guardian = "";

        // if the guardian information is parsed
        if(!empty($userData->guardian_information)) {
            // user id
            $guardian .= '<input type="hidden" id="user_id" value="'.$userData->user_id.'" name="user_id" value="student">';
            // loop through the information
            foreach($userData->guardian_information as $key => $eachItem) {
                $key_id = $key;
                $guardian .= '
                <div class="row" data-row="'.$key_id.'">
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_fullname]['.$key_id.']">Fullname</label>
                        <input type="text" value="'.$eachItem->guardian_fullname.'" name="guardian_info[guardian_fullname]['.$key_id.']" id="guardian_info[guardian_fullname]['.$key_id.']" class="form-control">
                        <div class="col-lg-12 col-md-12 pl-0 mt-2">
                            <label for="guardian_info[guardian_relation]['.$key_id.']">Relationship</label>
                            <select name="guardian_info[guardian_relation]['.$key_id.']" id="guardian_info[guardian_relation]['.$key_id.']" class="form-control selectpicker">
                                <option value="null">Select Relation</option>';
                                foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                    $guardian .= "<option ".($each->name == $eachItem->guardian_relation ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                                }
                        $guardian .= '</select>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_contact]['.$key_id.']">Contact Number</label>
                        <input type="text" value="'.$eachItem->guardian_contact.'" name="guardian_info[guardian_contact]['.$key_id.']" id="guardian_info[guardian_contact]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label for="guardian_info[guardian_email]['.$key_id.']">Email Address</label>
                        <input type="text" value="'.$eachItem->guardian_email.'" name="guardian_info[guardian_email]['.$key_id.']" id="guardian_info[guardian_email]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-1 col-md-1 text-right">
                        <div class="d-flex justify-content-end">';
                        if($key_id == 1) {
                            $guardian .= '
                            <div class="mr-1"><br>
                                <button data-row="'.$key_id.'" class="btn append-row btn-primary" type="button"><i class="fa fa-plus"></i></button>
                            </div>';
                        } else {
                            $guardian .= '
                            <div class="mr-1"><br>
                                <button data-row="'.$key_id.'" class="btn remove_guardian_row btn-danger" type="button"><i class="fa fa-trash"></i></button>
                            </div>';
                        }
                        $guardian .= '
                            </div>
                    </div>
                </div>';
            }
        }

        $response = '
        <form class="ajaxform" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Student Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Student ID (optional)</label>
                        <input type="text" value="'.($userData->unique_id ?? null).'" name="unique_id" id="unique_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="enrollment_date">Enrollment Date <span class="required">*</span></label>
                        <input type="date" value="'.($userData->enrollment_date ?? null).'" name="enrollment_date" id="enrollment_date" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control selectpicker">
                            <option value="null">Select Gender</option>';
                            foreach($this->pushQuery("*", "users_gender") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->gender) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="firstname">Firstname <span class="required">*</span></label>
                        <input type="text" value="'.($userData->firstname ?? null).'" name="firstname" id="firstname" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="lastname">Lastname <span class="required">*</span></label>
                        <input type="text" value="'.($userData->lastname ?? null).'" name="lastname" id="lastname" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="othername">Othernames</label>
                        <input type="text" value="'.($userData->othername ?? null).'" name="othername" id="othername" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                        <input type="date" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="phone">Primary Contact</label>
                        <input type="text" name="phone" value="'.($userData->phone_number ?? null).'" id="phone" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="phone_2">Secondary Contact</label>
                        <input type="text" name="phone_2" value="'.($userData->phone_number_2 ?? null).'" id="phone_2" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select name="country" id="country" class="form-control selectpicker">
                            <option value="null">Select Country</option>';
                            foreach($this->pushQuery("*", "country") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->country) ? "selected" : null)." value=\"{$each->id}\">{$each->country_name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="residence">Place of Residence <span class="required">*</span></label>
                        <input type="text" value="'.($userData->residence ?? null).'" name="residence" id="residence" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Broup</label>
                        <select name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="null">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->blood_group) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                        <input type="hidden" id="user_type" name="user_type" value="'.(!$isData ? "student" : null).'">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control">'.($userData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row mb-4 border-bottom pb-4">
                <div class="col-lg-12"><h5>GUARDIAN INFORMATION</h5></div>
                <div class="col-lg-12" id="student_guardian_list">';
                
                // if the data
                if($isData) {
                    $response .= $guardian;
                } else {
                    $response .= '
                    <div class="row" data-row="1">
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_fullname][1]">Fullname</label>
                            <input type="text" name="guardian_info[guardian_fullname][1]" id="guardian_info[guardian_fullname][1]" class="form-control">
                            <div class="col-lg-12 col-md-12 pl-0 mt-2">
                                <label for="guardian_info[guardian_relation][1]">Relationship</label>
                                <select name="guardian_info[guardian_relation][1]" id="guardian_info[guardian_relation][1]" class="form-control selectpicker">
                                    <option value="null">Select Relation</option>';
                                    foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response .= "<option value=\"{$each->name}\">{$each->name}</option>";                            
                                    }
                            $response .= '</select>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_contact][1]">Contact Number</label>
                            <input type="text" name="guardian_info[guardian_contact][1]" id="guardian_info[guardian_contact][1]" class="form-control">
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label for="guardian_info[guardian_email][1]">Email Address</label>
                            <input type="text" name="guardian_info[guardian_email][1]" id="guardian_info[guardian_email][1]" class="form-control">
                        </div>
                        <div class="col-lg-1 col-md-1 text-right">
                            <div class="d-flex justify-content-end">
                                <div class="mr-1">
                                    <br>
                                    <button data-row="1" class="btn append-row btn-primary" type="button"><i class="fa fa-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }

                $response .= '</div>
            </div>
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-lg-12"><h5>ACADEMICS</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="class_id">Class</label>
                        <select name="class_id" id="class_id" class="form-control selectpicker">
                            <option value="null">Select Student Class</option>';
                            foreach($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->class_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select name="department" id="department" class="form-control selectpicker">
                            <option value="">Select Student Department</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->department) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="section">Section</label>
                        <select name="section" id="section" class="form-control selectpicker">
                            <option value="null">Select Student Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

}