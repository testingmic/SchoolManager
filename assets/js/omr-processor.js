/**
 * Advanced OMR Processing System
 * Handles real-world OMR sheet processing with OpenCV.js
 * 
 * Features:
 * - Perspective correction for angled photos
 * - Dynamic bubble detection
 * - OCR for name extraction
 * - Advanced image preprocessing
 * - Template matching and alignment
 * 
 * @author MySchoolGH Development Team
 * @version 2.0
 */

class OMRProcessor {
    constructor() {
        this.debugMode = true;
        this.confidenceThreshold = 0.3;
        this.minBubbleFillRatio = 0.25;
        this.maxBubbleFillRatio = 0.9;
        this.processingStartTime = null;
        this.template = null;
        this.calibrationPoints = null;
    }

    /**
     * Main processing function for OMR sheets
     */
    async processOMRSheet(img) {
        this.processingStartTime = Date.now();
        this.log("Starting advanced OMR processing...");
        
        try {
            // Step 1: Load and validate image
            const src = cv.imread(img);
            this.log(`Image loaded: ${src.cols}x${src.rows} pixels`);
            
            if (src.empty()) {
                throw new Error("Failed to load image");
            }

            // Step 2: Preprocess image (perspective correction, noise reduction)
            const preprocessed = await this.preprocessImage(src);
            this.updateProgress(20);

            // Step 3: Detect and align OMR template
            const aligned = await this.alignOMRSheet(preprocessed);
            this.updateProgress(40);

            // Step 4: Extract student information using OCR
            const studentInfo = await this.extractStudentInfo(aligned);
            this.updateProgress(60);

            // Step 5: Detect and analyze bubbles
            const detectedAnswers = await this.detectBubbles(aligned);
            this.updateProgress(80);

            // Step 6: Validate and score results
            const results = this.scoreResults(detectedAnswers, studentInfo);
            this.updateProgress(100);

            // Step 7: Display results
            this.displayResults(src, aligned, results);

            // Cleanup
            src.delete();
            preprocessed.delete();
            aligned.delete();

            const processingTime = (Date.now() - this.processingStartTime) / 1000;
            this.log(`Processing completed in ${processingTime.toFixed(2)} seconds`);

            return results;

        } catch (error) {
            this.log(`Error processing OMR: ${error.message}`, 'error');
            throw error;
        }
    }

    /**
     * Advanced image preprocessing with perspective correction
     */
    async preprocessImage(src) {
        this.log("Starting image preprocessing...");
        
        // Convert to grayscale
        const gray = new cv.Mat();
        if (src.channels() === 4) {
            cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY);
        } else if (src.channels() === 3) {
            cv.cvtColor(src, gray, cv.COLOR_RGB2GRAY);
        } else {
            src.copyTo(gray);
        }

        // Apply Gaussian blur to reduce noise
        const blurred = new cv.Mat();
        const ksize = new cv.Size(5, 5);
        cv.GaussianBlur(gray, blurred, ksize, 1.5);

        // Enhance contrast using CLAHE (Contrast Limited Adaptive Histogram Equalization)
        const clahe = new cv.CLAHE(2.0, new cv.Size(8, 8));
        const enhanced = new cv.Mat();
        clahe.apply(blurred, enhanced);

        // Detect and correct perspective distortion
        const corrected = await this.correctPerspective(enhanced);

        // Apply adaptive threshold
        const thresh = new cv.Mat();
        cv.adaptiveThreshold(corrected, thresh, 255, cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY, 11, 2);

        // Morphological operations to clean up
        const kernel = cv.getStructuringElement(cv.MORPH_RECT, new cv.Size(2, 2));
        const cleaned = new cv.Mat();
        cv.morphologyEx(thresh, cleaned, cv.MORPH_CLOSE, kernel);

        // Cleanup intermediate matrices
        gray.delete();
        blurred.delete();
        enhanced.delete();
        corrected.delete();
        thresh.delete();
        kernel.delete();
        clahe.delete();

