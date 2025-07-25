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
			
			$params->lookup = trim($params->lookup);

			// exempt the
			$data = [];
			$exemption_list = [];
			$params->limit = 50;
			
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
				return ["code" => 400, "data" => "Sorry! The search term is required."];
			}

			// convert the params into an array string
			$param_array = (array) $params;
			$param_array = array_keys($param_array);

			// SEARCH FOR USERS AND GROUP THEM
			$data["users_list"] = $usersClass->quick_list($params)["data"];

			// set the book title and author
			$params->title = !empty($params->title) && is_array($params->title) ? $params->title[0] : null;
			$params->bookauthor = !empty($params->bookauthor) && is_array($params->bookauthor) ? $params->bookauthor[0] : null;
			$data["library_books_list"] = load_class("library", "controllers")->list($params)["data"] ?? [];

			// set the academic year and term to null
			$params->academic_year = null;
			$params->academic_term = null;
			$params->query = '';

			// get the payment id
			$params->receipt_id = $params->lookup;
			$data["fees_payment_receipt"] = load_class("fees", "controllers")->list($params)["data"] ?? [];;

			// return the data
			return $data;

		} catch(PDOException $e) {}

	}
}
?>