<?php

class Housing extends Myschoolgh {

    public $iclient;
    public $buildingsObject;
    public $blocksObject;
    public $roomsObject;
    public $bedsObject;

    public $housingData = [
        'roomCondition' => [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
            'needs_repair' => 'Needs Repair',
            'out_of_order' => 'Out of Order',
        ],
        'buildingType' => [
            'dormitory' => 'Dormitory',
            'boarding_house' => 'Boarding House',
            'hostel' => 'Hostel',
        ],
        'genderRestriction' => [
            'male_only' => 'Male Only',
            'female_only' => 'Female Only',
            'mixed' => 'Mixed Gender',
        ],
        'housingFacilities' => [
            'common_room' => 'Common Room',
            'study_area' => 'Study Area',
            'kitchen_kitchenette' => 'Kitchen/Kitchenette',
            'laundry_facilities' => 'Laundry Facilities',
            'shared_bathrooms' => 'Shared Bathrooms',
            'wifi_access' => 'WiFi Access',
            'air_conditioning' => 'Air Conditioning',
            'heating' => 'Heating',
            'security_cameras' => 'Security Cameras',
            'card_access_system' => 'Card Access System',
            'elevator_access' => 'Elevator Access',
            'disabled_access' => 'Disabled Access',
            'fire_safety_equipment' => 'Fire Safety Equipment',
            'water_dispenser' => 'Water Dispenser',
            'recreation_area' => 'Recreation Area',
        ],
        'roomFacilities' => [
            'mattress' => 'Mattress',
            'pillow' => 'Pillow',
            'bed_sheet' => 'Bed Sheet',
            'blanket' => 'Blanket',
            'study_table' => 'Study Table',
            'study_chair' => 'Study Chair',
            'wardrobe_closet' => 'Wardrobe/Closet',
            'bookshelf' => 'Bookshelf',
            'bedside_table' => 'Bedside Table',
            'reading_lamp' => 'Reading Lamp',
            'power_outlet' => 'Power Outlet',
            'ethernet_port' => 'Ethernet Port',
            'storage_boxes' => 'Storage Boxes',
            'mirror' => 'Mirror',
            'wall_hooks' => 'Wall Hooks',
        ],
        'roomType' => [
            'single' => 'Single',
            'double' => 'Double',
            'triple' => 'Triple',
            'quad' => 'Quad',
        ]
    ];

    public function __construct($params = null) {
		parent::__construct();
        $this->iclient = $params->client_data ?? [];
	}

    /**
     * Buildings Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function buildingsHandler($params, $action = "list") {
        $this->buildingsObject = load_class("Buildings", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->buildingsObject, $method)) {
                return $this->buildingsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Blocks Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function blocksHandler($params, $action = "list") {
        $this->blocksObject = load_class("Blocks", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->blocksObject, $method)) {
                return $this->blocksObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Rooms Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function roomsHandler($params, $action = "list") {
        $this->roomsObject = load_class("Rooms", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->roomsObject, $method)) {
                return $this->roomsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Beds Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function bedsHandler($params, $action = "list") {
        $this->bedsObject = load_class("Beds", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->bedsObject, $method)) {
                return $this->bedsObject->{$method}($params);
            }
        }
        return [];
    }
    
}
?>