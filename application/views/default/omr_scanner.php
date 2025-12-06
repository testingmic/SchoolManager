<?php
/**
 * OMR Scanner View
 * Interface for the Advanced OMR Answer Sheet Scanner
 */

// Ensure variables are available
$exams = $exams ?? [];
$classes = $classes ?? [];
$page_title = $page_title ?? 'OMR Scanner';
?>

<!-- Include the main header -->
<?php include_once "header.php"; ?>

<style>
.omr-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin: 20px 0;
}

.upload-section {
    border: 2px dashed #ddd;
    padding: 30px;
    text-align: center;
    border-radius: 10px;
    margin: 20px 0;
    transition: all 0.3s ease;
}

.upload-section:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-section.dragover {
    border-color: #28a745;
    background-color: #d4edda;
}

.processing-status {
    margin: 20px 0;
    padding: 15px;
    border-radius: 5px;
    display: none;
}

.status-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.status-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.status-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

.results-section {
    display: none;
    margin-top: 30px;
}

.canvas-container {
    text-align: center;
    margin: 20px 0;
}

canvas {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 10px;
    max-width: 100%;
    max-height: 400px;
}

.student-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.answers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
    margin: 20px 0;
    max-height: 400px;
    overflow-y: auto;
}

.answer-block {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
    font-size: 13px;
}

.answer-correct { 
    border-left-color: #28a745; 
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
}

.answer-incorrect { 
    border-left-color: #dc3545; 
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
}

.answer-unanswered { 
    border-left-color: #ffc107; 
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); 
}

.score-summary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: center;
}

.score-summary h3 {
    color: white;
    margin-top: 0;
}

.control-panel {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.btn-omr {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin: 5px;
    transition: all 0.3s ease;
}

.btn-omr:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
}

