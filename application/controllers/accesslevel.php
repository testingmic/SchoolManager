<?php
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Accesslevel {

    public $userId;
    public $clientId;
    public $appPrefs;
    public $userPermits = null;
    private $_message = '';
    private $db;
    private $session;

    public function __construct(){
        global $myschoolgh, $session;
        
        $this->db = $myschoolgh;
        $this->session = $session;
    }

    /**
     * A method to fetch access level details from DB
     *
     * @param String $accessLevel Pass level id to fetch details
     *
     * @return Object $this->_message
     */
    public function getPermissions($accessLevel = false) {
        $this->_message = false;

        // convert to uppercase
        $accessLevel = strtoupper($accessLevel);

        // set the condition for loading the user permission
        $condition = ($accessLevel == false) ? "1" : " (id = '{$accessLevel}' OR name = '{$accessLevel}')";

        // prepare the statement
        $stmt = $this->db->prepare("SELECT * FROM users_types WHERE {$condition}");

        if ($stmt->execute()) {
            $this->_message = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        return $this->_message;
    }

    /**
     * A method to fetch access level details from DB
     *
     * @param String $accessLevel Pass level user_id to fetch details
     *
     * @return Object $this->_message
     */
    public function getUserPermissions() {
        $this->_message = false;
        $clientId = !empty($this->clientId) ? $this->clientId : $this->session->clientId;

        $stmt = $this->db->prepare("SELECT * FROM users_roles WHERE user_id = '{$this->userId}' AND client_id = '{$clientId}'");

        if ($stmt->execute()) {
            $this->_message = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        return $this->_message;
    }

    /**
     * A method to save SMS Into history
     *
     * @param String $message Pass message to send
     * @param String $to      Pass recipients number
     *
     * @return Bool $this->_message
     */
    public function assignUserRole($userID, $accessLevel, $permissions = false) {
        $this->_message = false;

        $clientId = !empty($this->clientId) ? $this->clientId : $this->session->clientId;

        if ($permissions == false) {

            // Get Default Permissions
            $permissions = ($this->getPermissions($accessLevel) != false) ? 
                $this->getPermissions($accessLevel)[0]->default_permissions : null;

            $stmt = $this->db->prepare("
                INSERT INTO users_roles SET user_id = '{$userID}', client_id = ?, permissions = '{$permissions}'
            ");

            if ($stmt->execute([$clientId])) {
                $this->_message = true;
            }
        } else {

            $stmt = $this->db->prepare("
                UPDATE users_roles SET permissions = '".(is_array($permissions) ? json_encode($permissions) : $permissions)."' WHERE user_id = '{$userID}' AND client_id = '{$clientId}' LIMIT 1
            ");

            if ($stmt->execute()) {
                $this->_message = true;
            }
        }

        return $this->_message;
    }

    /**
     * Confirm that the user has permission
     * 
     * @return Bool
     */
    public function hasAccess($role, $currentPage = null) {
        
        // set the isSupportPreviewMode
        global $isSupportPreviewMode;

        // Check User Roles Table
        $permits = !empty($this->userPermits) ? $this->userPermits : [];

        // check if the session is in preview mode
        if($this->session->previewMode && $isSupportPreviewMode) {
            return true;
        }
        
        // user permissions
        if ($permits != false) {

            // code the user permissions section
            $permit = empty($this->userPermits) ? json_decode($permits[0]->permissions) : (!is_object($this->userPermits) ? json_decode($this->userPermits) : $this->userPermits);
            $permissions = $permit->permissions;
            
            // confirm that the requested page exists
            if(!isset($permissions->$currentPage)) {
                return false;
            }

            // check if the session is_only_readable_app as been parsed
            if(!empty($this->session->is_only_readable_app)) {
                // then the user has no permission to delete any record
                if(in_array($role, ["delete"])) {
                    return false;
                }
            }

            // confirm that the role exists
            if(isset($permissions->$currentPage->$role)) {
                return (bool) ($permissions->$currentPage->$role == 1) ? true : false;
            } else {
                return false;
            }
        }
    }

}