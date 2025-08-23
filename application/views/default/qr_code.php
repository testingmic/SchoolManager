<?php 
global $defaultClientData, $defaultUser, $myClass;

// get the client id
$clientId = !empty($session->clientId) ? $session->clientId : (
    $_GET["client"] ?? null
);

// base url
$baseUrl = $myClass->baseUrl;

// get the client data
$defaultClientData = !empty($defaultClientData) ? $defaultClientData : $myClass->client_session_data($clientId, false);
$clientState = $defaultClientData->client_state ?? "Inactive";
print_r($defaultClientData);
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code User Verification - School Manager</title>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
        const baseUrl = '<?= $baseUrl ?>';
        const clientId = '<?= $clientId ?>';
        function returnToHome() {
            window.location.href = '<?= $baseUrl ?>dashboard';
        }
    </script>
    <style>
        #html5-qrcode-button-camera-start {
            border: solid 1px #2563eb;
            padding: 5px;
            margin-top: 5px;
            border-radius: 5px;
            color: #2563eb;
        }
        #html5-qrcode-button-camera-stop {
            border: solid 1px #ff0000;
            padding: 5px;
            margin-top: 5px;
            border-radius: 5px;
            color: #ff0000;
        }
        #html5-qrcode-button-camera-stop:hover {
            background-color: #ff0000;
            color: #fff;
        }
        #html5-qrcode-button-camera-start:hover {
            background-color: #2563eb;
            color: #fff;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <?= render_qr_code_header() ?>

    <?php if($clientState !== "Active") { ?>
        <?= render_qr_code_inactive($baseUrl) ?>
    <?php } else { ?>
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 pt-4">
            <div class="grid lg:grid-cols-2 gap-4">
                <!-- Scanner Section -->
                <div class="bg-white rounded-2xl shadow-xl p-4">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Camera Scanner</h2>
                        <p class="text-gray-600">Position the QR code within the frame</p>
                    </div>
                    
                    <!-- Scanner Container -->
                    <div id="reader" class="rounded-xl overflow-hidden border-2 border-gray-200"></div>
                    
                    <!-- Scanner Controls -->
                    <div class="mt-4 flex justify-center space-x-3">
                        <button id="startBtn" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <i class="fas fa-play"></i>
                            <span>Start Scanner</span>
                        </button>
                        <button id="stopBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors hidden">
                            <i class="fas fa-stop"></i>
                            <span>Stop Scanner</span>
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="space-y-4">
                    <!-- Loading State -->
                    <div id="loadingState" class="bg-white rounded-2xl shadow-xl p-6 hidden">
                        <div class="flex items-center justify-center space-x-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                            <span class="text-gray-700">Fetching user information...</span>
                        </div>
                    </div>

                    <!-- Scan Result -->
                    <div id="scanResult" style="margin-top: 0px;" class="bg-white rounded-2xl shadow-xl p-6 hidden">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">QR Code Detected</h3>
                            <p class="text-gray-600 text-sm">Processing user information...</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <code id="scannedId" class="text-sm text-gray-700 break-all"></code>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div id="userInfo" class="bg-white rounded-2xl shadow-xl p-6 hidden">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user text-primary-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900" id="userName"></h3>
                            <p class="text-gray-600" id="userType"></p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-id-card text-gray-500 w-5"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Student/Staff ID</div>
                                    <div class="font-medium" id="userId"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-graduation-cap text-gray-500 w-5"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Class/Department</div>
                                    <div class="font-medium" id="userClass"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-envelope text-gray-500 w-5"></i>
                                <div>
                                    <div class="text-sm text-gray-500">Email</div>
                                    <div class="font-medium" id="userEmail"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-3">
                            <button id="confirmBtn" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-check"></i>
                                <span>Confirm & Verify</span>
                            </button>
                            <button id="rejectBtn" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-times"></i>
                                <span>Reject</span>
                            </button>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div id="successMessage" class="bg-white rounded-2xl shadow-xl p-6 hidden">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Verification Successful!</h3>
                            <p class="text-gray-600 mb-4">User has been successfully verified and logged.</p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center space-x-2 text-green-700">
                                    <i class="fas fa-clock"></i>
                                    <span id="verificationTime"></span>
                                </div>
                            </div>
                            <button id="newScanBtn" class="mt-4 bg-primary-500 hover:bg-primary-600 text-white py-2 px-6 rounded-lg transition-colors">
                                Scan Another Code
                            </button>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="bg-white rounded-2xl shadow-xl p-6 hidden">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Verification Failed</h3>
                            <p class="text-gray-600 mb-4" id="errorText"></p>
                            <button id="retryBtn" class="bg-primary-500 hover:bg-primary-600 text-white py-2 px-6 rounded-lg transition-colors">
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
        <script>
            let html5QrcodeScanner = null;
            let isScanning = false;
            let recentScans = [];

            // Update current time
            function updateTime() {
                const now = new Date();
                document.getElementById('current-time').textContent = now.toLocaleTimeString();
            }
            setInterval(updateTime, 1000);
            updateTime();

            // Initialize scanner
            function initializeScanner() {
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
                };

                html5QrcodeScanner = new Html5QrcodeScanner("reader", config);
            }

            // Start scanner
            function startScanner() {
                if (!html5QrcodeScanner) {
                    initializeScanner();
                }
                
                html5QrcodeScanner.render(onScanSuccess, onScanError);
                isScanning = true;
                
                document.getElementById('startBtn').classList.add('hidden');
                document.getElementById('stopBtn').classList.remove('hidden');
            }

            // Stop scanner
            function stopScanner() {
                if (html5QrcodeScanner && isScanning) {
                    html5QrcodeScanner.clear();
                    isScanning = false;
                }
                
                document.getElementById('startBtn').classList.remove('hidden');
                document.getElementById('stopBtn').classList.add('hidden');
            }

            // Handle successful scan
            function onScanSuccess(decodedText) {
                stopScanner();
                
                // Show scan result
                document.getElementById('scanResult').classList.remove('hidden');
                document.getElementById('scannedId').textContent = decodedText;
                
                // Show loading state
                document.getElementById('loadingState').classList.remove('hidden');
                
                // Fetch user data
                fetchUserData(decodedText);
            }

            // Handle scan error
            function onScanError(error) {}

            // Fetch user data from API
            async function fetchUserData(userId) {
                try {
                    // Simulate API call - replace with actual endpoint
                    $.get(`${baseUrl}api/users/lookup?unique_or_item_id=${encodeURIComponent(userId)}&client_id=${clientId}`, function(data) {
                        console.log(data);
                        if (data.success && data.data && data.data.length > 0) {
                            const user = data.data[0];
                            displayUserInfo(user);
                        } else {
                            showError('User not found in the system.');
                        }
                    });
                    
                    document.getElementById('loadingState').classList.add('hidden');
                    
                } catch (error) {
                    console.error('Error fetching user data:', error);
                    document.getElementById('loadingState').classList.add('hidden');
                    showError('Failed to fetch user information. Please try again.');
                }
            }

            // Display user information
            function displayUserInfo(user) {
                document.getElementById('userInfo').classList.remove('hidden');
                document.getElementById('userName').textContent = user.name || 'N/A';
                document.getElementById('userType').textContent = user.user_type_description || user.user_type || 'N/A';
                document.getElementById('userId').textContent = user.unique_id || user.user_id || 'N/A';
                document.getElementById('userClass').textContent = user.class_name || user.department_name || 'N/A';
                document.getElementById('userEmail').textContent = user.email || 'N/A';
            }

            // Show error message
            function showError(message) {
                document.getElementById('errorMessage').classList.remove('hidden');
                document.getElementById('errorText').textContent = message;
            }

            // Show success message
            function showSuccess() {
                document.getElementById('userInfo').classList.add('hidden');
                document.getElementById('successMessage').classList.remove('hidden');
                document.getElementById('verificationTime').textContent = new Date().toLocaleString();
            }

            // Reset scanner for new scan
            function resetForNewScan() {
                document.getElementById('scanResult').classList.add('hidden');
                document.getElementById('userInfo').classList.add('hidden');
                document.getElementById('successMessage').classList.add('hidden');
                document.getElementById('errorMessage').classList.add('hidden');
                document.getElementById('loadingState').classList.add('hidden');
                
                startScanner();
            }

            // Event listeners
            document.getElementById('startBtn').addEventListener('click', startScanner);
            document.getElementById('stopBtn').addEventListener('click', stopScanner);
            
            document.getElementById('confirmBtn').addEventListener('click', async () => {
                try {
                    // Simulate confirmation API call
                    const userId = document.getElementById('scannedId').textContent;
                    const response = await $.post(`${baseUrl}api/users/verify`, {
                        user_id: userId,
                        action: 'verify',
                        client_id: clientId
                    });
                    
                    if (response.success) {
                        showSuccess();
                    } else {
                        showError('Verification failed. Please try again.');
                    }
                } catch (error) {
                    console.error('Verification error:', error);
                    showError('Verification failed. Please try again.');
                }
            });
            
            document.getElementById('rejectBtn').addEventListener('click', () => {
                showError('User verification was rejected.');
            });
            
            document.getElementById('newScanBtn').addEventListener('click', resetForNewScan);
            document.getElementById('retryBtn').addEventListener('click', resetForNewScan);

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', () => {
                // Auto-start scanner after a short delay
                setTimeout(startScanner, 1000);
            });
        </script>
    <?php } ?>
</body>
</html>
