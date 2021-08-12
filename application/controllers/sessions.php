<?php

class Sessions extends Myschoolgh {
	
	public function __construct() {
		parent::__construct();
	}

	private function count($session_name) {
		return (isset($_SESSION[$session_name]) and count($_SESSION[$session_name]) > 0) ? true : false;
	}

	public function add($session_name, $first, $second, $third = null, $fourth = null, $fifth = null) {
		
		if($this->count($session_name)) {
			foreach($_SESSION[$session_name] as $key => $value) {			
				if($value['first'] == $first) {
				 	$_SESSION[$session_name][$key]['second'] = trim($second);
				 	(!empty($third)) ? ($_SESSION[$session_name][$key]['third'] = trim($third)) : null;
				 	(!empty($fourth)) ? ($_SESSION[$session_name][$key]['forth'] = trim($fourth)) : null;
				 	(!empty($fifth)) ? ($_SESSION[$session_name][$key]['fifth'] = trim($fifth)) : null;
				 	break;
				}
			}

			$itemId = array_column($_SESSION[$session_name], "first");

            if (!in_array($first, $itemId)) {
            	if(empty($third)) {
            		$_SESSION[$session_name][] = array(
						'first'=>$first, 
						'second' => trim($second)
					);
            	} else {
            		if(!empty($fourth)) {
            			if(!empty($fifth)) {
            				$_SESSION[$session_name][] = array(
								'first'=>$first, 
								'second' => trim($second),
								'third' => trim($third),
								'forth' => trim($fourth),
								'fifth' => trim($fifth)
							);
            			} else {
	            			$_SESSION[$session_name][] = array(
								'first'=>$first, 
								'second' => trim($second),
								'third' => trim($third),
								'forth' => trim($fourth),
							);
	            		}
            		} else {
	            		$_SESSION[$session_name][] = array(
							'first'=>$first, 
							'second' => trim($second),
							'third' => trim($third)
						);
	            	}
            	}
            	
            }

		} else {
			if(empty($third)) {
				$_SESSION[$session_name][] = array(
					'first' => $first,
					'second' => $second
				);
			} else {
				if(empty($fourth)) {
					$_SESSION[$session_name][] = array(
						'first' => $first,
						'second' => $second,
						'third' => trim($third)
					);
				} else {
					if(!empty($fifth)) {
						$_SESSION[$session_name][] = array(
							'first'=>$first, 
							'second' => trim($second),
							'third' => trim($third),
							'forth' => trim($fourth),
							'fifth' => trim($fifth)
						);
					} else {
						$_SESSION[$session_name][] = array(
							'first'=>$first, 
							'second' => trim($second),
							'third' => trim($third),
							'forth' => trim($fourth),
						);
					}
				}
			}
		}

		$this->session->set($session_name, $_SESSION[$session_name]);
	}

	public function remove($session_name, $itemId, $unlink = false) {
		// count the rows
		if($this->count($session_name)) {
			// loop through the array item
			foreach($_SESSION[$session_name] as $key => $value) {
				// confirm if the item id parsed is in the array
				if($value['first'] == $itemId) {
					// unset the session
				 	unset($_SESSION[$session_name][$key]);
				 	// unlink the file
				 	if($unlink) {
				 		// confirm the file exists
				 		if(is_file($unlink.$itemId) && file_exists($unlink.$itemId)) {
				 			// delete the file
				 			unlink($unlink.$itemId);
				 		}
				 	}
					return true;
				 	break;
				}				
			}
		}

	}

	public function clear($session_name, $unlink) {

		// ensure that the session is not empty
		if($this->count($session_name)) {
			
			// loop through the session data list
			foreach($_SESSION[$session_name] as $key => $value) {
				
				// confirm the file exists
				if(is_file($unlink.$value['first']) && file_exists($unlink.$value['first'])) {
					
					// delete the file
					unlink($unlink.$value['first']);
				}
			}
			unset($_SESSION[$session_name]);

			// return true
			return true;
		}

	}
	
}
?>