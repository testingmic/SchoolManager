<?php

class Search extends Myschoolgh {

	public function __construct() {
		parent::__construct();
	}

	final function advanced_filter($term, $keyword) {

		$split = explode($keyword, $term);

		// remove all other query
		if(isset($split[1])) {
			$sc = explode(",", $split[1]);
			$split = $sc[0];
		}
		return $split;

	}

	/**
	 * Advance Search for Data from all Tables
	 * 
	 * @return Array
	 */
	public function lookup(stdClass $params) {

		try {

			// global variable
			global $usersClass;

			// append the quick_user_search
			$params->quick_user_search = true;
			$params->group_by_user_type = true;
			$params->minified = "no_status_filters";
			$params->lookup = $params->term ?? null;

			// exempt the
			$data = [];
			$exemption_list = [];
			$params->limit = 250;
			
			// replace the keys parsed with its equivalent table column
			$queryString = [
				"userstatus" => "user_status", "usertype" => "user_type", 
				"uniqueid" => "unique_id", "userid" => "user_id", "gender" => "gender",

				// LIBRARY BOOKS
				"booktitle" => "title", "isbn" => "isbn", "bookauthor" => "author", 

				// FEES PAYMENT
				"feesreceipt" => "receipt_id", "paymentid" => "payment_id", "receipt" => "receipt_id"
			];

			// set the array of the function
			$funcs = ["user_status" => "ucfirst", "gender" => "ucfirst"];

			// loop through the possible search filter
			foreach(array_keys($queryString) as $value) {
				// loop through the filter
				$delimit = "=";
				// add some advanced filters for user_status
				if( strpos($params->lookup, "{$value}{$delimit}") !== false ) {
					// set the filters
					$filter = $queryString[$value];
					// use the advanced filter
					$params->{$filter} = $this->advanced_filter($params->lookup, "{$value}{$delimit}");
					// replace the items
					$params->lookup = str_ireplace(["{$value}{$delimit}", $params->{$filter}, ","], "", $params->lookup);
					// convert to an array
					$params->{$filter} = $this->stringToArray($params->{$filter}, ".", null, false, $funcs[$filter] ?? "isNull");
				}
			}

			// return empty if the term to search is empty
			if(empty($params->term)) {
				return ["code" => 203, "data" => "Sorry! The search term is required."];
			}

			// convert the params into an array string
			$param_array = (array) $params;
			$param_array = array_keys($param_array);

			// If the title param was not parsed
			if(empty(array_intersect(["title", "isbn", "author", "receipt_id", "payment_id"], $param_array))) {
				// SEARCH FOR USERS AND GROUP THEM
				$data["users_list"] = $usersClass->quick_list($params)["data"];
			}

			// if the title, isbn or author are not empty
			if(!empty(array_intersect(["title", "isbn", "author"], $param_array))) {
				// set a new limit of rows to return
				$params->limit = 50;

				// set the book title and author
				$params->title = !empty($params->title) && is_array($params->title) ? $params->title[0] : null;
				$params->bookauthor = !empty($params->bookauthor) && is_array($params->bookauthor) ? $params->bookauthor[0] : null;
				$data["library_books_list"] = load_class("library", "controllers")->list($params)["data"] ?? [];
			}

			// download fees receipt
			if(!empty(array_intersect(["receipt_id", "payment_id"], $param_array))) {
				// set a new limit of rows to return
				$params->limit = 20;

				// get the payment id
				$params->payment_id = !empty($params->payment_id) && is_array($params->payment_id) ? $params->payment_id[0] : null;
				$data["fees_payment_receipt"] = load_class("fees", "controllers")->list($params)["data"] ?? [];;
			}

			return $data;

		} catch(PDOException $e) {}

	}
}
?>