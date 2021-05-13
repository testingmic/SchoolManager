<?php
class Promotion extends Myschoolgh {

     public function __construct(stdClass $params = null)
    {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? null;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
    }

    /**
     * List all the promotions log
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query = "";
        $params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->student_id) && !empty($params->student_id)) ? " AND a.student_id LIKE '%{$params->student_id}%'" : null;
        $params->query .= (isset($params->promote_to) && !empty($params->promote_to)) ? " AND a.promote_to LIKE '%{$params->promote_to}%'" : null;
        $params->query .= (isset($params->promote_from) && !empty($params->promote_from)) ? " AND a.promote_from LIKE '%{$params->promote_from}%'" : null;
        $params->query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $params->query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

        try {

            $stmt = $this->db->prepare("SELECT a.*,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_from LIMIT 1) AS from_class_name,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_to LIMIT 1) AS to_class_name
                FROM promotions_log a
                WHERE 1 {$params->query} LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
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
     * List Students
     * 
     * @return Array
     */
    public function students(stdClass $params) {

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        
        // if the class id is empty
        if(empty($params->class_id)) {
            return ["code" => 201,"data" => $this->is_required("Class")];
        }

        $params->query = "";
        $params->query .= isset($params->clientId) && !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->class_id) ? " AND c.item_id = '{$params->class_id}'" : null;
        $params->query .= isset($params->student_id) && !empty($params->student_id) ? " AND a.item_id = '{$params->student_id}'" : null;
        $params->query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $params->query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

        try {

            $stmt = $this->db->prepare("SELECT a.item_id, a.unique_id, a.firstname, a.lastname, a.name, 
                    a.image, a.gender, a.enrollment_date, a.email, a.date_of_birth, a.class_id, c.name AS class_name,
                    (
                        SELECT b.is_promoted FROM promotions_log b 
                        WHERE 
                            b.student_id = a.item_id AND 
                            a.academic_term = b.academic_term AND 
                            a.academic_year = b.academic_year
                        LIMIT 1
                    ) AS is_promoted
                FROM users a
                LEFT JOIN classes c ON c.id = a.class_id
                WHERE 1 {$params->query} AND a.user_type = ? LIMIT {$params->limit}
            ");
            $stmt->execute(["student"]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $result->is_promoted = empty($result->is_promoted) ? 0 : $result->is_promoted;
                $data[] = $result;
            }

            // confirm if the promotions list was parsed
            $params->limit = 1;
            $params->promote_from = $params->class_id;

            return [
                "code" => 200,
                "data" => [
                    "students_list" => $data,
                    "promotion_log" => (bool) !empty($this->list($params)["data"])
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

}