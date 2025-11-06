
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview - Oasis School Complex</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .preview-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .preview-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            width: 100%;
        }
        
        .preview-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .preview-subtitle {
            font-size: 14px;
            color: #6b7280;
        }
        
        .certificate-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            overflow: visible;
            min-height: 900px; /* Increased minimum height for full certificate */
            padding: 20px 0; /* Added vertical padding */
        }
        
        .certificate-frame {
            transform-origin: top center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 4px;
            overflow: visible;
            width: fit-content;
            height: fit-content;
            margin-bottom: 60px; /* Extra margin to prevent cutoff */
        }
        
        /* Responsive scaling */
        @media (max-width: 1400px) {
            .certificate-frame {
                transform: scale(0.9);
                margin-bottom: 80px;
            }
        }
        
        @media (max-width: 1200px) {
            .certificate-frame {
                transform: scale(0.8);
                margin-bottom: 100px;
            }
        }
        
        @media (max-width: 1000px) {
            .certificate-frame {
                transform: scale(0.7);
                margin-bottom: 120px;
            }
        }
        
        @media (max-width: 800px) {
            .certificate-frame {
                transform: scale(0.6);
                margin-bottom: 140px;
            }
        }
        
        @media (max-width: 600px) {
            .certificate-frame {
                transform: scale(0.5);
                margin-bottom: 160px;
            }
            
            .preview-container {
                padding: 10px;
            }
        }
        
        /* Special handling for landscape certificates */
        .certificate-frame:has(.certificate[style*="aspect-ratio:297/210"]) {
            margin-bottom: 40px; /* Less margin needed for landscape */
        }
        
        @media (max-width: 1400px) {
            .certificate-frame:has(.certificate[style*="aspect-ratio:297/210"]) {
                margin-bottom: 60px;
            }
        }
        
        .preview-actions {
            margin-top: 20px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 0 10px;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background: #1d4ed8;
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        /* Override certificate styles for preview */
        .certificate-frame .certificate {
            margin: 0 !important;
            box-shadow: none !important;
        }
        
        /* Ensure full height is visible */
        .certificate-frame {
            min-height: fit-content;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1 class="preview-title">Certificate Preview</h1>
            <p class="preview-subtitle">Oasis School Complex - Live Configuration Preview</p>
        </div>
        
        <div class="certificate-wrapper">
            <div class="certificate-frame">
                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graduation Certificate - Leslie Ruecker</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        html, body { margin:0; padding:0; height:100%; }
        body { font-family: Times New Roman; }
        
        .certificate {
            background: #ffffff;
            margin: 0 auto;
            position: relative;
            padding: 20px 40px; /* inner padding */
            box-sizing: border-box;
            color: #1f2937;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        @media screen { 
            body { 
                background:#f3f4f6; 
                padding:20px; 
            } 
            .certificate { 
                max-width:793px; 
                aspect-ratio:210/297; 
                box-shadow:0 0 4px rgba(0,0,0,.15),0 4px 12px rgba(0,0,0,.08); 
                border-radius:4px;
                min-height: 1122px; /* A4 height in pixels at 96 DPI */
            } 
        }
        @media print { body { background:#fff; padding:0; } .certificate { box-shadow:none; border-radius:0; page-break-inside:avoid; } }
        
        .geometric-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                #2563eb10 0%, 
                transparent 25%, 
                transparent 75%, 
                #f59e0b10 100%);
            z-index: 1;
        }
        
        .geometric-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
        }
        
        .shape {
            position: absolute;
            border: 2px solid #f59e0b;
        }
        
        .shape.circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            top: 30px;
            right: 30px;
        }
        
        .shape.triangle {
            width: 0;
            height: 0;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            border-bottom: 43px solid #2563eb;
            bottom: 30px;
            left: 30px;
            border: none;
            opacity: 0.3;
        }
        
        .shape.square {
            width: 40px;
            height: 40px;
            bottom: 50px;
            right: 50px;
            transform: rotate(45deg);
        }
        
    .content-wrapper { position:relative; z-index:10; display:flex; flex-direction:column; min-height:100%; padding-bottom: 20px; }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .school-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin: 0 auto 20px;
            border: 3px solid #2563eb;
            border-radius: 50%;
            padding: 10px;
            background: white;
        }
        
        .main-title {
            font-size: 28px;
            font-weight: 300;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        
        .subtitle {
            font-size: 16px;
            color: #1e40af;
            font-weight: 400;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .school-name {
            font-size: 20px;
            color: #f59e0b;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .content {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.6;
        }
        
        .certification-text {
            font-size: 20px;
            margin-bottom: 2px;
            margin-top: 20px;
            font-weight: 300;
        }
        
        .student-name {
            font-size: 24px;
            font-weight: 700;
            color: #2563eb;
            margin: 30px 0;
            padding: 20px 40px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                #f59e0b20 20%, 
                #f59e0b20 80%, 
                transparent 100%);
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }
        
        .student-name::before,
        .student-name::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 60px;
            height: 2px;
            background: #2563eb;
        }
        
        .student-name::before {
            left: -80px;
        }
        
        .student-name::after {
            right: -80px;
        }
        
        .completion-text {
            font-size: 20px;
            margin: 25px 0;
            font-weight: 300;
        }
        
        .program-text {
            font-size: 20px;
            font-weight: 500;
            margin: 12px 0;
            color: #1e40af;
        }
        
        .details-section {
            margin: 30px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
        }
        
        .detail-item {
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        
        .detail-value {
            font-weight: 600;
            color: #1f2937;
            display: block;
            margin-top: 5px;
        }
        
        .footer-text {
            font-size: 20px;
            font-style: italic;
            color: #1e40af;
            margin: 2px 0;
            font-weight: 300;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 30px;
            min-height: 100px;
            align-items: flex-end;
        }
        
        .signature {
            text-align: center;
            width: 180px;
        }
        
        .signature-image {
            height: 50px;
            margin-bottom: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #1f2937;
            margin-bottom: 8px;
            width: 160px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .signature-title {
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .student-photo {
            position: absolute;
            object-fit: cover;
            border: 2px solid #2563eb;
            border-radius: 15px;
            z-index: 10;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        /* Size variants */
        .photo-small { width: 80px; height: 100px; }
        .photo-medium { width: 100px; height: 120px; }
        .photo-large { width: 120px; height: 150px; }
        /* Position variants */
        .photo-top-left { top: 50px; left: 80px; }
        .photo-top-right { top: 50px; right: 80px; }
        .photo-top-center { top: 50px; left: 50%; transform: translateX(-50%); }
        .photo-center-left { top: 28%; left: 80px; transform: translateY(-50%); }
        .photo-center-center { top: 28%; left: 50%; transform: translate(-50%, -50%); }
        .photo-center-right { top: 28%; right: 80px; transform: translateY(-50%); }
        .photo-bottom-left { bottom: 120px; left: 80px; }
        .photo-bottom-right { bottom: 120px; right: 80px; }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 3;
        }
        
        .watermark img {
            max-width: 350px;
            max-height: 350px;
        }b
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .certificate {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
                <!-- Geometric Background -->
        <div class="geometric-bg"></div>
        
        <!-- Geometric Shapes -->
        <div class="geometric-shapes">
            <div class="shape circle"></div>
            <div class="shape triangle"></div>
            <div class="shape square"></div>
        </div>
        
        <!-- Watermark -->
                
        <!-- Student Photo -->
                
        <!-- Content -->
        <div class="content-wrapper">
            <div class="header">
                <img src="" alt="School Logo" class="school-logo">            
                 <div class="school-name">
                    Oasis School Complex
                </div>
                
                <div class="main-title">
                    GRADUATION CERTIFICATE
                </div>
            </div>
            
            <div class="content">
                <div class="certification-text">
                    This certificate is awarded to
                </div>
                
                <div class="student-name">
                    Leslie Ruecker
                </div>
                
                <div class="completion-text">
                    
                </div>
                
                <div class="program-text">
                                            for Graduating from Basic 6 to Next Level,
                                    </div>

                                     <div class="footer-text">
                        after a successful examination
                    </div>
                                
                <div class="details-section">
                                            <div class="detail-item">
                            Certificate Number
                            <span class="detail-value">CERT-EV8327</span>
                        </div>
                                        
                    <div class="detail-item">
                        Academic Year
                        <span class="detail-value">2025/2026</span>
                    </div>
                    
                    <div class="detail-item">
                        Date of Graduation
                        <span class="detail-value">16th October, 2025</span>
                    </div>
                </div>
                
               
            </div>
            
            <div class="signatures">
                <div class="signature">
                                                                <div style="height: 50px;"></div>
                                        <div class="signature-line"></div>
                    <div class="signature-title">
                        Headmaster/Principal
                    </div>
                </div>
                
                <div class="signature">
                                            <div style="height: 50px;"></div>
                                        <div class="signature-line"></div>
                    <div class="signature-title">
                        Academic Director
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
            </div>
        </div>
        
      
    </div>
    
    <script>
        // Ensure the page is fully loaded before displaying
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
            
            // Auto-adjust wrapper height if content is overflowing
            adjustWrapperHeight();
        });
        
        function adjustWrapperHeight() {
            const certificateFrame = document.querySelector('.certificate-frame');
            const certificateWrapper = document.querySelector('.certificate-wrapper');
            
            if (certificateFrame && certificateWrapper) {
                const frameHeight = certificateFrame.getBoundingClientRect().height;
                const currentWrapperHeight = certificateWrapper.offsetHeight;
                
                // If frame is taller than wrapper, increase wrapper height
                if (frameHeight > currentWrapperHeight - 100) { // 100px buffer
                    certificateWrapper.style.minHeight = (frameHeight + 150) + 'px';
                }
            }
        }
        
        // Print styles
        window.addEventListener('beforeprint', function() {
            document.querySelector('.preview-header').style.display = 'none';
            document.querySelector('.preview-actions').style.display = 'none';
            document.querySelector('.preview-container').style.padding = '0';
            document.querySelector('.preview-container').style.box-shadow = 'none';
            document.querySelector('.certificate-frame').style.transform = 'none';
            document.querySelector('.certificate-frame').style.margin = '0';
            document.body.style.background = 'white';
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('.preview-header').style.display = 'block';
            document.querySelector('.preview-actions').style.display = 'block';
            document.querySelector('.preview-container').style.padding = '20px';
            document.querySelector('.preview-container').style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            document.body.style.background = '#f3f4f6';
            
            // Re-adjust after print
            setTimeout(adjustWrapperHeight, 100);
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            setTimeout(adjustWrapperHeight, 100);
        });
    </script>
</body>
</html>