.progress-bar {
    width: 100%;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 20px;
    background: linear-gradient(90deg, #56ab2f 0%, #a8e6cf 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    border-left: 4px solid #007bff;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #007bff;
}

.debug-info {
    background: #343a40;
    color: #ffffff;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    max-height: 200px;
    overflow-y: auto;
}
</style>

<!-- Page Header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-scan fa-lg"></i> <?php echo $page_title; ?></h4>
                <div class="card-header-action">
                    <button onclick="showAnswerKeyManager()" class="btn btn-primary btn-sm">
                        <i class="fas fa-key"></i> Manage Answer Keys
                    </button>
                    <button onclick="showReports()" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main OMR Interface -->
<div class="row">
    <div class="col-md-12">
        <div class="card omr-container">
            <div class="card-body">
                <!-- Control Panel -->
                <div class="control-panel">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Select Exam:</label>
                            <select id="examSelect" class="form-control">
                                <option value="">Choose Exam...</option>
                                <?php foreach($exams as $exam): ?>
                                    <option value="<?php echo $exam['exam_id']; ?>">
                                        <?php echo htmlspecialchars($exam['exam_name']); ?> - <?php echo htmlspecialchars($exam['subject']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Select Class:</label>
                            <select id="classSelect" class="form-control">
                                <option value="">Choose Class...</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?php echo $class['class_id']; ?>">
                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Processing Mode:</label>
                            <select id="processingMode" class="form-control">
                                <option value="auto">Automatic Processing</option>
                                <option value="manual">Manual Review</option>
                                <option value="batch">Batch Processing</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Upload Section -->
                <div class="upload-section" id="uploadSection">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #6c757d;"></i>
                    <h4>📄 Upload OMR Answer Sheet</h4>
                    <p>Drag and drop your OMR answer sheet image here, or click to browse</p>
                    <input type="file" id="fileInput" accept="image/*" style="display: none;">
                    <button onclick="document.getElementById('fileInput').click()" class="btn-omr">
                        <i class="fas fa-folder-open"></i> Choose Image
                    </button>
                    <button onclick="processUploadedImage()" class="btn-omr btn-success" id="processBtn" disabled>
                        <i class="fas fa-cog fa-spin" style="display: none;" id="processSpinner"></i>
                        <i class="fas fa-search" id="processIcon"></i>
                        Scan Answer Sheet
                    </button>
                </div>

                <!-- Processing Status -->
                <div id="processingStatus" class="processing-status">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status" style="display: none;" id="statusSpinner"></div>
                        <span id="statusText">Ready to scan...</span>
                    </div>
                    <div class="progress-bar" id="progressContainer" style="display: none;">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="resultsSection" class="results-section">
                    <div class="row">
                        <!-- Image Display -->
                        <div class="col-md-6">
                            <div class="canvas-container">
                                <h5>📸 Original Image</h5>
                                <canvas id="originalCanvas"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="canvas-container">
                                <h5>🔍 Processed Image</h5>
                                <canvas id="processedCanvas"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="student-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-user"></i> Student Information</h5>
                                <p><strong>Name:</strong> <span id="studentName">Detecting...</span></p>
                                <p><strong>Subject:</strong> <span id="subject">Detecting...</span></p>
                                <p><strong>Scan Time:</strong> <span id="scanTime"><?php echo date('Y-m-d H:i:s'); ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-cog"></i> Processing Info</h5>
                                <p><strong>Confidence:</strong> <span id="confidenceScore">-</span>%</p>
                                <p><strong>Processing Time:</strong> <span id="processingTime">-</span>s</p>
                                <p><strong>Status:</strong> <span id="processingStatus">Processing...</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Score Summary -->
                    <div class="score-summary">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h3 id="totalScore">0/0</h3>
                                <p>Total Score</p>
                            </div>
                            <div class="col-md-3">
                                <h3 id="percentage">0%</h3>
                                <p>Percentage</p>
                            </div>
                            <div class="col-md-3">
                                <h3 id="questionsProcessed">0</h3>
                                <p>Questions Processed</p>
                            </div>
                            <div class="col-md-3">
                                <h3 id="unansweredQuestions">0</h3>
                                <p>Unanswered</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button onclick="saveResults()" class="btn-omr btn-success" id="saveBtn" disabled>
                                <i class="fas fa-save"></i> Save Results
                            </button>
                            <button onclick="retakePhoto()" class="btn-omr">
                                <i class="fas fa-camera"></i> Retake Photo
                            </button>
                            <button onclick="exportResults()" class="btn-omr">
                                <i class="fas fa-download"></i> Export Results
                            </button>
                        </div>
                    </div>

                    <!-- Detailed Answers -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list"></i> Detailed Answer Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div id="answersGrid" class="answers-grid">
                                <!-- Answers will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Information -->
                <div class="debug-info" id="debugInfo">
                    <h6><i class="fas fa-bug"></i> Debug Information</h6>
                    <div id="debugContent">System ready. Upload an OMR sheet to begin processing.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answer Key Manager Modal -->
<div class="modal fade" id="answerKeyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Answer Key Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="answerKeyForm">
                    <div class="mb-3">
                        <label>Select Exam:</label>
                        <select id="answerKeyExam" class="form-control" required>
                            <option value="">Choose Exam...</option>
                            <?php foreach($exams as $exam): ?>
                                <option value="<?php echo $exam['exam_id']; ?>">
                                    <?php echo htmlspecialchars($exam['exam_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Number of Questions:</label>
                        <input type="number" id="numQuestions" class="form-control" value="60" min="1" max="100">
                    </div>
                    <div id="answerKeyGrid" class="row">
                        <!-- Answer key inputs will be generated here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAnswerKey()">Save Answer Key</button>
            </div>
        </div>
    </div>
</div>

<!-- Load required scripts -->
<script src="https://docs.opencv.org/4.x/opencv.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

<script>
// Include the advanced OMR processing JavaScript
const BASE_URL = "<?php echo site_url('omr_scanner'); ?>";
let uploadedImage = null;
let answerKey = {};
let debugLog = [];
let currentResults = null;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeOMRScanner();
});

function initializeOMRScanner() {
    setupFileHandling();
    setupDragAndDrop();
    log("OMR Scanner initialized successfully");
}

function setupFileHandling() {
    const fileInput = document.getElementById('fileInput');
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const img = new Image();
        img.onload = function() {
            uploadedImage = img;
            showStatus('Image loaded successfully!', 'success');
            document.getElementById('processBtn').disabled = false;
            log(`Image loaded: ${file.name} (${img.width}x${img.height})`);
        };
        img.src = URL.createObjectURL(file);
    });
}

