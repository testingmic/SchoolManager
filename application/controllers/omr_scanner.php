<?php
/**
 * OMR Scanner Controller
 * Advanced OMR (Optical Mark Recognition) system for processing answer sheets
 * 
 * @author MySchoolGH Team
 * @version 2.0
 */

// Ensure user is logged in
if (!loggedIn()) {
    require "login.php";
    exit(-1);
}

// Check if user has appropriate permissions
if (!in_array($clientdata->user_type, ["admin", "teacher", "support"])) {
    invalid_route();
    exit;
}

// Global variables
global $myschoolgh, $myClass, $clientdata;

class OMRScanner {
    private $myClass;
    private $clientdata;
    
    public function __construct($myClass, $clientdata) {
        $this->myClass = $myClass;
        $this->clientdata = $clientdata;
    }
    
    /**
     * Display the OMR scanner interface
     */
    public function index() {
        // Get list of available exams for answer key selection
        $exams = $this->getAvailableExams();
        $classes = $this->getAvailableClasses();
        
        return [
            'exams' => $exams,
            'classes' => $classes,
            'page_title' => 'OMR Answer Sheet Scanner'
        ];
    }
    
    /**
     * Process uploaded OMR image and extract results
     */
    public function processOMR() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
        
        try {
            // Validate input
            $examId = $this->sanitizeInput($_POST['exam_id'] ?? '');
            $classId = $this->sanitizeInput($_POST['class_id'] ?? '');
            
            if (empty($examId) || empty($classId)) {
                return $this->jsonResponse(['error' => 'Exam ID and Class ID are required'], 400);
            }
            
            // Handle file upload
            $uploadResult = $this->handleFileUpload();
            if ($uploadResult['status'] !== 'success') {
                return $this->jsonResponse(['error' => $uploadResult['message']], 400);
            }
            
            $imagePath = $uploadResult['file_path'];
            
            // Get answer key for the exam
            $answerKey = $this->getAnswerKey($examId);
            if (!$answerKey) {
                return $this->jsonResponse(['error' => 'Answer key not found for this exam'], 404);
            }
            
            // Process the OMR image (this would integrate with the frontend JS processing)
            $processingResult = [
                'status' => 'success',
                'image_path' => $imagePath,
                'answer_key' => $answerKey,
                'exam_id' => $examId,
                'class_id' => $classId,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            return $this->jsonResponse($processingResult);
            
        } catch (Exception $e) {
            error_log("OMR Processing Error: " . $e->getMessage());
            return $this->jsonResponse(['error' => 'Processing failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Save OMR scan results to database
     */
    public function saveResults() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['student_name', 'exam_id', 'class_id', 'detected_answers', 'score', 'total_questions'];
            foreach ($required as $field) {
                if (!isset($input[$field])) {
                    return $this->jsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }
            
            // Find or create student record
            $studentId = $this->findOrCreateStudent($input['student_name'], $input['class_id']);
            
            // Save OMR scan result
            $resultId = $this->saveOMRResult([
                'student_id' => $studentId,
                'exam_id' => $input['exam_id'],
                'class_id' => $input['class_id'],
                'detected_answers' => json_encode($input['detected_answers']),
                'score' => $input['score'],
                'total_questions' => $input['total_questions'],
                'percentage' => ($input['score'] / $input['total_questions']) * 100,
                'scan_timestamp' => date('Y-m-d H:i:s'),
                'processed_by' => $this->clientdata->user_id
            ]);
            
            // Save individual answer details
            $this->saveAnswerDetails($resultId, $input['detected_answers'], $input['exam_id']);
            
            return $this->jsonResponse([
                'status' => 'success',
                'result_id' => $resultId,
                'student_id' => $studentId,
                'message' => 'OMR results saved successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("OMR Save Error: " . $e->getMessage());
            return $this->jsonResponse(['error' => 'Failed to save results: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Create or update answer key for an exam
     */
    public function manageAnswerKey() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $examId = $this->sanitizeInput($input['exam_id'] ?? '');
            $answerKey = $input['answer_key'] ?? [];
            
            if (empty($examId) || empty($answerKey)) {
                return $this->jsonResponse(['error' => 'Exam ID and answer key are required'], 400);
            }
            
            // Save or update answer key
            $this->saveAnswerKey($examId, $answerKey);
            
            return $this->jsonResponse([
                'status' => 'success',
                'message' => 'Answer key saved successfully',
                'exam_id' => $examId
            ]);
            
        } catch (Exception $e) {
            error_log("Answer Key Error: " . $e->getMessage());
            return $this->jsonResponse(['error' => 'Failed to save answer key: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get OMR scan history and statistics
     */
    public function getReports() {
        try {
            $examId = $this->sanitizeInput($_GET['exam_id'] ?? '');
            $classId = $this->sanitizeInput($_GET['class_id'] ?? '');
            
            $whereClause = "1=1";
            $params = [];
            
            if (!empty($examId)) {
                $whereClause .= " AND exam_id = ?";
                $params[] = $examId;
            }
            
            if (!empty($classId)) {
                $whereClause .= " AND class_id = ?";
                $params[] = $classId;
            }
            
            $results = $this->myClass->fetchData("
                SELECT omr.*, u.name as student_name, c.name as class_name 
                FROM omr_results omr 
                LEFT JOIN users u ON omr.student_id = u.user_id 
                LEFT JOIN classes c ON omr.class_id = c.class_id 
                WHERE $whereClause 
                ORDER BY omr.scan_timestamp DESC
            ", $params);
            
            // Calculate statistics
            $stats = $this->calculateStats($results);
            
            return $this->jsonResponse([
                'status' => 'success',
                'results' => $results,
                'statistics' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Reports Error: " . $e->getMessage());
            return $this->jsonResponse(['error' => 'Failed to generate reports'], 500);
        }
    }
    
    // Private helper methods
    
    private function handleFileUpload() {
        if (!isset($_FILES['omr_image'])) {
            return ['status' => 'error', 'message' => 'No image uploaded'];
        }
        
        $file = $_FILES['omr_image'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => 'File upload error'];
        }
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['status' => 'error', 'message' => 'Invalid file type. Only JPEG and PNG allowed.'];
        }
        
        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'File too large. Maximum 10MB allowed.'];
        }
        
        // Create upload directory
        $uploadDir = 'assets/uploads/omr/' . date('Y/m/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid('omr_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['status' => 'success', 'file_path' => $filepath];
        } else {
            return ['status' => 'error', 'message' => 'Failed to save uploaded file'];
        }
    }
    
    private function getAnswerKey($examId) {
        $result = $this->myClass->fetchData("
            SELECT answer_key FROM omr_answer_keys 
            WHERE exam_id = ? AND status = 'active'
        ", [$examId]);
        
        return $result ? json_decode($result[0]['answer_key'], true) : null;
    }
    
    private function saveAnswerKey($examId, $answerKey) {
        // Deactivate existing keys
        $this->myClass->executeQuery("
            UPDATE omr_answer_keys SET status = 'inactive' 
            WHERE exam_id = ?
        ", [$examId]);
        
        // Insert new key
        return $this->myClass->executeQuery("
            INSERT INTO omr_answer_keys (exam_id, answer_key, created_by, created_at, status)
            VALUES (?, ?, ?, ?, 'active')
        ", [
            $examId,
            json_encode($answerKey),
            $this->clientdata->user_id,
            date('Y-m-d H:i:s')
        ]);
    }
    
    private function findOrCreateStudent($studentName, $classId) {
        // Try to find existing student
        $student = $this->myClass->fetchData("
            SELECT user_id FROM users 
            WHERE LOWER(name) = LOWER(?) AND user_type = 'student' 
            AND client_id = ?
        ", [$studentName, $this->clientdata->client_id]);
        
        if ($student) {
            return $student[0]['user_id'];
        }
        
        // Create new student record
        $userId = $this->myClass->nextSequence('users');
        $this->myClass->executeQuery("
            INSERT INTO users (user_id, name, user_type, class_id, client_id, date_created, created_by)
            VALUES (?, ?, 'student', ?, ?, ?, ?)
        ", [
            $userId,
            $studentName,
            $classId,
            $this->clientdata->client_id,
            date('Y-m-d H:i:s'),
            $this->clientdata->user_id
        ]);
        
        return $userId;
    }
    
    private function saveOMRResult($data) {
        $resultId = $this->myClass->nextSequence('omr_results');
        
        $this->myClass->executeQuery("
            INSERT INTO omr_results 
            (result_id, student_id, exam_id, class_id, detected_answers, score, total_questions, 
             percentage, scan_timestamp, processed_by, client_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $resultId,
            $data['student_id'],
            $data['exam_id'],
            $data['class_id'],
            $data['detected_answers'],
            $data['score'],
            $data['total_questions'],
            $data['percentage'],
            $data['scan_timestamp'],
            $data['processed_by'],
            $this->clientdata->client_id
        ]);
        
        return $resultId;
    }
    
    private function saveAnswerDetails($resultId, $detectedAnswers, $examId) {
        $answerKey = $this->getAnswerKey($examId);
        
        foreach ($detectedAnswers as $questionNum => $selectedAnswer) {
            $correctAnswer = $answerKey[$questionNum] ?? null;
            $isCorrect = ($selectedAnswer === $correctAnswer) ? 1 : 0;
            
            $this->myClass->executeQuery("
                INSERT INTO omr_answer_details 
                (result_id, question_number, selected_answer, correct_answer, is_correct)
                VALUES (?, ?, ?, ?, ?)
            ", [$resultId, $questionNum, $selectedAnswer, $correctAnswer, $isCorrect]);
        }
    }
    
    private function getAvailableExams() {
        return $this->myClass->fetchData("
            SELECT exam_id, exam_name, subject, date_created 
            FROM exams 
            WHERE client_id = ? AND status = 'active'
            ORDER BY date_created DESC
        ", [$this->clientdata->client_id]);
    }
    
    private function getAvailableClasses() {
        return $this->myClass->fetchData("
            SELECT class_id, name as class_name 
            FROM classes 
            WHERE client_id = ? AND status = 'active'
            ORDER BY name
        ", [$this->clientdata->client_id]);
    }
    
    private function calculateStats($results) {
        if (empty($results)) {
            return ['total_scans' => 0, 'average_score' => 0, 'highest_score' => 0, 'lowest_score' => 0];
        }
        
        $totalScans = count($results);
        $scores = array_column($results, 'percentage');
        
        return [
            'total_scans' => $totalScans,
            'average_score' => round(array_sum($scores) / $totalScans, 2),
            'highest_score' => max($scores),
            'lowest_score' => min($scores),
            'last_scan' => $results[0]['scan_timestamp'] ?? 'N/A'
        ];
    }
    
    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
    
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Initialize OMR Scanner
$omrScanner = new OMRScanner($myClass, $clientdata);

// Route requests
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'process':
        $omrScanner->processOMR();
        break;
        
    case 'save':
        $omrScanner->saveResults();
        break;
        
    case 'answer_key':
        $omrScanner->manageAnswerKey();
        break;
        
    case 'reports':
        $omrScanner->getReports();
        break;
        
    case 'index':
    default:
        $data = $omrScanner->index();
        // Include the view file
        extract($data);
        include_once "application/views/default/omr_scanner.php";
        break;
}
?>