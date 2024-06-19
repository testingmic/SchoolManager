<?php

class Quiz extends Myschoolgh {

	public function __construct() {
		parent::__construct();
	}

	public function quiz_history(stdClass $params) {

		// initialize the query string
        global $defaultUser;

        $filter = 1;

        if($defaultUser->user_type == 'student') {
        	$filter .= " AND users.item_id='{$defaultUser->user_id}'";
        }

        // set the query
        $stmt = $this->db->prepare("
            SELECT *, 
                users.name, tc.category_id,
                (SELECT b.test_title FROM quiz_question_instructions b WHERE b.instruction_id = q.instruction_id) AS test_title,
                tc.name as category_name, tc.description, q.date_log AS test_date
            FROM quiz_test_history q
                LEFT JOIN users ON users.item_id=q.user_id
                LEFT JOIN quiz_test_categories tc ON q.category_id=tc.category_id
            WHERE {$filter} ORDER BY q.id DESC
        ");
        $stmt->execute();

        $data = [];

        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

        	$data[] = $result;

        }

        return [
        	"code" => 200,
        	"data" => $data,
        ];


	}

}