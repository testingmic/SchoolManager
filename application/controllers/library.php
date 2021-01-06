<?php
/**
 * Library class extends Myschoolgh Model
 *
 * Loads the base classes and executes the request.
 *
 * @package		MySchoolGH
 * @subpackage	Students super class
 * @category	Library Controller
 * @author		Emmallen Networks
 * @link		https://www.myschoolgh.com/
 */
class Library extends Myschoolgh {

	public function __construct() {
		parent::__construct();
	}

    /**
     * List library books
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
    public function list(stdClass $params) {

        // create a new object
        $filesObject = load_class("forms", "controllers");

		$params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;

        // set the filters
        $filters = "";
        $filters .= isset($params->q) ? " AND bk.title LIKE '%{$params->q}%'" : "";
		$filters .= isset($params->class_id) ? " AND bk.class_id='{$params->class_id}'" : "";
        $filters .= isset($params->book_id) ? " AND bk.item_id='{$params->book_id}'" : "";
        $filters .= isset($params->category_id) ? " AND bk.category_id='{$params->category_id}'" : "";
        $filters .= isset($params->department_id) ? " AND bk.department_id='{$params->department_id}'" : "";
        $filters .= isset($params->isbn) ? " AND bk.isbn='{$params->isbn}'" : "";

        // query the database
		$stmt = $this->db->prepare("
			SELECT 
				".(isset($params->minified) ? 
					" 	bk.id, bk.item_id, bk.title, bk.description, bk.rack_no, bk.row_no, bk.book_image, bk.isbn, 
						bk.author, (SELECT quantity FROM books_stock WHERE books_id = bk.item_id) AS books_stock " : 
					" bk.*, (SELECT quantity FROM books_stock WHERE books_id = bk.item_id) AS books_stock,
                (SELECT name FROM classes WHERE classes.id = bk.class_id LIMIT 1) AS class_name,
                (SELECT name FROM departments WHERE departments.id = bk.department_id LIMIT 1) AS department_name,
                bt.name AS category_name,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = bk.created_by LIMIT 1) AS created_by_info,
                (SELECT b.description FROM files_attachment b WHERE b.resource='library_book' AND b.record_id = bk.item_id ORDER BY b.id DESC LIMIT 1) AS attachment ")."
			FROM books bk
			LEFT JOIN books_type bt ON bt.id = bk.category_id
			WHERE bk.deleted= ? AND bk.client_id = ? {$filters} ORDER BY bk.id DESC LIMIT {$params->limit} 
		");
		$stmt->execute([0, $params->clientId]);

        $data = [];

        // loop through the list of books
        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

            // if attachment variable was parsed
            $result->isbn = strtoupper($result->isbn);

			// if created_by_info is set
			if(isset($result->created_by_info)) {

				// convert the attachment into an object
				$result->attachment = json_decode($result->attachment);
            	$result->attachment_html = !empty($result->attachment->files) ? $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-4 col-md-6", false, false) : null;

				// loop through the information
				foreach(["created_by_info"] as $each) {
					// convert the created by string into an object
					$result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
				}
			}

			// if show_in_list was parsed
			if(isset($params->show_in_list)) {
				$result->in_session = $this->in_session_list($result->item_id, "{$params->show_in_list}_session");
			}
            
            $data[] = $result;
        }
        
        return [
            "data" => $data
        ];
    }

	/**
	 * @method book_borrowed_details
	 *
	 * This will convert the books list submitted as an array and fetch records for each item
	 *
	 * @param array $booksList
	 *
	 * @return bookName
	 **/
	public function book_borrowed_details($borrowed_id) {
		
		try {
			
			$stmt = $this->db->prepare("
				SELECT 
					bd.quantity, bd.fine, bd.actual_paid, bd.date_borrowed, bd.return_date,
					bk.isbn, bk.title, bk.item_id, bd.status, bd.id AS borrowed_row_id, bd.book_id
				FROM 
					books_borrowed_details bd
				LEFT JOIN books bk ON bk.item_id = bd.book_id
				WHERE borrowed_id = ? LIMIT 100
			");
			$stmt->execute([$borrowed_id]);
			
			// set the returned state to true
			$data = [];

			// using the while loop to fetch all the items
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$data[] = $result;
			}

			return [
				"data" => $data
			];

		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
     * List The Issued or Request List
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
	public function issued_request_list(stdClass $params) {

		global $accessObject;

		$params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

		// append to the parameter based on the user type logged in
		if($params->userData->user_type == "teacher") {
			$params->user_role = "teacher";
			$params->user_id = $params->userData->user_id;
		}

		// if the user is an employee and does not have the permission to issue
		if(in_array($params->userData->user_type, ["accountant", "employee"]) && !$accessObject->hasAccess("issue", "library")) {
			$params->user_role = $params->userData->user_type;
			$params->user_id = $params->userData->user_id;
		}

		// if the user is a student or parent
		if(in_array($params->userData->user_type, ["student", "parent"])) {
			$params->user_role = "student";
			$user_id = $params->userData->user_id;

			// if the user is a parent
			if($params->userData->user_type == "parent") {
				$user_id = $this->session->student_id;
			}

			// set the user id
			$params->user_id = $user_id;
		}

		// show the book list so long as the borrowed_id is parsed
		if(isset($params->borrowed_id)) {
			$params->show_list = true;
		}

		$params->query .= (isset($params->issued_date)) ? " AND a.issued_date='{$params->issued_date}'" : null;
		$params->query .= (isset($params->return_date)) ? " AND a.return_date='{$params->return_date}'" : null;
		$params->query .= (isset($params->status)) ? " AND a.status='{$params->status}'" : null;
        $params->query .= (isset($params->user_role)) ? " AND a.user_role='{$params->user_role}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
		$params->query .= (isset($params->borrowed_id)) ? " AND a.item_id='{$params->borrowed_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM books_borrowed_details b WHERE b.book_id = a.item_id LIMIT 1) AS books_count,
					(SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type,'|',b.unique_id) FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_info
                FROM books_borrowed a
                WHERE {$params->query} AND a.deleted = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				if(isset($params->show_list)) {
					$result->books_list = $this->book_borrowed_details($result->item_id);
				}

				// loop through the information
                foreach(["user_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type","unique_id"]);
                    }
                }

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
     * List library books category
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
	public function category_list(stdClass $params) {

		$params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->category_id)) ? " AND a.item_id='{$params->category_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM books b WHERE b.category_id = a.id AND b.client_id = a.client_id LIMIT 1) AS books_count,
					(SELECT name FROM departments WHERE departments.id = a.department_id LIMIT 1) AS department_name
                FROM books_type a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

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
     * Add Books Category
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
	public function add_category(stdClass $params) {

		try {

			# create a new unique id
			$item_id = random_string("alnum", 32);

            # execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO books_type SET client_id = ?, item_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $item_id, $params->userId]);
            
            # log the user activity
            $this->userLogs("library_category", $item_id, null, "{$params->userData->name} created a new category: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Book Category successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			# return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

	}

	/**
     * Update Books Category
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
	public function update_category(stdClass $params) {

		try {

			# old record
            $prevData = $this->pushQuery("*", "books_type", "item_id='{$params->category_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            # if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            # execute the statement
            $stmt = $this->db->prepare("
                UPDATE books_type SET name = '{$params->name}'
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
				WHERE client_id = ? AND item_id = ?
            ");
            $stmt->execute([$params->clientId, $params->category_id]);
            
            # log the user activity
            $this->userLogs("library_category", $params->category_id, null, "{$params->userData->name} updated the category.", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Book Category successfully updated."];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}update-book-category/{$params->category_id}/update"];

			# return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

	}

	/**
	 * @method check_borrowed_state
	 * @param stdClass $params 
	 *
	 * @return Bool
	 **/
	public function check_borrowed_state(stdClass $params) {

		try {

			$stmt = $this->db->prepare("SELECT status FROM books_borrowed WHERE id = ? AND students_id = ? AND client_id = ?");
			$stmt->execute([$params->bookId, $params->studentId, $params->clientId]);

			$result = $stmt->fetch(PDO::FETCH_OBJ);

			return ($result == "Borrowed") ? true : false;

		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * Issue or Request Handler
	 * 
	 * Use the label parameter to ascertain the action to perform
	 * 
	 * @return Array
	 */
	public function issue_request_handler(stdClass $params) {
		
		/** Return false if an array was not parsed */
		if(!is_array($params->label) || !isset($params->label["todo"]) || !isset($params->label["mode"])) {
			return ["code" => 203, "data" => "Sorry! The label parameter must be an array. Also ensure 'todo' and 'mode' was parsed"];
		}

		/** Assign variables */
		$todo = $params->label["todo"];
		$mode = $params->label["mode"];
		$book_id = $params->label["book_id"] ?? null;

		$the_session = "{$mode}_session";

		/** Switch through the label */
		if($params->label["todo"] == "add") {
			// quantity
			$quantity = $params->label["quantity"] ?? 1;

			// get the book details
			$param = (object) [
				"limit" => 1,
				"minified" => true,
				"book_id" => $book_id,
				"clientId" => $params->clientId
			];
			$book_info = $this->list($param)["data"];

			// confirm that the book exists
			if(empty($book_info)) {
				return ["code" => 203, "data" => "Sorry! Invalid book id was supplied"];
			}

			// add to session
			$this->add_book_to_session($the_session, $book_id, $quantity, $book_info[0]);

			// return the session list as the response
			return [
				"data" => [
					"books_list" => $this->session->$the_session
				]
			];
		}

		/** Remove book from session */
		elseif($params->label["todo"] == "remove") {
			// remove book from session
			$this->remove_book_from_session($the_session, $book_id);
			
			// return the session list as the response
			return [
				"data" => ["books_list" => $this->session->$the_session]
			];
		}

		/** List the books list in session */
		elseif($params->label["todo"] == "list") {
			// return the session list as the response
			return [
				"data" => ["books_list" => $this->session->$the_session]
			];
		}

		/** Issue the book out to the user */
		elseif(in_array($params->label["todo"], ["issue", "request"])) {

			// if the data is not parsed
			if(!isset($params->label["data"])) {
				return ["code" => 203, "data" => "Sorry! The data to be processed"];
			}

			// the books list
			if(!isset($params->label["data"]["books_list"])) {
				return ["code" => 203, "data" => "Sorry! You have not selected any books yet"];
			}

			// append more values
			$params->label["data"]["userId"] = $params->userId;
			$params->label["data"]["clientId"] = $params->clientId;
			$params->label["data"]["request"] = $params->label["todo"];
			$params->label["data"]["fullname"] = $params->userData->name;
			
			// issue book from session
			$request = $this->issue_book_to_user($params->label["data"], $the_session, $params->userId);

			// return the session list as the response
			return [
				"data" => $request ? "The request successfully processed." : "Sorry! There was an error while processing the request"
			];
		}

	}

	/**
	 * In Array List
	 * 
	 * Confirm that the book id is in the list of the session parameter parsed
	 * 
	 * @param	String	$book_id
	 * @param 	String	$the_session
	 * 
	 * @return Bool
	 */
	public function in_session_list($book_id, $the_session) {
		if($this->count_session_data($the_session)) {
			$book = array_column($_SESSION[$the_session], "book_id");
			return (bool) in_array($book_id, $book);
		}
		return false;
	}

	/**
	 * Add Book To Session
	 * 
	 * @param 	String	$quantity
	 * @param	String	$book_id
	 * @param 	String	$the_session
	 * @param	stdClass	$info	The summary details of the book
	 * 
	 * @return Bool
	 */
	public function add_book_to_session($the_session, $book_id, $quantity, $info) {
		if($this->count_session_data($the_session)) {
			foreach($_SESSION[$the_session] as $key => $value) {				
				if($value['book_id'] == $book_id) {
				 	$_SESSION[$the_session][$key]['quantity'] = $quantity;
				 	break;
				}				
			}
			$book_ids = array_column($_SESSION[$the_session], "book_id");
            if (!in_array($book_id, $book_ids)) {
            	$_SESSION[$the_session][] = ['book_id' => $book_id, 'quantity' => $quantity, 'info' => $info];
            }
		} else {
			$_SESSION[$the_session][] = ['book_id' => $book_id, 'quantity' => $quantity, 'info' => $info];
		}

		$this->session->set($the_session, $_SESSION[$the_session]);
		
		return true;
	}

	/**
	 * Remove Book from session
	 * 
	 * @param	String	$book_id
	 * @param 	String	$the_session
	 * 
	 * @return Bool
	 */
	public function remove_book_from_session($the_session, $book_id) {
		if($this->count_session_data($the_session)) {
			foreach($_SESSION[$the_session] as $key => $value) {					
				if($value['book_id'] == $book_id) {
				 	unset($_SESSION[$the_session][$key]);
				 	break;
				}				
			}
		}
		$this->session->set($the_session, $_SESSION[$the_session]);

		return true;
	}

	/**
	 * Count Session
	 * 
	 * @param String $session_name
	 * 
	 * @return Bool
	 */
    public function count_session_data($the_session) {
		return !empty($this->session->{$the_session}) ? true : false;
	}

	/**
	 * @method issue_book_to_user
	 *
	 * This handles the issuance of a book to the student
	 *
	 * @param $issueDate 	The date that the book is been given out
	 * @param $return_date 	The date that the student is supposed to return the book
	 * @param $studentId 	The Student ID Number
	 * @param $overdueRate	This is the fine for overdue
	 * @param $overdueApply	This is to check whether fine applies to all books or just the order
	 *
	 * @return bookName
	 **/
	public function issue_book_to_user($params, $the_session, $userId) {
		
		// convert the data parameter to an object
		$data = (object) $params;
		$status = ($params->request == "issue") ? "Issued" : "Requested";

		// confirm that the return date is not empty
		if(!isset($data->return_date)) {
			return ["code" => 203, "data" => "Sorry! The return date for this request must be set."];
		}

		// rate for overdue
		if(isset($data->overdue_apply) && ($data->overdue_apply !== "single") && !empty($data->overdue_rate)) {
			$eachBookRate = ($data->overdue_rate / count($data->books_list));
		} else {
			$eachBookRate = isset($data->overdue_rate) ? $data->overdue_rate : 0;
		}

		$books_list = [];
		foreach($data->books_list as $key => $value) {
			$books_list[] = $key;
		}

		$this->db->beginTransaction();
		
		try {

			$item_id = random_string("alnum", 32);

			$stmt = $this->db->prepare("
				INSERT INTO 
					books_borrowed
				SET
					client_id = ?, item_id = ?, user_id = ?, user_role = ?, books_id = ?, 
					issued_date = ?, return_date = ?, fine = ?, issued_by = ?, the_type = ?, status = ?
			");
			$stmt->execute([
				$data->clientId, $item_id, $data->user_id, $data->user_role ?? null, json_encode($books_list), 
				date("Y-m-d"), $data->return_date, $data->overdue_rate ?? 0, $userId, $data->request, $status
			]);

			foreach($data->books_list as $key => $value) {
				$stmt = $this->db->prepare("
					INSERT INTO 
						books_borrowed_details
					SET
						borrowed_id = ?, book_id = ?, quantity = ?,
						return_date = ?, fine = ?, issued_by = ?, received_by = ?
				");
				$stmt->execute([$item_id, $key, $value, $data->return_date, $eachBookRate, $userId, $data->user_id]);

				// reduce the books stock quantity
				$this->db->query("UPDATE books_stock SET quantity = (quantity - {$value}) WHERE books_id = '{$key}' LIMIT 1");
			}

			/** The Message */
			$message = $data->request == "issue" ? "Issued Books out to the User." : "Made a request for a list of Books.";

			/** Record the user activity **/
			$this->userLogs("books_borrowed", $item_id, null, "{$data->fullname} {$message}.", $data->userId);

			$this->session->{$the_session} = null;

			$this->db->commit();

			return true;
		} catch(PDOException $e) {
			$this->db->rollback();
			return [];
		}

	}

	/**
	 * @method booksFiltered
	 *
	 * This returns a list of books based on the filters that has been applied
	 *
	 * @param stdClass postData
	 *
	 * @return Array list of all book category and the count
	 **/
	public function booksFiltered(stdClass $params) {

		try {

			$filter = "";
			$filter .= isset($params->departmentId) ? "_books.department_id = '{$params->departmentId}'" :  null;
			$filter .= isset($params->programmeId) ? "_books.programme_id = '{$params->programmeId}'" :  null;

			$stmt = $this->db->prepare("
				SELECT
					bt.*, 
					(SELECT COUNT(*) FROM _books WHERE _books.category_id = bt.id AND _books.status = 1)
				FROM books_type bt
				WHERE bt.client_id = ? AND bt.status = ?
			");
			$stmt->execute([$this->session->userId, 1]);

			$results = array();

			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$results[] = $result;
			}

			return $results;

		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * @method booksBorrowedBooksListing
	 *
	 * This will convert the books list submitted as an array and fetch records for each item
	 *
	 * @param stdClass $booksList
	 *
	 * @return bookName
	 **/
	public function bookBorrowedBooksTitles($booksList, $linkType = 'link') {
		
		try {

			$booksTitle = '';
			$booksList = $this->stringToArray($booksList);

			foreach($booksList as $eachBook) {

				$stmt = $this->db->prepare("
					SELECT title, id FROM _books WHERE id = ?
				");
				$stmt->execute([$eachBook]);

				while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

					if($linkType == 'link') {
						$booksTitle .= "<a href='{$this->baseUrl}update-book/{$result->id}'>{$result->title}</a><br>";
					} else {
						$booksTitle .= "<a data-function=\"view-book\" data-book-id=\"{$result->id}\" href=\"javascript:void(0)\">{$result->title}</a><br>";
					}

				}
			}

			return $booksTitle;

		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * @method returnBorrowedBook
	 *
	 * This handles the return of any borrowed book either single or entire order
	 *
	 * @param $itemId 		This can either be the Order Id or single item in the order
	 * @param $returnOption	Either entire-order OR single-book
	 * @param $paymentAmount	The Amount that the Student has paid
	 * @param $overdueFine	Also parse the fine that is to be paid by the student
	 *
	 * @return boolean
	 **/
	public function returnBorrowedBook($itemId, $returnOption, $paymentAmount, $overdueFine) {
		
		/* Fetch the overdue fine that is stored in the database */
		if($returnOption == "entire-order") {
			$mainOrderId = $itemId;
			$booksCount =  $this->stringToArray($this->itemByIdNoStatus("books_borrowed", "id", $itemId, "books_id"));
			$dbOverdueAmount = $this->itemById("books_borrowed", "id", $itemId, "fine");
		}
		else {
			$mainOrderId = $this->itemByIdNoStatus("books_borrowed_details", "id", $itemId, "borrowed_id");
			$booksCount = $this->stringToArray($this->itemByIdNoStatus("books_borrowed", "id", $mainOrderId, "books_id"));
			$dbOverdueAmount = $this->itemById("books_borrowed_details", "id", $itemId, "fine");
		}

		/* Setting the records straight */
		$fine_paid = 0;
		/* Continue processing the form */
		if(($dbOverdueAmount <= $paymentAmount))
			$fine_paid = 1;
		
		/* if the user wants to return a single book */
		if($returnOption == "single-book") {
			/**
			 * Update the books borrowed data info
			 * If the books borrowed is One then set the status to 
			 * returned else just return this book only.
			*/
			$stmt = $this->db->prepare("
				UPDATE books_borrowed_details 
				SET status = ?, fine_paid = ?, actual_paid = ?, actual_date_returned = now()
				WHERE id = ?
			");
			$stmt->execute(['Returned', $fine_paid, $paymentAmount, $itemId]);

			/* Update the books borowed */
			if($booksCount == 1) {
				$stmt = $this->db->prepare("
					UPDATE books_borrowed
					SET status = ?, actual_paid = ?, fine_paid = ?, actual_date_returned = now()
					WHERE id = ?
				");
				$stmt->execute(['Returned', $paymentAmount, $fine_paid, $mainOrderId]);

				/* Record the user activity */
				$this->recordUserHistory(array($mainOrderId, 'books-borrowed', 'Recorded the return of a Book by a Student.'));
			}
		}

		/* If the return option is the entire order */
		elseif($returnOption == "entire-order") {
			/* Loop through the entire order list and do some mega processing */
			$stmt = $this->db->prepare("
				SELECT * FROM books_borrowed_details WHERE borrowed_id = ?
			");
			$stmt->execute([$mainOrderId]);
			/* Initializing */
			$paymentBalance = $paymentAmount;
			/* Using the while loop */
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				/* Fetch the fine for this item */
				$paymentAmount = $paymentBalance;
				if($result->fine < $paymentAmount) {
					$paymentBalance = $paymentAmount - $result->fine;
					$totalPaid = $result->fine;
					$fine_paid = 1;
				} elseif($result->fine == $paymentAmount) {
					$paymentBalance = 0.00;
					$totalPaid = $result->fine;
					$fine_paid = 1;
				} elseif($result->fine > $paymentAmount) {
					$paymentBalance = 0.00;
					$totalPaid = $paymentAmount;
					$fine_paid = 0;
				}

				/**
				 * Update the books borrowed data info
				 * If the books borrowed is One then set the status to 
				 * returned else just return this book only.
				*/
				$detailStmt = $this->db->prepare("
					UPDATE books_borrowed_details 
					SET status = ?, fine_paid = ?, actual_paid = ?, actual_date_returned = now()
					WHERE borrowed_id = ? AND id = ?
				");
				$detailStmt->execute(['Returned', $fine_paid, $totalPaid, $mainOrderId, $result->id]);
			}

			/* Setting the records straight */
			$fine_paid = 0;
			
			/* Continue processing the form */
			if(($dbOverdueAmount <= $paymentAmount))
				$fine_paid = 1;

			/* Update the main book borrowed order */
			$stmt = $this->db->prepare("
				UPDATE books_borrowed
				SET status = ?, actual_paid = ?, fine_paid = ?, actual_date_returned = now()
				WHERE id = ?
			");
			$stmt->execute(['Returned', xss_clean($_POST["paymentAmount"]), $fine_paid, $mainOrderId]);
			
		}

		return true;

	}

 	/**
	 * @method update_book
	 *
	 * This handles the update of a book in the system
	 *
	 * @param $bookTitle 	This is the title of the Book
	 * @param $bookISBN 	The ISBN for the Book to be recorded
	 * @param $bookAuthor 	The name of the Book Author
	 * @param $rack_no 		The number on the Rack where the book can be found
	 * @param $row_no 		The row on which rack that the book can be found
	 * @param $programme_id	The programme offered
	 * @param $departmentId The department of the Book
	 * @param $description 	The decription of the Book
	 * @param $quantity		The Quantity of the Book in Stock
	 * @param $bookId 		The Book ID to be updated
	 *
	 * @return boolean
	 **/
	public function update_book(stdClass $params) {

        // old record
        $prevData = $this->pushQuery("*", "books", "item_id='{$params->book_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid book id was supplied."];
        }

		// confirm that a logo was parsed
        if(isset($params->book_image)) {
            // set the upload directory
            $uploadDir = "assets/img/library/";
            // File path config 
            $fileName = basename($params->book_image["name"]); 
			$fileName = preg_replace("/[^a-zA-Z0-9._]/", "", $fileName);
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');            
            // check if its a valid image
            if(!empty($fileName) && in_array($fileType, $allowTypes)){
                // set a new filename
                $book_image = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->book_image["tmp_name"], $book_image)){}
            }
        }

		/** Record the user activity **/
		$query = $this->auto_update(
			array(
				"books",
				"isbn = ?, title = ?, description = ?, author = ?, rack_no = ?, row_no = ?, 
                    class_id = ?, category_id = ?, department_id = ? 
                    ".(isset($params->quantity) ? ",quantity = '{$params->quantity}'" : "")."
					".(isset($book_image) ? ",book_image = '{$book_image}'" : "")."
                    ".(isset($params->code) ? ",code = '{$params->code}'" : "")."",
				"item_id = ? AND client_id = ?",
				array(
					$params->isbn, $params->title, $params->description ?? null, 
                    $params->author, $params->rack_no ?? null, 
                    $params->row_no ?? null, $params->class_id ?? null, $params->category_id ?? null, 
                    $params->department_id ?? null, $params->book_id, $params->clientId
				)
			)
		);

		// if the query was successful
		if($query) {

			/** Record the user activity **/
			$this->userLogs("library_book", $params->book_id, null, "{$params->userData->name} updated the Book: {$params->title}", $params->userId);

			$return = ["code" => 200, "data" => "Library Book successfully updated.", "refresh" => 2000];
			$return["additional"] = ["href" => "{$this->baseUrl}update-book/{$params->book_id}/update"];

			return $return;
		}

	}

	/**
	 * @method add_book
	 *
	 * This handles the insertion of a new book
	 *
	 * @param $bookTitle 	This is the title of the Book
	 * @param $bookISBN 	The ISBN for the Book to be recorded
	 * @param $bookAuthor 	The name of the Book Author
	 * @param $rack_no 		The number on the Rack where the book can be found
	 * @param $row_no 		The row on which rack that the book can be found
	 * @param $class_id	The programme offered
	 * @param $departmentId The department of the Book
	 * @param $description 	The decription of the Book
	 * @param $quantity		The Quantity of the Book in Stock
	 *
	 * @return boolean
	 **/
	public function add_book(stdClass $params) {

        // generate a random string for the book
        $item_id = random_string("alnum", 32);
		
		$counter = $this->append_zeros(($this->itemsCount("books", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
        $code = $this->client_data($params->clientId)->client_preferences->labels->book.$counter;

		// confirm that a logo was parsed
        if(isset($params->book_image)) {
            // set the upload directory
            $uploadDir = "assets/img/library/";
            // File path config 
            $fileName = basename($params->book_image["name"]);
			$fileName = preg_replace("/[^a-zA-Z0-9._]/", "", $fileName);
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');            
            // check if its a valid image
            if(!empty($fileName) && in_array($fileType, $allowTypes)){
                // set a new filename
                $book_image = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->book_image["tmp_name"], $book_image)){}
            }
        }

		$query = $this->auto_insert(
			array(
				"books",
				"client_id = ?, isbn = ?, title = ?, description = ?, author = ?, rack_no = ?, 
                row_no = ?, class_id = ?, category_id = ?, department_id = ?, quantity = ?, code = ?,
                created_by = ?, item_id = ?, book_image = ?",
				array(
					$params->clientId, $params->isbn, $params->title, $params->description ?? null, $params->author, 
                    $params->rack_no ?? null, $params->row_no ?? null, $params->class_id ?? null, $params->category_id ?? null,  
                    $params->department_id ?? null, $params->quantity, $code, $params->userId, $item_id, $book_image ?? null
				)
			)
		);

		// if the query was successful
		if($query) {

			/** Create a new stock of the book  */
			$this->db->query("INSERT INTO books_stock SET books_id = '{$item_id}', quantity = '{$params->quantity}'");

			/** Record the user activity **/
			$this->userLogs("library_book", $item_id, null, "{$params->userData->name} added the Book: {$params->title}", $params->userId);

			$return = ["code" => 200, "data" => "Library Book successfully added.", "refresh" => 2000];
			$return["additional"] = ["href" => "{$this->baseUrl}update-book/{$item_id}/view", "clear" => true];

			return $return;
		}
	}
    
    /**
     * Upload Resource
     * 
     * Upload e-version of the resources attached to this resource
     * 
     * @return Array 
     */
    public function upload_resource(stdClass $params) {
        
        // old record
        $module = "ebook_{$params->book_id}";
        $prevData = $this->pushQuery("a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
            "books a", "a.item_id='{$params->book_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid book id was supplied."];
        }

        // return error message if no attachments has been uploaded
        if(empty($this->session->{$module})) {
            return ["code" => 203, "data" => "Sorry! Please upload files to be uploaded."];
        }

        // initialize
        $initial_attachment = [];

        /** Confirm that there is an attached document */
        if(!empty($prevData[0]->attachment)) {
            // decode the json string
            $db_attachments = json_decode($prevData[0]->attachment);
            // get the files
            if(isset($db_attachments->files)) {
                $initial_attachment = $db_attachments->files;
            }
        }

        // prepare the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments($module, $params->userId, $params->book_id, $initial_attachment);
        
        // update attachment if already existing
        if(isset($db_attachments)) {
            $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND resource = ? LIMIT 1");
            $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $params->book_id, "library_book"]);
        } else {
            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["library_book", $params->book_id, json_encode($attachments), $params->book_id, $params->userId, $attachments["raw_size_mb"]]);
        }
        
        // fetch the files again
        $prevData = $this->pushQuery("a.id, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
            "books a", "a.item_id='{$params->book_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1");
        
        // decode the json string
        $db_attachments = json_decode($prevData[0]->attachment);
        $attachment_html = load_class("forms", "controllers")->list_attachments($db_attachments->files, $params->userId, "col-lg-4 col-md-6", false, false);

        return [
            "code" => 200,
            "data" => "Files successfully uploaded",
            "additional" => [
                "files_list" => $attachment_html
            ]
        ];

    }

}