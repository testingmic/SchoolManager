<?php 
$appUrl = "https://app.myschoolgh.com";
$registerUrl = "https://app.myschoolgh.com/register";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySchoolGH - Modern School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #f59e0b;
            --accent-color: #10b981;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow);
        }

        .nav-container {
            max-width: 80%;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .nav-center {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-center a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-center a:hover {
            color: var(--primary-color);
        }

        .nav-center a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-center a:hover::after {
            width: 100%;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-docs {
            background: transparent;
            color: var(--text-dark);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .btn-docs:hover {
            background: #f9fafb;
            border-color: var(--primary-color);
        }

        .btn-dashboard {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-dashboard:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Hero Section */
        .hero {
            min-height: 80vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-container {
            max-width: 80%;
            margin: 0 auto;
            padding: 0 0rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-content {
            color: white;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 0.5s;
            line-height: 1.2;
        }

        .hero h1 .highlight {
            color: var(--secondary-color);
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 0.8s;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 1.1s;
        }

        .hero-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 1.4s;
        }

        .hero-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-weight: 500;
        }

        .hero-feature i {
            color: var(--secondary-color);
            font-size: 1.2rem;
        }

        .affiliate-box {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 300px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 1.7s;
        }

        .affiliate-box h4 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .affiliate-box p {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .affiliate-box a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .hero-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards 1s;
        }

        .hero-card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
        }

        .hero-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .hero-card p {
            color: var(--text-light);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            display: block;
        }

        .hero-stat-label {
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .btn-primary {
            background: var(--white);
            color: var(--primary-color);
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid var(--white);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--white);
            color: var(--primary-color);
            transform: translateY(-3px);
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            background: var(--bg-light);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .section-header p {
            font-size: 1.25rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
        }

        .feature-card.animate {
            animation: fadeInUp 0.8s ease forwards;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .feature-card p {
            color: var(--text-light);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            padding: 6rem 0;
            background: var(--primary-color);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }

        .stat-item {
            opacity: 0;
            transform: translateY(30px);
        }

        .stat-item.animate {
            animation: fadeInUp 0.8s ease forwards;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Pricing Section */
        .pricing {
            padding: 6rem 0;
            background: var(--bg-light);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .pricing-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
        }

        .pricing-card.animate {
            animation: fadeInUp 0.8s ease forwards;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .pricing-card.featured {
            border: 2px solid var(--primary-color);
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'Most Popular';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pricing-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .pricing-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .pricing-description {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .pricing-price {
            text-align: center;
            margin-bottom: 2rem;
        }

        .pricing-amount {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 0.25rem;
        }

        .pricing-currency {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .pricing-period {
            font-size: 1rem;
            color: var(--text-light);
            margin-top: 0.5rem;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .pricing-features li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .pricing-features li:last-child {
            border-bottom: none;
        }

        .pricing-features i {
            color: var(--accent-color);
            font-size: 1.1rem;
        }

        .pricing-button {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            display: block;
        }

        .pricing-button.primary {
            background: var(--primary-color);
            color: white;
        }

        .pricing-button.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .pricing-button.secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .pricing-button.secondary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            color: white;
            text-align: center;
        }

        .cta-content h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-content p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .footer-section p,
        .footer-section a {
            color: #9ca3af;
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: var(--secondary-color);
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 1rem;
            text-align: center;
            color: #9ca3af;
        }

        /* Animations */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-center {
                display: none;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-features {
                grid-template-columns: 1fr;
            }

            .affiliate-box {
                position: relative;
                bottom: auto;
                left: auto;
                margin-top: 2rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-card.featured {
                transform: none;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .cta-content h2 {
                font-size: 2rem;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Loading animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="logo">
                <i class="fas fa-graduation-cap"></i>
                MySchoolGH
            </a>
            <ul class="nav-center">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-right">
                <!-- <a href="#docs" class="btn-docs">Documentation</a> -->
                <a href="<?= $appUrl ?>" class="btn-dashboard">Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        <div class="hero-container">
            <div class="hero-content">
                <h1>Simplify School <span class="highlight">Administration</span> with MySchoolGH</h1>
                <p>All-in-one school management solution tailored for Ghanaian schools. Handle student records, staff, finances, and more with our robust web and mobile app.</p>
                <div class="hero-buttons">
                    <a href="#pricing" class="btn-secondary">View Pricing Plans</a>
                </div>
                <div class="hero-features">
                    <div class="hero-feature">
                        <i class="fas fa-check"></i>
                        <span>Cloud-Based & Secure</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check"></i>
                        <span>Real-time Analytics</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
            <div class="hero-card">
                <div class="hero-card-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Complete School Management</h3>
                <div class="absolute -top-6 -left-6 w-12 h-12 bg-yellow-400 rounded-full opacity-80 animate-pulse"></div>
                <p>Everything required for efficient school management, from student data to financial operations.</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number">10K+</span>
                        <span class="hero-stat-label">Students</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">50+</span>
                        <span class="hero-stat-label">Schools</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">99%</span>
                        <span class="hero-stat-label">Uptime</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose MySchoolGH?</h2>
                <p>Our platform offers everything you need to manage your educational institution efficiently and effectively.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Student Management</h3>
                    <p>Comprehensive student records, attendance tracking, and academic performance monitoring in one centralized system.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Teacher Portal</h3>
                    <p>Empower teachers with tools for lesson planning, grade management, and parent communication.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3>Financial Management</h3>
                    <p>Streamlined fee collection, expense tracking, and financial reporting for better fiscal control.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Communication Hub</h3>
                    <p>Seamless communication between administrators, teachers, parents, and students through integrated messaging.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics & Reports</h3>
                    <p>Data-driven insights with comprehensive analytics and customizable reports for informed decision-making.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Access</h3>
                    <p>Access your school management system anywhere, anytime with our responsive mobile-friendly platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" id="stats">
        <div class="container">
            <div class="stats-grid">
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header">
                <h2>Choose Your Perfect Plan</h2>
                <p>Flexible pricing options designed to meet the needs of schools of all sizes.</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3 class="pricing-name">Starter</h3>
                        <p class="pricing-description">Perfect for small schools getting started</p>
                    </div>
                    <div class="pricing-price">
                        <div class="pricing-amount">
                            <span class="pricing-currency">₵</span>
                            <span>1,500</span>
                        </div>
                        <div class="pricing-period">flat fee / term</div>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Up to 150 students</li>
                        <li><i class="fas fa-check"></i> Student management</li>
                        <li><i class="fas fa-check"></i> Financial management</li>
                        <li><i class="fas fa-check"></i> Advanced analytics</li>
                        <li><i class="fas fa-check"></i> Student & Parent portal</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                    </ul>
                    <a href="<?= $registerUrl ?>?plan=basic" class="pricing-button secondary">Get Started</a>
                </div>

                <div class="pricing-card featured">
                    <div class="pricing-header">
                        <h3 class="pricing-name">Professional</h3>
                        <p class="pricing-description">Most popular choice for growing schools</p>
                    </div>
                    <div class="pricing-price">
                        <div class="pricing-amount">
                            <span class="pricing-currency">₵</span>
                            <span>3,000</span>
                        </div>
                        <div class="pricing-period">flat fee / term</div>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Up to 500 students</li>
                        <li><i class="fas fa-check"></i> Advanced student management</li>
                        <li><i class="fas fa-check"></i> Financial management</li>
                        <li><i class="fas fa-check"></i> Advanced analytics</li>
                        <li><i class="fas fa-check"></i> Student & Parent portal</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                    </ul>
                    <a href="<?= $registerUrl ?>?plan=professional" class="pricing-button primary">Get Started</a>
                </div>

                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3 class="pricing-name">Enterprise</h3>
                        <p class="pricing-description">For large institutions with complex needs</p>
                    </div>
                    <div class="pricing-price">
                        <div class="pricing-amount">
                            <span class="pricing-currency">₵</span>
                            <span>5,000</span>
                        </div>
                        <div class="pricing-period">flat fee / term</div>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Unlimited students</li>
                        <li><i class="fas fa-check"></i> Student & Financial management</li>
                        <li><i class="fas fa-check"></i> Advanced analytics</li>
                        <li><i class="fas fa-check"></i> Student & Parent portal</li>
                        <li><i class="fas fa-check"></i> Dedicated support</li>
                        <li><i class="fas fa-check"></i> Personalized Domain</li>
                    </ul>
                    <a href="<?= $registerUrl ?>?plan=enterprise" class="pricing-button secondary">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Transform Your School?</h2>
                <p>Join hundreds of educational institutions that have already revolutionized their management processes.</p>
                <a href="<?= $registerUrl ?>?plan=trial" class="btn-primary">Start Your Free Trial</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>MySchoolGH</h3>
                    <p>Empowering educational institutions with modern, efficient, and comprehensive school management solutions.</p>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p><i class="fas fa-envelope"></i> info@myschoolgh.com</p>
                    <p><i class="fas fa-phone"></i> +233 550 107 7770</p>
                    <p><i class="fas fa-map-marker-alt"></i> Accra, Ghana</p>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <p><a href="https://www.facebook.com/myschoolgh"><i class="fab fa-facebook"></i> Facebook</a></p>
                    <p><a href="https://www.twitter.com/myschoolgh"><i class="fab fa-twitter"></i> Twitter</a></p>
                    <p><a href="https://www.linkedin.com/company/myschoolgh"><i class="fab fa-linkedin"></i> LinkedIn</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 MySchoolGH. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Loading screen
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loading').classList.add('hidden');
            }, 1000);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            observer.observe(card);
        });

        // Observe pricing cards
        document.querySelectorAll('.pricing-card').forEach(card => {
            observer.observe(card);
        });

        // Observe stat items
        document.querySelectorAll('.stat-item').forEach(stat => {
            observer.observe(stat);
        });

        // Animated counter for stats
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 20);
        }

        // Trigger counter animation when stats section is visible
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    document.querySelectorAll('.stat-number').forEach(stat => {
                        const target = parseInt(stat.getAttribute('data-target'));
                        animateCounter(stat, target);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.getElementById('stats');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax effect for floating elements
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelectorAll('.floating-element');
            
            parallax.forEach((element, index) => {
                const speed = 0.5 + (index * 0.1);
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Add hover effects to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Typing effect for hero title
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';
            
            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Initialize typing effect when page loads
        window.addEventListener('load', function() {
            setTimeout(() => {
                const heroTitle = document.querySelector('.hero h1');
                if (heroTitle) {
                    const originalText = heroTitle.textContent;
                    typeWriter(heroTitle, originalText, 50);
                }
            }, 1500);
        });
    </script>
</body>
</html>
