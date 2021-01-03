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
        $filters .= isset($params->class_id) ? " AND bk.class_id='{$params->class_id}'" : "";
        $filters .= isset($params->book_id) ? " AND bk.item_id='{$params->book_id}'" : "";
        $filters .= isset($params->category_id) ? " AND bk.category_id='{$params->category_id}'" : "";
        $filters .= isset($params->department_id) ? " AND bk.department_id='{$params->department_id}'" : "";
        $filters .= isset($params->isbn) ? " AND bk.isbn='{$params->isbn}'" : "";

        // query the database
		$stmt = $this->db->prepare("
			SELECT 
				bk.*, (SELECT quantity FROM books_stock WHERE books_id = bk.id) AS books_stock,
                (SELECT name FROM classes WHERE classes.id = bk.class_id LIMIT 1) AS class_name,
                (SELECT name FROM departments WHERE departments.id = bk.department_id LIMIT 1) AS department_name,
                bt.name AS category_name,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = bk.created_by LIMIT 1) AS created_by_info,
                (SELECT b.description FROM files_attachment b WHERE b.resource='library_book' AND b.record_id = bk.item_id ORDER BY b.id DESC LIMIT 1) AS attachment
			FROM books bk
			LEFT JOIN books_type bt ON bt.id = bk.category_id
			WHERE bk.deleted= ? AND bk.client_id = ? {$filters} ORDER BY bk.id DESC LIMIT {$params->limit} 
		");
		$stmt->execute([0, $params->clientId]);

        $data = [];

        // loop through the list of books
        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            
            // loop through the information
            foreach(["created_by_info"] as $each) {
                // convert the created by string into an object
                $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
            }

            // if attachment variable was parsed
            $result->isbn = strtoupper($result->isbn);
			$result->attachment = json_decode($result->attachment);

            // if the files is set
            $result->attachment_html = !empty($result->attachment->files) ? $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-4 col-md-6", false, false) : null;
            
            $data[] = $result;
        }
        
        return [
            "data" => $data
        ];
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
	 * @method checkBorrowedState
	 * @param stdClass $params 
	 *
	 * @return Bool
	 **/
	public function checkBorrowedState(stdClass $params) {

		try {

			$stmt = $this->db->prepare("SELECT status FROM books_borrowed WHERE id = ? AND students_id = ? AND client_id = ?");
			$stmt->execute([$params->bookId, $params->studentId, $params->clientId]);

			$result = $stmt->fetch(PDO::FETCH_OBJ);

			return ($result == "Borrowed") ? true : false;

		} catch(PDOException $e) {
			return false;
		}
	}

	public function categoryBooksCounting() {

		try {

			$stmt = $this->db->prepare("
				SELECT
					bt.*, 
					(SELECT COUNT(*) FROM _books WHERE _books.category_id = bt.id AND _books.status = 1) AS books_counted,
					dept.name AS department_name
				FROM books_type bt
				LEFT JOIN departments dept ON dept.id = bt.department_id
				WHERE bt.status = ? AND bt.client_id = ?
			");
			$stmt->execute([1, $params->clientId]);

			$results = array();

			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$results[] = $result;
			}

			return $results;

		} catch(PDOException $e) {
			return $e->getMessage();
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
	 * @method booksBorrowedBooksListing
	 *
	 * This will convert the books list submitted as an array and fetch records for each item
	 *
	 * @param array $booksList
	 *
	 * @return bookName
	 **/
	public function bookBorrowedBooksDetails($borrowedId) {
		
		try {
			$booksList = "";
			$stmt = $this->db->prepare("
				SELECT 
					bd.quantity, bd.fine, bd.date_borrowed, bd.return_date,
					bk.isbn, bk.title, bd.status, bd.id AS borrowed_row_id, bd.book_id
				FROM 
					books_borrowed_details bd
				LEFT JOIN _books bk ON bk.id = bd.book_id
				WHERE borrowed_id = ?
			");
			$stmt->execute([$borrowedId]);
			// set the returned state to true
			$due_button = '<span class="badge badge-success">Active</span>';
			$due_note = 'active';
			// using the while loop to fetch all the items
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// if the user did not request for showing the details fo the books
				// then print only the book title
				// check if the due date has expired
				if($result->status == "Borrowed") {
					// set it as overdue
					if(strtotime($result->return_date) < time()) {
						$due_button = '<span class="badge badge-danger">Overdue</span>';
						$due_note = 'overdue';
					}
				} elseif($result->status == "Returned") {
					$due_button = '';
				}
				//print_r($result);
				$booksList .= "<tr>";
				$booksList .= "<td><a data-function=\"view-book\" href='javascript:void(0)' data-book-id=\"{$result->book_id}\">{$result->title}</a> {$due_button} <br><strong>ISBN: <small>{$result->isbn}</small></strong></td>";
				$booksList .= "<td>{$result->quantity}</td>";
				$booksList .= "<td>{$result->return_date}</td>";
				$booksList .= "<td>{$result->fine}</td>";
				$booksList .= "<td>".(($result->status == "Borrowed") ? "<button data-note=\"$due_note\" data-amount=\"".substr($result->fine, 0, -3)."\" data-book-id=\"{$result->borrowed_row_id}\" title=\"Return Book\" data-function=\"return-book\" data-mode=\"single-book\" class=\"btn btn-outline-warning\"><i class=\"fa fa-reply\"></i> Return</button>" : "<span class=\"badge badge-success\">Returned</span>")."</td>";
				$booksList .= "</tr>";
			}

			return $booksList;

		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}

    public function countSessionData() {

		return (isset($_SESSION['borrowedBookSession']) and count($_SESSION['borrowedBookSession']) > 0) ? true : false;
	}

	/**
	 * @method issueBooksToStudent
	 *
	 * This handles the issuance of a book to the student
	 *
	 * @param $issueDate 	The date that the book is been given out
	 * @param $returnDate 	The date that the student is supposed to return the book
	 * @param $studentId 	The Student ID Number
	 * @param $overdueRate	This is the fine for overdue
	 * @param $overdueApply	This is to check whether fine applies to all books or just the order
	 *
	 * @return bookName
	 **/
	public function issueBooksToStudent($issueDate, $returnDate, $studentId, $overdueRate, $overdueApply) {

		if($this->countSessionData()) {
			
			if($overdueApply != "each-book") {
				$eachBookRate = ($overdueRate / count($_SESSION['borrowedBookSession']));
			} else {
				$eachBookRate = $overdueRate;
			}

			$books_list = '';
			foreach($_SESSION['borrowedBookSession'] as $key => $value) {
				$books_list .= $value['book_id'].'||';
			}

			$books_list = substr($books_list, 0, -2);

			$this->db->beginTransaction();
			
			try {

				$stmt = $this->db->prepare("
					INSERT INTO 
						books_borrowed
					SET
						client_id = ?,
						student_id = ?,
						books_id = ?,
						issueDate = ?,
						returnDate = ?,
						fine = ?,
						issued_by = ?
				");
				$stmt->execute([$params->clientId, $studentId, $books_list, $issueDate, $returnDate, $overdueRate, $this->session->userId]);

				$lastRowId = $this->lastRowId("books_borrowed", "id");

				foreach($_SESSION['borrowedBookSession'] as $key => $value) {
					$stmt = $this->db->prepare("
						INSERT INTO 
							books_borrowed_details
						SET
							borrowed_id = ?,
							book_id = ?,
							quantity = ?,
							return_date = ?,
							fine = ?,
							issued_by = ?,
							received_by = ?
					");
					$stmt->execute([$lastRowId, $value['book_id'], $value['quantity'], $returnDate, $eachBookRate, $this->session->userId, $studentId]);
				}

				/** Record the user activity **/
                $this->userLogs("books_borrowed", $lastRowId, null, "{$params->userData->name} Issued Books out to a Student.", $params->userId);

				unset($_SESSION['borrowedBookSession']);

				$this->db->commit();

				return true;
			} catch(PDOException $e) {
				$this->db->rollback();
				return $e->getMessage();
			}

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

		/** Record the user activity **/
		$query = $this->auto_update(
			array(
				"books",
				"isbn = ?, title = ?, description = ?, author = ?, rack_no = ?, row_no = ?, 
                    class_id = ?, category_id = ?, department_id = ? 
                    ".(isset($params->quantity) ? ",quantity = '{$params->quantity}'" : "")."
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

        /** Record the user activity **/
		$this->userLogs("library_book", $params->book_id, null, "{$params->userData->name} updated the Book: {$params->title}", $params->userId);

        $return = ["code" => 200, "data" => "Library Book successfully updated.", "refresh" => 2000];
		$return["additional"] = ["href" => "{$this->baseUrl}update-book/{$params->book_id}/update"];

        return $return;

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

		$query = $this->auto_insert(
			array(
				"books",
				"client_id = ?, isbn = ?, title = ?, description = ?, author = ?, rack_no = ?, 
                row_no = ?, class_id = ?, category_id = ?, department_id = ?, quantity = ?, code = ?,
                created_by = ?, item_id = ?",
				array(
					$params->clientId, $params->isbn, $params->title, $params->description ?? null, $params->author, 
                    $params->rack_no ?? null, $params->row_no ?? null, $params->class_id ?? null, $params->category_id ?? null,  
                    $params->department_id ?? null, $params->quantity, $code, $params->userId, $item_id
				)
			)
		);

		/** Record the user activity **/
		$this->userLogs("library_book", $item_id, null, "{$params->userData->name} added the Book: {$params->title}", $params->userId);

        $return = ["code" => 200, "data" => "Library Book successfully added.", "refresh" => 2000];
		$return["additional"] = ["href" => "{$this->baseUrl}update-book/{$item_id}/view", "clear" => true];

        return $return;
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