        this.log("Image preprocessing completed");
        return cleaned;
    }

    /**
     * Detect and correct perspective distortion
     */
    async correctPerspective(src) {
        this.log("Detecting perspective distortion...");

        try {
            // Find contours to detect the OMR sheet boundaries
            const contours = new cv.MatVector();
            const hierarchy = new cv.Mat();
            cv.findContours(src, contours, hierarchy, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);

            let largestContour = null;
            let maxArea = 0;

            // Find the largest rectangular contour (likely the OMR sheet)
            for (let i = 0; i < contours.size(); i++) {
                const contour = contours.get(i);
                const area = cv.contourArea(contour);
                
                if (area > maxArea && area > src.rows * src.cols * 0.1) {
                    const peri = cv.arcLength(contour, true);
                    const approx = new cv.Mat();
                    cv.approxPolyDP(contour, approx, 0.02 * peri, true);
                    
                    if (approx.rows >= 4) { // Should be roughly rectangular
                        if (largestContour) largestContour.delete();
                        largestContour = approx;
                        maxArea = area;
                    } else {
                        approx.delete();
                    }
                }
                contour.delete();
            }

            contours.delete();
            hierarchy.delete();

            if (largestContour && maxArea > 0) {
                // Extract corner points
                const corners = this.orderPoints(largestContour);
                largestContour.delete();

                // Calculate destination rectangle
                const destWidth = Math.max(
                    this.distance(corners[0], corners[1]),
                    this.distance(corners[2], corners[3])
                );
                const destHeight = Math.max(
                    this.distance(corners[1], corners[2]),
                    this.distance(corners[3], corners[0])
                );

                // Define source and destination points
                const srcPoints = cv.matFromArray(4, 1, cv.CV_32FC2, [
                    corners[0].x, corners[0].y,
                    corners[1].x, corners[1].y,
                    corners[2].x, corners[2].y,
                    corners[3].x, corners[3].y
                ]);

                const dstPoints = cv.matFromArray(4, 1, cv.CV_32FC2, [
                    0, 0,
                    destWidth, 0,
                    destWidth, destHeight,
                    0, destHeight
                ]);

                // Apply perspective transform
                const M = cv.getPerspectiveTransform(srcPoints, dstPoints);
                const warped = new cv.Mat();
                cv.warpPerspective(src, warped, M, new cv.Size(destWidth, destHeight));

                // Cleanup
                srcPoints.delete();
                dstPoints.delete();
                M.delete();

                this.log(`Perspective corrected: ${destWidth}x${destHeight}`);
                return warped;
            } else {
                this.log("No perspective correction needed - using original image");
                const result = new cv.Mat();
                src.copyTo(result);
                return result;
            }

        } catch (error) {
            this.log(`Perspective correction failed: ${error.message}`, 'warning');
            // Return a copy of the original if correction fails
            const result = new cv.Mat();
            src.copyTo(result);
            return result;
        }
    }

    /**
     * Align OMR sheet using template matching and calibration marks
     */
    async alignOMRSheet(src) {
        this.log("Aligning OMR sheet...");

        try {
            // Look for alignment marks or calibration points
            const aligned = await this.findAndAlignCalibrationMarks(src);
            return aligned;
        } catch (error) {
            this.log(`Alignment failed, using original: ${error.message}`, 'warning');
            const result = new cv.Mat();
            src.copyTo(result);
            return result;
        }
    }

    /**
     * Find calibration marks for precise alignment
     */
    async findAndAlibrationMarks(src) {
        // Create template for calibration marks (small filled circles)
        const template = cv.Mat.zeros(20, 20, cv.CV_8UC1);
        cv.circle(template, new cv.Point(10, 10), 8, new cv.Scalar(255), -1);

        const result = new cv.Mat();
        cv.matchTemplate(src, template, result, cv.TM_CCOEFF_NORMED);

        const minMaxLoc = cv.minMaxLoc(result);
        template.delete();
        result.delete();

        if (minMaxLoc.maxVal > 0.7) {
            this.log(`Calibration mark found with confidence: ${minMaxLoc.maxVal.toFixed(3)}`);
            this.calibrationPoints = minMaxLoc.maxLoc;
        }

        // For now, return the source image
        // In a full implementation, you would use calibration points to fine-tune alignment
        const aligned = new cv.Mat();
        src.copyTo(aligned);
        return aligned;
    }

    /**
     * Extract student information using OCR
     */
    async extractStudentInfo(src) {
        this.log("Extracting student information...");

        try {
            // Define name region (typically top 15% of the sheet)
            const nameRegionHeight = Math.floor(src.rows * 0.15);
            const nameRect = new cv.Rect(0, 0, src.cols, nameRegionHeight);
            const nameRegion = src.roi(nameRect);

            // Convert to canvas for OCR
            const canvas = document.createElement('canvas');
            cv.imshow(canvas, nameRegion);

            // Use Tesseract.js for OCR
            const ocrResult = await Tesseract.recognize(canvas, 'eng', {
                logger: m => {
                    if (m.status === 'recognizing text') {
                        this.log(`OCR Progress: ${Math.round(m.progress * 100)}%`);
                    }
                }
            });

            const text = ocrResult.data.text;
            const confidence = ocrResult.data.confidence;
            
            this.log(`OCR completed with confidence: ${confidence.toFixed(1)}%`);
            
            // Parse extracted text
            const studentInfo = this.parseStudentInfo(text);
            studentInfo.ocrConfidence = confidence;

            nameRegion.delete();
            return studentInfo;

        } catch (error) {
            this.log(`OCR extraction failed: ${error.message}`, 'error');
            return {
                name: 'Could not detect',
                subject: 'Could not detect',
                studentId: 'Could not detect',
                ocrConfidence: 0
            };
        }
    }

    /**
     * Parse OCR text to extract student information
     */
    parseStudentInfo(text) {
        const lines = text.split('\n').filter(line => line.trim().length > 0);
        let name = 'Could not detect';
        let subject = 'Could not detect';
        let studentId = 'Could not detect';

        // Common patterns for different fields
        const namePatterns = [/name[:\s]+([a-zA-Z\s]+)/i, /([A-Z][a-z]+\s+[A-Z][a-z]+)/];
        const subjectPatterns = [/subject[:\s]+([a-zA-Z\s]+)/i];
        const idPatterns = [/id[:\s]+([0-9A-Z]+)/i, /([0-9]{6,})/];

        // Try to extract name
        for (let line of lines) {
            for (let pattern of namePatterns) {
                const match = line.match(pattern);
                if (match && match[1] && match[1].length > 3) {
                    name = match[1].trim().toUpperCase();
                    break;
                }
            }
            if (name !== 'Could not detect') break;
        }

        // Try to extract subject
        const subjects = ['MATHEMATICS', 'MATH', 'SCIENCE', 'ENGLISH', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY', 'HISTORY'];
        for (let line of lines) {
            const upperLine = line.toUpperCase();
            for (let subj of subjects) {
                if (upperLine.includes(subj)) {
                    subject = subj;
                    break;
                }
            }
            if (subject !== 'Could not detect') break;
        }

        // Try to extract student ID
        for (let line of lines) {
            for (let pattern of idPatterns) {
                const match = line.match(pattern);
                if (match && match[1]) {
                    studentId = match[1].trim();
                    break;
                }
            }
            if (studentId !== 'Could not detect') break;
        }

        return { name, subject, studentId };
    }

    /**
     * Advanced bubble detection with dynamic coordinate mapping
     */
    async detectBubbles(processedImg) {
        this.log("Detecting bubbles with dynamic mapping...");

        const detectedAnswers = {};
        const imgHeight = processedImg.rows;
        const imgWidth = processedImg.cols;

        // Dynamic parameters based on image size
        const headerHeight = Math.floor(imgHeight * 0.15); // Skip header (15%)
        const footerHeight = Math.floor(imgHeight * 0.05); // Skip footer (5%)
        const workingHeight = imgHeight - headerHeight - footerHeight;
        
        const questionsPerColumn = 30;
        const numberOfColumns = 2;
        const questionHeight = workingHeight / questionsPerColumn;
        
        // Dynamic bubble sizing
        const bubbleWidth = Math.max(15, Math.floor(imgWidth * 0.02));
        const bubbleHeight = Math.max(15, Math.floor(questionHeight * 0.4));
        
        // Column positioning
        const columnWidth = imgWidth / numberOfColumns;
        const optionSpacing = columnWidth / 6; // Space for 4 options plus margins

        this.log(`Working area: ${imgWidth}x${workingHeight}, Bubble size: ${bubbleWidth}x${bubbleHeight}`);

        let totalDetected = 0;
        let totalProcessed = 0;

        // Process each column
        for (let col = 0; col < numberOfColumns; col++) {
            const colStartX = Math.floor(col * columnWidth + columnWidth * 0.1);
            
            // Process each question in the column
            for (let q = 0; q < questionsPerColumn; q++) {
                const questionNum = col * questionsPerColumn + q + 1;
                if (questionNum > 60) break; // Limit to 60 questions
                
                totalProcessed++;
                const questionY = headerHeight + q * questionHeight + questionHeight * 0.2;

                let bestAnswer = null;
                let maxFilled = 0;
                const bubbleData = {};

                // Check each option (A, B, C, D)
                const options = ['A', 'B', 'C', 'D'];
                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    const bubbleX = colStartX + i * optionSpacing + optionSpacing * 0.5;

                    try {
                        // Ensure bubble coordinates are within image bounds
                        const x = Math.max(0, Math.min(bubbleX, imgWidth - bubbleWidth));
                        const y = Math.max(0, Math.min(questionY, imgHeight - bubbleHeight));
                        const width = Math.min(bubbleWidth, imgWidth - x);
                        const height = Math.min(bubbleHeight, imgHeight - y);

                        if (width > 5 && height > 5) {
                            const bubbleRect = new cv.Rect(x, y, width, height);
                            const roi = processedImg.roi(bubbleRect);
                            
                            // Calculate fill ratio
                            const nonZeroPixels = cv.countNonZero(roi);
                            const totalPixels = roi.rows * roi.cols;
                            const fillRatio = nonZeroPixels / totalPixels;
                            
                            bubbleData[option] = {
                                fillRatio: fillRatio,
                                pixels: nonZeroPixels,
                                total: totalPixels,
                                coordinates: {x, y, width, height}
                            };

                            roi.delete();

                            // Check if this bubble is filled (with range validation)
                            if (fillRatio >= this.minBubbleFillRatio && 
                                fillRatio <= this.maxBubbleFillRatio && 
                                fillRatio > maxFilled) {
                                bestAnswer = option;
                                maxFilled = fillRatio;
                            }
                        }
                    } catch (error) {
                        this.log(`Error processing Q${questionNum}${option}: ${error.message}`, 'warning');
                    }
                }

                // Store result with confidence scoring
                if (bestAnswer) {
                    detectedAnswers[questionNum] = {
                        answer: bestAnswer,
                        confidence: maxFilled,
                        bubbleData: bubbleData
                    };
                    totalDetected++;
                } else {
                    // Check if any bubble has partial fill (possible unclear marking)
                    let partialFill = null;
                    let partialConfidence = 0;
                    
                    for (let option in bubbleData) {
                        const fillRatio = bubbleData[option].fillRatio;
                        if (fillRatio > 0.15 && fillRatio > partialConfidence) {
                            partialFill = option;
                            partialConfidence = fillRatio;
                        }
                    }

                    detectedAnswers[questionNum] = {
                        answer: partialFill || '-',
                        confidence: partialConfidence,
                        bubbleData: bubbleData,
                        uncertain: true
                    };
                }
            }
        }

        this.log(`Bubble detection completed: ${totalDetected}/${totalProcessed} clear answers detected`);
        return detectedAnswers;
    }

    /**
     * Score the detected answers against the answer key
     */
    scoreResults(detectedAnswers, studentInfo) {
        this.log("Scoring results...");

        let correctAnswers = 0;
        let totalQuestions = Object.keys(detectedAnswers).length;
        let uncertainAnswers = 0;
        const mistakes = [];
        const answerAnalysis = {};

        for (let questionNum in detectedAnswers) {
            const detected = detectedAnswers[questionNum];
            const studentAnswer = detected.answer;
            const correctAnswer = answerKey[questionNum];
            const confidence = detected.confidence || 0;
            const isUncertain = detected.uncertain || false;

            if (isUncertain) uncertainAnswers++;

            const isCorrect = studentAnswer === correctAnswer && studentAnswer !== '-';
            
            if (isCorrect) {
                correctAnswers++;
            } else if (studentAnswer !== '-') {
                mistakes.push({
                    question: parseInt(questionNum),
                    studentAnswer: studentAnswer,
                    correctAnswer: correctAnswer,
                    confidence: confidence
                });
            }

            answerAnalysis[questionNum] = {
                studentAnswer: studentAnswer,
                correctAnswer: correctAnswer,
                isCorrect: isCorrect,
                confidence: confidence,
                isUncertain: isUncertain,
                bubbleData: detected.bubbleData
            };
        }

        const percentage = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0;
        
        const results = {
            studentInfo: studentInfo,
            score: correctAnswers,
            totalQuestions: totalQuestions,
            percentage: percentage,
            uncertainAnswers: uncertainAnswers,
            mistakes: mistakes,
            answerAnalysis: answerAnalysis,
            detectedAnswers: detectedAnswers,
            processingTime: (Date.now() - this.processingStartTime) / 1000,
            averageConfidence: this.calculateAverageConfidence(detectedAnswers)
        };

        this.log(`Scoring completed: ${correctAnswers}/${totalQuestions} (${percentage}%)`);
        return results;
    }

    /**
     * Display results in the UI
     */
    displayResults(originalImg, processedImg, results) {
        this.log("Displaying results...");

        // Show processed images
        cv.imshow('originalCanvas', originalImg);
        cv.imshow('processedCanvas', processedImg);

        // Update student information
        document.getElementById('studentName').textContent = results.studentInfo.name;
        document.getElementById('subject').textContent = results.studentInfo.subject;
        document.getElementById('confidenceScore').textContent = results.studentInfo.ocrConfidence?.toFixed(1) || 'N/A';
        document.getElementById('processingTime').textContent = results.processingTime.toFixed(2);

        // Update score display
        document.getElementById('totalScore').textContent = `${results.score}/${results.totalQuestions}`;
        document.getElementById('percentage').textContent = `${results.percentage}%`;
        document.getElementById('questionsProcessed').textContent = results.totalQuestions;
        document.getElementById('unansweredQuestions').textContent = 
            results.totalQuestions - Object.values(results.detectedAnswers).filter(a => a.answer !== '-').length;

        // Display detailed answers
        this.displayDetailedAnswers(results.answerAnalysis);

        // Enable save button and show results
        document.getElementById('saveBtn').disabled = false;
        document.getElementById('resultsSection').style.display = 'block';
        
        // Store results globally for saving
        window.currentResults = results;
        
        this.log("Results displayed successfully");
    }

    /**
     * Display detailed answer analysis
     */
    displayDetailedAnswers(answerAnalysis) {
        const answersGrid = document.getElementById('answersGrid');
        answersGrid.innerHTML = '';

        for (let questionNum in answerAnalysis) {
            const analysis = answerAnalysis[questionNum];
            const questionDiv = document.createElement('div');
            
            let className = 'answer-block ';
            if (analysis.studentAnswer === '-') {
                className += 'answer-unanswered';
            } else if (analysis.isCorrect) {
                className += 'answer-correct';
            } else {
                className += 'answer-incorrect';
            }

            questionDiv.className = className;
            
            const confidenceColor = analysis.confidence > 0.7 ? '#28a745' : 
                                   analysis.confidence > 0.4 ? '#ffc107' : '#dc3545';

            questionDiv.innerHTML = `
                <strong>Q${questionNum}:</strong> ${analysis.studentAnswer || '-'}<br>
                <small>Correct: ${analysis.correctAnswer || 'N/A'}</small><br>
                <small>Confidence: <span style="color: ${confidenceColor}">
                    ${(analysis.confidence * 100).toFixed(1)}%
                </span></small>
                ${analysis.isUncertain ? '<br><small class="text-warning">⚠️ Uncertain</small>' : ''}
            `;
            
            answersGrid.appendChild(questionDiv);
        }
    }

    // Helper functions
    
    calculateAverageConfidence(detectedAnswers) {
        const confidences = Object.values(detectedAnswers)
            .map(a => a.confidence || 0)
            .filter(c => c > 0);
        
        return confidences.length > 0 ? 
            confidences.reduce((a, b) => a + b, 0) / confidences.length : 0;
    }

    orderPoints(contour) {
        // Order points as: top-left, top-right, bottom-right, bottom-left
        const points = [];
        for (let i = 0; i < contour.rows; i++) {
            const point = contour.data32S.slice(i * 2, i * 2 + 2);
            points.push({x: point[0], y: point[1]});
        }

        points.sort((a, b) => a.x + a.y - (b.x + b.y));
        const tl = points[0];
        const br = points[points.length - 1];
        
        points.sort((a, b) => a.x - a.y - (b.x - b.y));
        const tr = points[points.length - 1];
        const bl = points[0];

        return [tl, tr, br, bl];
    }

    distance(p1, p2) {
        return Math.sqrt(Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2));
    }

    updateProgress(percentage) {
        const progressFill = document.getElementById('progressFill');
        const progressContainer = document.getElementById('progressContainer');
        
        if (progressFill && progressContainer) {
            progressContainer.style.display = 'block';
            progressFill.style.width = percentage + '%';
            
            if (percentage >= 100) {
                setTimeout(() => {
                    progressContainer.style.display = 'none';
                }, 2000);
            }
        }
    }

    log(message, level = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logMessage = `${timestamp}: ${message}`;
        
        if (this.debugMode) {
            console.log(`[OMR-${level.toUpperCase()}] ${logMessage}`);
        }
        
        // Update debug display
        if (typeof window !== 'undefined' && window.debugLog) {
            window.debugLog.push(logMessage);
            if (window.debugLog.length > 20) {
                window.debugLog.shift(); // Keep only last 20 logs
            }
            
            const debugContent = document.getElementById('debugContent');
            if (debugContent) {
                debugContent.innerHTML = window.debugLog.slice(-10).join('<br>');
            }
        }
    }
}

// Initialize global OMR processor
window.omrProcessor = new OMRProcessor();
window.debugLog = window.debugLog || [];

// Global functions for integration
window.processOMRSheet = function(img) {
    return window.omrProcessor.processOMRSheet(img);
};

window.log = function(message, level) {
    window.omrProcessor.log(message, level);
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OMRProcessor;
}