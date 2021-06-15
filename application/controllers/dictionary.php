<?php 

class Dictionary extends Myschoolgh {

    public function __construct()
    {
        $this->ss_type = [
            "n" => "noun",
            "v" => "verb",
            "s" => "adverb",
            "r" => "adverb",
            "a" => "adjective"
        ];
        parent::__construct();
    }

    /**
     * Search for a Word
     * 
     * @return Array
     */
    public function search(stdClass $params) {

        try {

            $params->term = strtolower($params->term);
            
            $search_query = "";
            $params->deep_search = true;
            $search_query .= isset($params->deep_search) ? "LIKE '%{$params->term}%'" : "LIKE '{$params->term}'";

            $search = $this->db->prepare("SELECT a.*,
                    g.gloss AS glossary
                FROM wn_synset a 
                    LEFT JOIN wn_gloss g ON g.synset_id = a.synset_id
                WHERE a.word {$search_query}
            ");
            $search->execute();
            $data = [];
            while($result = $search->fetch(PDO::FETCH_OBJ)) {
                $result->hyponym = $this->hyponym($result->synset_id);
                $data[$this->ss_type[$result->ss_type]][] = $result;
            }

            return [
                "search_list" => $data,
                "count" => count($data)
            ];
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get the Hyponym
     * 
     * @return Array
     */
    private function hyponym($synset_id) {
        try {

            $search = $this->db->prepare("SELECT 
                    w.word, w.sense_number, w.synset_id, w.tag_count,
                    g.gloss AS glossary
                FROM wn_hyponym a
                    LEFT JOIN wn_gloss g ON g.synset_id = a.synset_id_1
                    LEFT JOIN wn_synset w ON w.synset_id = a.synset_id_2
                WHERE a.synset_id_2= ? LIMIT 1
            ");
            $search->execute([$synset_id]);
            return $search->fetchAll(PDO::FETCH_OBJ);

        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get the Hypernym
     * 
     * @return Array
     */
    private function hypernym($synset_id) {
        try {

            $search = $this->db->prepare("SELECT w.word, w.sense_number, w.synset_id, g.gloss AS glossary
                FROM wn_hypernym a
                    LEFT JOIN wn_gloss g ON g.synset_id = a.synset_id_2
                    LEFT JOIN wn_synset w ON w.synset_id = a.synset_id_1
                WHERE a.synset_id_1= ? LIMIT 1
            ");
            $search->execute([$synset_id]);
            return $search->fetchAll(PDO::FETCH_OBJ);

        } catch(PDOException $e) {
            return [];
        }
    }

}
?>