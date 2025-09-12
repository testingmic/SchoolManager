<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom A5 OMR Answer Sheet Scanner</title>
    <script src="https://docs.opencv.org/4.x/opencv.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        h1 {
            text-align: center;
            color: #2d3748;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .upload-zone {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border: 3px dashed #cbd5e0;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .upload-zone:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
        }

        .file-input {
            margin: 20px 0;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .file-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .status-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid;
            display: none;
            backdrop-filter: blur(5px);
        }

        .status-info { 
            border-left-color: #3182ce; 
            background: linear-gradient(90deg, rgba(49, 130, 206, 0.1), transparent);
        }
        .status-success { 
            border-left-color: #38a169; 
            background: linear-gradient(90deg, rgba(56, 161, 105, 0.1), transparent);
        }
        .status-error { 
            border-left-color: #e53e3e; 
            background: linear-gradient(90deg, rgba(229, 62, 62, 0.1), transparent);
        }

        .results-section {
            display: none;
            margin-top: 30px;
        }

        .canvas-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .canvas-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .canvas-item h4 {
            margin-bottom: 15px;
            color: #2d3748;
            font-size: 1.1rem;
        }

        canvas {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .info-card {
            background: linear-gradient(135deg, #e6fffa, #f0fff4);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(56, 178, 172, 0.2);
        }

        .info-card h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .score-card {
            background: linear-gradient(135deg, #fed7d7, #fbb6ce);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .answers-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .answers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .answer-item {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #cbd5e0;
            transition: all 0.3s ease;
        }

        .answer-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .answer-correct {
            border-left-color: #38a169;
            background: linear-gradient(135deg, #f0fff4, #e6fffa);
        }

        .answer-incorrect {
            border-left-color: #e53e3e;
            background: linear-gradient(135deg, #fed7d7, #fbb6ce);
        }

        .answer-blank {
            border-left-color: #ed8936;
            background: linear-gradient(135deg, #fffaf0, #fef5e7);
        }

        .debug-section {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }

        .debug-section h4 {
            color: #90cdf4;
            margin-bottom: 15px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s ease;
            width: 0%;
        }

        @media (max-width: 768px) {
            .canvas-grid,
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>A5 OMR Scanner</h1>
        <p class="subtitle">Specialized for MySchoolGH Answer Sheets</p>
        
        <div class="upload-zone">
            <div class="upload-content">
                <h3>Upload Your A5 Answer Sheet</h3>
                <p>Select your OMR answer sheet image for processing</p>
                <input type="file" id="fileInput" accept="image/*" class="file-input">
                <button onclick="processSheet()" class="btn">Process Answer Sheet</button>
            </div>
        </div>

        <div id="statusCard" class="status-card">
            <div id="progressBar" class="progress-bar" style="display: none;">
                <div id="progressFill" class="progress-fill"></div>
            </div>
            <div id="statusText">Ready to process...</div>
        </div>

        <div id="resultsSection" class="results-section">
            <div class="canvas-grid">
                <div class="canvas-item">
                    <h4>Original Image</h4>
                    <canvas id="originalCanvas"></canvas>
                </div>
                <div class="canvas-item">
                    <h4>Processed Image</h4>
                    <canvas id="processedCanvas"></canvas>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>Student Information</h3>
                    <div class="info-item">
                        <span>Name:</span>
                        <strong id="studentName">Detecting...</strong>
                    </div>
                    <div class="info-item">
                        <span>Subject:</span>
                        <strong id="studentSubject">Detecting...</strong>
                    </div>
                </div>

                <div class="info-card score-card">
                    <h3>Results Summary</h3>
                    <div class="info-item">
                        <span>Score:</span>
                        <strong id="scoreDisplay">0/60</strong>
                    </div>
                    <div class="info-item">
                        <span>Percentage:</span>
                        <strong id="percentageDisplay">0%</strong>
                    </div>
                    <div class="info-item">
                        <span>Processed:</span>
                        <strong id="processedCount">0 questions</strong>
                    </div>
                </div>
            </div>

            <div class="answers-container">
                <h3>Answer Analysis</h3>
                <div id="answersGrid" class="answers-grid">
                    <!-- Answers will be populated here -->
                </div>
            </div>

            <div class="debug-section">
                <h4>Processing Log</h4>
                <div id="debugLog"></div>
            </div>
        </div>
    </div>

    <script>
        var uploadedImage = null;
        var answerKey = {};
        var debugMessages = [];
        
        function initializeAnswerKey() {
            var answers = ['A', 'B', 'C', 'D'];
            for (var i = 1; i <= 60; i++) {
                answerKey[i] = answers[Math.floor(Math.random() * answers.length)];
            }
            log("Answer key initialized for 60 questions");
        }

        function log(message) {
            console.log(message);
            debugMessages.push(new Date().toLocaleTimeString() + ": " + message);
            updateDebugDisplay();
        }

        function updateDebugDisplay() {
            var debugLog = document.getElementById('debugLog');
            debugLog.innerHTML = debugMessages.slice(-15).join('<br>');
            debugLog.scrollTop = debugLog.scrollHeight;
        }

        function showStatus(message, type, showProgress) {
            var statusCard = document.getElementById('statusCard');
            var statusText = document.getElementById('statusText');
            var progressBar = document.getElementById('progressBar');
            
            statusCard.style.display = 'block';
            statusCard.className = 'status-card status-' + (type || 'info');
            statusText.textContent = message;
            
            progressBar.style.display = showProgress ? 'block' : 'none';
            
            if (type === 'success' || type === 'error') {
                setTimeout(function() {
                    if (!showProgress) statusCard.style.display = 'none';
                }, 3000);
            }
        }

        function updateProgress(percentage) {
            var progressFill = document.getElementById('progressFill');
            progressFill.style.width = percentage + '%';
        }

        document.getElementById('fileInput').addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showStatus('Please select a valid image file!', 'error');
                return;
            }

            var img = new Image();
            img.onload = function() {
                uploadedImage = img;
                showStatus('Image loaded: ' + file.name + ' (' + img.width + 'x' + img.height + ')', 'success');
                log('Image loaded successfully: ' + file.name);
            };
            img.onerror = function() {
                showStatus('Failed to load image. Please try another file.', 'error');
            };
            img.src = URL.createObjectURL(file);
        });

        function processSheet() {
            if (!uploadedImage) {
                showStatus('Please upload an image first!', 'error');
                return;
            }

            showStatus('Processing A5 answer sheet...', 'info', true);
            updateProgress(0);
            
            initializeAnswerKey();
            
            setTimeout(function() {
                try {
                    processA5OMR(uploadedImage);
                } catch (error) {
                    log('Error: ' + error.message);
                    showStatus('Processing failed. Please try again.', 'error');
                }
            }, 100);
        }

        function processA5OMR(img) {
            log("Starting A5 OMR processing...");
            updateProgress(10);
            
            var src = cv.imread(img);
            log('Image dimensions: ' + src.cols + 'x' + src.rows);
            updateProgress(20);
            
            var processed = preprocessA5Image(src);
            updateProgress(40);
            
            extractA5StudentInfo(src);
            updateProgress(60);
            
            var answers = detectA5Answers(processed);
            updateProgress(80);
            
            displayA5Results(src, processed, answers);
            updateProgress(100);
            
            src.delete();
            processed.delete();
            
            showStatus('Processing completed successfully!', 'success');
        }

        function preprocessA5Image(src) {
            log("Preprocessing A5 image...");
            
            var gray = new cv.Mat();
            cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
            
            // Apply Gaussian blur to smooth the image
            var blurred = new cv.Mat();
            cv.GaussianBlur(gray, blurred, new cv.Size(3, 3), 0);
            
            // Use adaptive threshold for better handling of varying lighting
            var thresh = new cv.Mat();
            cv.adaptiveThreshold(blurred, thresh, 255, cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY_INV, 15, 3);
            
            // Apply minimal morphological operations to clean noise but preserve markings
            var kernel1 = cv.getStructuringElement(cv.MORPH_ELLIPSE, new cv.Size(2, 2));
            var opened = new cv.Mat();
            cv.morphologyEx(thresh, opened, cv.MORPH_OPEN, kernel1, new cv.Point(-1, -1), 1);
            
            // Close small gaps in markings
            var kernel2 = cv.getStructuringElement(cv.MORPH_ELLIPSE, new cv.Size(3, 3));
            var final = new cv.Mat();
            cv.morphologyEx(opened, final, cv.MORPH_CLOSE, kernel2, new cv.Point(-1, -1), 1);
            
            // Cleanup
            gray.delete();
            blurred.delete();
            thresh.delete();
            opened.delete();
            kernel1.delete();
            kernel2.delete();
            
            log("Enhanced preprocessing completed for marked bubbles");
            return final;
        }

        function extractA5StudentInfo(src) {
            log("Extracting student information...");
            
            var nameRegionHeight = Math.floor(src.rows * 0.08);
            
            var nameRegion = src.roi(new cv.Rect(
                Math.floor(src.cols * 0.02),
                Math.floor(src.rows * 0.12),
                Math.floor(src.cols * 0.45),
                nameRegionHeight
            ));
            
            var subjectRegion = src.roi(new cv.Rect(
                Math.floor(src.cols * 0.52),
                Math.floor(src.rows * 0.12),
                Math.floor(src.cols * 0.45),
                nameRegionHeight
            ));
            
            var processedName = preprocessTextRegion(nameRegion);
            var processedSubject = preprocessTextRegion(subjectRegion);
            
            var nameCanvas = document.createElement('canvas');
            var subjectCanvas = document.createElement('canvas');
            
            cv.imshow(nameCanvas, processedName);
            cv.imshow(subjectCanvas, processedSubject);
            
            Promise.all([
                Tesseract.recognize(nameCanvas, 'eng'),
                Tesseract.recognize(subjectCanvas, 'eng')
            ]).then(function(results) {
                parseA5StudentInfo(results[0].data.text, results[1].data.text);
                
                nameRegion.delete();
                subjectRegion.delete();
                processedName.delete();
                processedSubject.delete();
            }).catch(function(error) {
                log('OCR Error: ' + error.message);
                document.getElementById('studentName').textContent = 'Could not detect';
                document.getElementById('studentSubject').textContent = 'Could not detect';
                
                nameRegion.delete();
                subjectRegion.delete();
                processedName.delete();
                processedSubject.delete();
            });
        }

        function preprocessTextRegion(region) {
            var processed = region.clone();
            
            if (processed.channels() > 1) {
                cv.cvtColor(processed, processed, cv.COLOR_RGBA2GRAY);
            }
            
            cv.threshold(processed, processed, 0, 255, cv.THRESH_BINARY + cv.THRESH_OTSU);
            
            var kernel = cv.getStructuringElement(cv.MORPH_RECT, new cv.Size(2, 1));
            cv.dilate(processed, processed, kernel);
            kernel.delete();
            
            return processed;
        }

        function parseA5StudentInfo(nameText, subjectText) {
            var name = 'Could not detect';
            var subject = 'Could not detect';
            
            if (nameText && nameText.trim().length > 2) {
                name = nameText.replace(/[^a-zA-Z\s]/g, ' ').replace(/\s+/g, ' ').trim();
                if (name.length < 3) name = 'Could not detect';
            }
            
            if (subjectText && subjectText.trim().length > 1) {
                subject = subjectText.replace(/[^a-zA-Z\s]/g, ' ').replace(/\s+/g, ' ').trim().toUpperCase();
                if (subject.length < 2) subject = 'Could not detect';
            }
            
            document.getElementById('studentName').textContent = name;
            document.getElementById('studentSubject').textContent = subject;
            
            log('Student Info - Name: ' + name + ', Subject: ' + subject);
        }

        function detectA5Answers(processedImg) {
            log("Detecting answers on A5 sheet...");
            
            var answers = {};
            var imgHeight = processedImg.rows;
            var imgWidth = processedImg.cols;
            
            var questionsPerColumn = 20;
            var totalColumns = 3;
            
            var answerAreaTop = Math.floor(imgHeight * 0.28);
            var answerAreaBottom = Math.floor(imgHeight * 0.95);
            var answerAreaHeight = answerAreaBottom - answerAreaTop;
            var questionSpacing = answerAreaHeight / questionsPerColumn;
            
            var columnPositions = [
                Math.floor(imgWidth * 0.08),
                Math.floor(imgWidth * 0.40),
                Math.floor(imgWidth * 0.72)
            ];
            
            var bubbleWidth = Math.floor(imgWidth * 0.018);
            var bubbleHeight = Math.floor(questionSpacing * 0.5);
            var optionSpacing = Math.floor(imgWidth * 0.04);
            
            log('Detection params - Columns: ' + totalColumns + ', Questions/col: ' + questionsPerColumn);
            log('Answer area: ' + answerAreaTop + '-' + answerAreaBottom + ', Bubble: ' + bubbleWidth + 'x' + bubbleHeight);
            
            var allFillRatios = [];
            
            for (var col = 0; col < totalColumns; col++) {
                var colX = columnPositions[col];
                
                for (var q = 0; q < questionsPerColumn; q++) {
                    var questionNum = col * questionsPerColumn + q + 1;
                    var questionY = answerAreaTop + Math.floor(q * questionSpacing);
                    
                    for (var opt = 0; opt < 4; opt++) {
                        var bubbleX = colX + opt * optionSpacing;
                        
                        try {
                            var safeX = Math.max(0, Math.min(bubbleX, imgWidth - bubbleWidth));
                            var safeY = Math.max(0, Math.min(questionY, imgHeight - bubbleHeight));
                            var safeWidth = Math.min(bubbleWidth, imgWidth - safeX);
                            var safeHeight = Math.min(bubbleHeight, imgHeight - safeY);
                            
                            if (safeWidth > 0 && safeHeight > 0) {
                                var bubbleRect = new cv.Rect(safeX, safeY, safeWidth, safeHeight);
                                var roi = processedImg.roi(bubbleRect);
                                
                                var filledPixels = cv.countNonZero(roi);
                                var totalPixels = roi.rows * roi.cols;
                                var fillRatio = filledPixels / totalPixels;
                                
                                allFillRatios.push(fillRatio);
                                roi.delete();
                            }
                        } catch (error) {
                            // Skip problematic bubbles
                        }
                    }
                }
            }
            
            if (allFillRatios.length === 0) {
                log("No valid bubbles found for analysis");
                return {};
            }
            
            allFillRatios.sort(function(a, b) { return a - b; });
            var q75 = allFillRatios[Math.floor(allFillRatios.length * 0.75)] || 0;
            var mean = allFillRatios.reduce(function(sum, val) { return sum + val; }, 0) / allFillRatios.length;
            var variance = allFillRatios.reduce(function(sum, val) { return sum + Math.pow(val - mean, 2); }, 0) / allFillRatios.length;
            var stdDev = Math.sqrt(variance);
            
            var dynamicThreshold = Math.max(
                mean + 2 * stdDev,
                q75 * 1.5,
                0.35
            );
            
            log('Fill ratio analysis - Mean: ' + (mean*100).toFixed(1) + '%, Q75: ' + (q75*100).toFixed(1) + '%');
            log('Dynamic threshold set to: ' + (dynamicThreshold*100).toFixed(1) + '%');
            
            var totalProcessed = 0;
            var detectedAnswers = 0;
            
            for (var col = 0; col < totalColumns; col++) {
                var colX = columnPositions[col];
                
                for (var q = 0; q < questionsPerColumn; q++) {
                    var questionNum = col * questionsPerColumn + q + 1;
                    var questionY = answerAreaTop + Math.floor(q * questionSpacing);
                    
                    var bestOption = null;
                    var maxFillRatio = 0;
                    var fillRatios = {};
                    var validOptions = [];
                    
                    var options = ['A', 'B', 'C', 'D'];
                    for (var opt = 0; opt < 4; opt++) {
                        var option = options[opt];
                        var bubbleX = colX + opt * optionSpacing;
                        
                        try {
                            var safeX = Math.max(0, Math.min(bubbleX, imgWidth - bubbleWidth));
                            var safeY = Math.max(0, Math.min(questionY, imgHeight - bubbleHeight));
                            var safeWidth = Math.min(bubbleWidth, imgWidth - safeX);
                            var safeHeight = Math.min(bubbleHeight, imgHeight - safeY);
                            
                            if (safeWidth > 0 && safeHeight > 0) {
                                var bubbleRect = new cv.Rect(safeX, safeY, safeWidth, safeHeight);
                                var roi = processedImg.roi(bubbleRect);
                                
                                var filledPixels = cv.countNonZero(roi);
                                var totalPixels = roi.rows * roi.cols;
                                var fillRatio = filledPixels / totalPixels;
                                
                                fillRatios[option] = fillRatio;
                                
                                if (fillRatio >= dynamicThreshold) {
                                    validOptions.push({ option: option, fillRatio: fillRatio });
                                    if (fillRatio > maxFillRatio) {
                                        bestOption = option;
                                        maxFillRatio = fillRatio;
                                    }
                                }
                                
                                roi.delete();
                            }
                        } catch (error) {
                            log('Error processing Q' + questionNum + option + ': ' + error.message);
                        }
                    }
                    
                    if (validOptions.length > 1) {
                        validOptions.sort(function(a, b) { return b.fillRatio - a.fillRatio; });
                        var highest = validOptions[0].fillRatio;
                        var secondHighest = validOptions[1].fillRatio;
                        
                        if (highest - secondHighest < 0.15) {
                            bestOption = null;
                            log('Q' + questionNum + ': Multiple unclear markings detected');
                        }
                    }
                    
                    answers[questionNum] = bestOption;
                    totalProcessed++;
                    
                    if (bestOption) {
                        detectedAnswers++;
                    }
                    
                    if (questionNum <= 10 || bestOption) {
                        var ratioStr = '';
                        for (var key in fillRatios) {
                            ratioStr += key + ':' + Math.round(fillRatios[key] * 100) + '% ';
                        }
                        log('Q' + questionNum + ': ' + ratioStr + ' -> ' + (bestOption || 'BLANK'));
                    }
                }
            }
            
            log('Detection completed: ' + detectedAnswers + '/' + totalProcessed + ' questions marked');
            return answers;
        }

        function displayA5Results(originalImg, processedImg, detectedAnswers) {
            log("Displaying A5 results...");
            
            cv.imshow('originalCanvas', originalImg);
            cv.imshow('processedCanvas', processedImg);
            
            var correctCount = 0;
            var attemptedCount = 0;
            var incorrectCount = 0;
            var blankCount = 0;
            var totalQuestions = 60;
            
            var answersGrid = document.getElementById('answersGrid');
            answersGrid.innerHTML = '';
            
            for (var q = 1; q <= totalQuestions; q++) {
                var studentAnswer = detectedAnswers[q];
                var correctAnswer = answerKey[q];
                
                var isCorrect = false;
                var answerClass = 'answer-blank';
                var statusIcon = '—';
                
                if (studentAnswer) {
                    attemptedCount++;
                    if (studentAnswer === correctAnswer) {
                        correctCount++;
                        isCorrect = true;
                        answerClass = 'answer-correct';
                        statusIcon = '✓';
                    } else {
                        incorrectCount++;
                        answerClass = 'answer-incorrect';
                        statusIcon = '✗';
                    }
                } else {
                    blankCount++;
                    statusIcon = '—';
                }
                
                var answerDiv = document.createElement('div');
                answerDiv.className = 'answer-item ' + answerClass;
                answerDiv.innerHTML = '<div style="display: flex; justify-content: space-between; align-items: center;"><span style="font-weight: bold; font-size: 1.1em;">Q' + q + '</span><span style="font-size: 1.4em; font-weight: bold;">' + (studentAnswer || '—') + '</span></div><div style="margin-top: 8px; font-size: 0.85em; color: #666; display: flex; justify-content: space-between;"><span>Correct: ' + correctAnswer + '</span><span style="font-size: 1.2em;">' + statusIcon + '</span></div>';
                answersGrid.appendChild(answerDiv);
            }
            
            var percentage = totalQuestions > 0 ? Math.round((correctCount / totalQuestions) * 100) : 0;
            var attemptRate = Math.round((attemptedCount / totalQuestions) * 100);
            var accuracy = attemptedCount > 0 ? Math.round((correctCount / attemptedCount) * 100) : 0;
            
            document.getElementById('scoreDisplay').textContent = correctCount + '/' + totalQuestions;
            document.getElementById('percentageDisplay').textContent = percentage + '% (' + accuracy + '% accuracy)';
            document.getElementById('processedCount').textContent = attemptedCount + ' attempted, ' + blankCount + ' blank';
            
            var resultsSection = document.getElementById('resultsSection');
            resultsSection.style.display = 'block';
            resultsSection.style.opacity = '0';
            resultsSection.style.transform = 'translateY(20px)';
            
            setTimeout(function() {
                resultsSection.style.transition = 'all 0.5s ease';
                resultsSection.style.opacity = '1';
                resultsSection.style.transform = 'translateY(0)';
            }, 100);
            
            log('Final Results:');
            log('   Correct: ' + correctCount + '/' + totalQuestions + ' (' + percentage + '%)');
            log('   Attempted: ' + attemptedCount + ' (' + attemptRate + '%)');
            log('   Accuracy: ' + accuracy + '% of attempted questions');
            log('   Blank: ' + blankCount + ', Incorrect: ' + incorrectCount);
        }

        function onOpenCvReady() {
            log("OpenCV.js loaded successfully!");
            showStatus('System ready - Upload your A5 answer sheet to begin', 'success');
        }

        function waitForOpenCV() {
            if (typeof cv !== 'undefined' && cv.Mat) {
                onOpenCvReady();
            } else {
                setTimeout(waitForOpenCV, 100);
            }
        }

        if (typeof cv !== 'undefined' && cv.Mat) {
            onOpenCvReady();
        } else {
            log("Loading OpenCV.js...");
            showStatus('Loading computer vision library...', 'info');
            waitForOpenCV();
        }

        var uploadZone = document.querySelector('.upload-zone');
        
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadZone.style.borderColor = '#667eea';
            uploadZone.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
        });
        
        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadZone.style.borderColor = '#cbd5e0';
            uploadZone.style.backgroundColor = '';
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadZone.style.borderColor = '#cbd5e0';
            uploadZone.style.backgroundColor = '';
            
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('fileInput').files = files;
                document.getElementById('fileInput').dispatchEvent(new Event('change'));
            }
        });

        function loadCustomAnswerKey() {
            var customAnswers = {
                1: 'A', 2: 'B', 3: 'C', 4: 'D', 5: 'A', 6: 'B', 7: 'C', 8: 'D', 9: 'A', 10: 'B',
                11: 'C', 12: 'D', 13: 'A', 14: 'B', 15: 'C', 16: 'D', 17: 'A', 18: 'B', 19: 'C', 20: 'D',
                21: 'A', 22: 'B', 23: 'C', 24: 'D', 25: 'A', 26: 'B', 27: 'C', 28: 'D', 29: 'A', 30: 'B',
                31: 'C', 32: 'D', 33: 'A', 34: 'B', 35: 'C', 36: 'D', 37: 'A', 38: 'B', 39: 'C', 40: 'D',
                41: 'A', 42: 'B', 43: 'C', 44: 'D', 45: 'A', 46: 'B', 47: 'C', 48: 'D', 49: 'A', 50: 'B',
                51: 'C', 52: 'D', 53: 'A', 54: 'B', 55: 'C', 56: 'D', 57: 'A', 58: 'B', 59: 'C', 60: 'D'
            };
            
            answerKey = customAnswers;
            log("Custom answer key loaded");
        }

        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'u':
                        e.preventDefault();
                        document.getElementById('fileInput').click();
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (uploadedImage) processSheet();
                        break;
                }
            }
        });

        var helpButton = document.createElement('button');
        helpButton.className = 'btn';
        helpButton.innerHTML = 'Help';
        helpButton.style.position = 'fixed';
        helpButton.style.bottom = '20px';
        helpButton.style.right = '20px';
        helpButton.style.zIndex = '1000';
        
        helpButton.onclick = function() {
            alert('A5 OMR Scanner Help:\n\nInstructions:\n1. Upload a clear, well-lit photo of your A5 answer sheet\n2. Ensure the sheet is flat and all bubbles are visible\n3. The scanner will automatically detect student info and answers\n4. Review results and debug information\n\nShortcuts:\n• Ctrl+U: Upload file\n• Ctrl+Enter: Process sheet\n\nSheet Requirements:\n• A5 size format\n• 60 questions (3 columns × 20 rows)\n• Clear bubble markings\n• Good contrast and lighting\n\nTroubleshooting:\n• If detection fails, try better lighting\n• Ensure sheet is not wrinkled or damaged\n• Check that bubbles are properly filled\n• Review debug log for detailed information');
        };
        
        document.body.appendChild(helpButton);
    </script>
</body>
</html>