function setupDragAndDrop() {
    const uploadSection = document.getElementById('uploadSection');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadSection.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    uploadSection.addEventListener('dragenter', () => uploadSection.classList.add('dragover'));
    uploadSection.addEventListener('dragleave', () => uploadSection.classList.remove('dragover'));
    uploadSection.addEventListener('drop', handleDrop);

    function handleDrop(e) {
        uploadSection.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('fileInput').files = files;
            const event = new Event('change', { bubbles: true });
            document.getElementById('fileInput').dispatchEvent(event);
        }
    }
}

function processUploadedImage() {
    if (!uploadedImage) {
        showStatus('Please upload an image first!', 'error');
        return;
    }

    const examId = document.getElementById('examSelect').value;
    const classId = document.getElementById('classSelect').value;

    if (!examId || !classId) {
        showStatus('Please select both exam and class!', 'error');
        return;
    }

    showStatus('Processing OMR sheet...', 'info');
    document.getElementById('processBtn').disabled = true;
    document.getElementById('processSpinner').style.display = 'inline';
    document.getElementById('processIcon').style.display = 'none';
    
    // First get the answer key
    fetchAnswerKey(examId).then(() => {
        processOMRSheet(uploadedImage);
    }).catch(error => {
        showStatus('Error loading answer key: ' + error.message, 'error');
        resetProcessButton();
    });
}

async function fetchAnswerKey(examId) {
    try {
        const response = await fetch(`${BASE_URL}?action=process`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `exam_id=${examId}&class_id=${document.getElementById('classSelect').value}`
        });

        const result = await response.json();
        if (result.status === 'success') {
            answerKey = result.answer_key;
            log(`Answer key loaded: ${Object.keys(answerKey).length} questions`);
        } else {
            throw new Error(result.error || 'Failed to load answer key');
        }
    } catch (error) {
        log(`Error fetching answer key: ${error.message}`);
        throw error;
    }
}

// ... (Include the rest of the OMR processing JavaScript from the HTML file)
// This would be the same functions from the standalone HTML file but adapted for PHP integration

function showStatus(message, type = 'info') {
    const statusEl = document.getElementById('processingStatus');
    const statusText = document.getElementById('statusText');
    const spinner = document.getElementById('statusSpinner');
    
    statusEl.style.display = 'block';
    statusText.textContent = message;
    statusEl.className = `processing-status status-${type}`;
    
    if (type === 'info') {
        spinner.style.display = 'inline-block';
    } else {
        spinner.style.display = 'none';
    }
    
    if (type === 'success' || type === 'error') {
        setTimeout(() => {
            statusEl.style.display = 'none';
        }, 3000);
    }
}

function log(message) {
    console.log(message);
    debugLog.push(`${new Date().toLocaleTimeString()}: ${message}`);
    updateDebugInfo();
}

function updateDebugInfo() {
    document.getElementById('debugContent').innerHTML = debugLog.slice(-10).join('<br>');
}

function resetProcessButton() {
    document.getElementById('processBtn').disabled = false;
    document.getElementById('processSpinner').style.display = 'none';
    document.getElementById('processIcon').style.display = 'inline';
}

// Additional functions for PHP integration...
function saveResults() {
    if (!currentResults) {
        showStatus('No results to save!', 'error');
        return;
    }

    const data = {
        student_name: document.getElementById('studentName').textContent,
        exam_id: document.getElementById('examSelect').value,
        class_id: document.getElementById('classSelect').value,
        detected_answers: currentResults.detectedAnswers,
        score: currentResults.score,
        total_questions: currentResults.totalQuestions
    };

    fetch(`${BASE_URL}?action=save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showStatus('Results saved successfully!', 'success');
            document.getElementById('saveBtn').disabled = true;
        } else {
            showStatus('Error saving results: ' + result.error, 'error');
        }
    })
    .catch(error => {
        showStatus('Error saving results: ' + error.message, 'error');
    });
}

// Initialize OpenCV when ready
function onOpenCvReady() {
    log("OpenCV.js is ready!");
    showStatus('System ready - upload an OMR sheet to begin', 'success');
}

if (typeof cv !== 'undefined') {
    onOpenCvReady();
}
</script>

<?php include_once "footer.php"; ?>