<?php
require_once 'includes/db.php';

// Get website content from database
$company_info = $pdo->query("SELECT * FROM company_info LIMIT 1")->fetch();
$hero_slides = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM company_stats WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

// Default values if database is empty
if (!$company_info) {
    $company_info = [
        'company_name' => 'LandPro Solutions',
        'tagline' => 'Your Trusted Land & C of O Processing Partner',
        'about_title' => 'About LandPro Solutions',
        'about_content' => 'We are Nigeria\'s leading land sales and Certificate of Occupancy processing company.',
        'phone' => '+234 901 234 5678',
        'email' => 'info@landprosolutions.com',
        'address' => '123 Real Estate Avenue, Victoria Island, Lagos, Nigeria',
        'working_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM',
        'facebook_url' => '',
        'twitter_url' => '',
        'instagram_url' => '',
        'linkedin_url' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($company_info['company_name']) ?> - <?= htmlspecialchars($company_info['tagline']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: #2c5530 !important;
        }
        
        .nav-link {
            font-weight: 500;
            color: #2c5530 !important;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #4a7c59 !important;
        }
        
        /* Hero Section */
        .hero-section {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .btn-hero {
            background: linear-gradient(45deg, #2c5530, #4a7c59);
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: white;
        }
        
        /* Carousel Custom Styles */
        .carousel-item {
            height: 100vh;
        }
        
        .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1;
        }
        
        /* Section Styles */
        .section-padding {
            padding: 80px 0;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c5530;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, #2c5530, #4a7c59);
            border-radius: 2px;
        }
        
        /* About Section */
        .about-section {
            background: #f8f9fa;
        }
        
        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }
        
        .feature-box {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .feature-box:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: #4a7c59;
            margin-bottom: 1rem;
        }
        
        /* Services Section */
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
        }
        
        .service-icon {
            font-size: 3.5rem;
            color: #4a7c59;
            margin-bottom: 1rem;
        }
        
        .service-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c5530;
            margin-bottom: 1rem;
        }
        
        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            color: white;
        }
        
        .contact-info {
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .contact-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #fff;
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Admin Portal Access */
        .admin-access {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .admin-btn {
            background: #2c5530;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.9rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .admin-btn:hover {
            background: #4a7c59;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
                padding: 0 1rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
                padding: 0 1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .section-padding {
                padding: 50px 0;
            }
            
            .feature-box {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .service-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .contact-info {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .carousel-item {
                height: 80vh;
            }
            
            .hero-content {
                padding: 0 1rem;
            }
            
            .btn-hero {
                padding: 12px 24px;
                font-size: 1rem;
            }
            
            .admin-access {
                bottom: 100px;
                right: 15px;
            }
            
            .admin-btn {
                padding: 10px 16px;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
            
            .section-padding {
                padding: 40px 0;
            }
            
            .feature-box {
                padding: 1rem;
            }
            
            .service-card {
                padding: 1rem;
            }
            
            .contact-info {
                padding: 1rem;
            }
            
            .feature-icon {
                font-size: 2.5rem;
            }
            
            .service-icon {
                font-size: 3rem;
            }
            
            .carousel-item {
                height: 70vh;
            }
            
            .contact-item {
                flex-direction: column;
                text-align: center;
                margin-bottom: 1rem;
            }
            
            .contact-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
            
            .navbar-toggler {
                font-size: 1rem;
            }
            
            .container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-home-lg-alt me-2"></i><?= htmlspecialchars($company_info['company_name']) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Slider -->
    <section id="home">
        <?php if (!empty($hero_slides)): ?>
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($hero_slides as $index => $slide): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" 
                        class="<?= $index === 0 ? 'active' : '' ?>"></button>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-inner">
                <?php foreach ($hero_slides as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" 
                     style="background: <?= htmlspecialchars($slide['background_gradient']) ?>;">
                    <div class="hero-content">
                        <h1 class="hero-title"><?= htmlspecialchars($slide['title']) ?></h1>
                        <p class="hero-subtitle"><?= htmlspecialchars($slide['subtitle']) ?></p>
                        <?php if ($slide['button_text'] && $slide['button_link']): ?>
                        <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="btn-hero">
                            <i class="fas fa-arrow-right me-2"></i><?= htmlspecialchars($slide['button_text']) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <?php else: ?>
        <!-- Default hero if no slides in database -->
        <div class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Welcome to <?= htmlspecialchars($company_info['company_name']) ?></h1>
                <p class="hero-subtitle"><?= htmlspecialchars($company_info['tagline']) ?></p>
                <a href="#services" class="btn-hero">
                    <i class="fas fa-arrow-right me-2"></i>Explore Our Services
                </a>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section section-padding">
        <div class="container">
            <h2 class="section-title"><?= htmlspecialchars($company_info['about_title']) ?></h2>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-text">
                        <?php 
                        $about_paragraphs = explode("\n\n", $company_info['about_content']);
                        foreach ($about_paragraphs as $index => $paragraph): 
                        ?>
                            <p <?= $index === 0 ? 'class="lead"' : '' ?>><?= nl2br(htmlspecialchars(trim($paragraph))) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <?php foreach ($stats as $stat): ?>
                        <div class="col-md-6">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class="<?= htmlspecialchars($stat['icon']) ?>"></i>
                                </div>
                                <h4><?= htmlspecialchars($stat['value']) ?></h4>
                                <p><?= htmlspecialchars($stat['title']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="row">
                <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                        </div>
                        <h3 class="service-title"><?= htmlspecialchars($service['title']) ?></h3>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <?php 
                        $features = json_decode($service['features'], true);
                        if ($features && !empty($features)): 
                        ?>
                        <ul class="list-unstyled mt-3">
                            <?php foreach ($features as $feature): ?>
                            <li><i class="fas fa-check text-success me-2"></i><?= htmlspecialchars($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section section-padding">
        <div class="container">
            <h2 class="section-title text-white">Contact Us</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h3 class="mb-4">Get In Touch</h3>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <div>
                                <h5>Address</h5>
                                <p><?= nl2br(htmlspecialchars($company_info['address'])) ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone contact-icon"></i>
                            <div>
                                <h5>Phone</h5>
                                <p><?= nl2br(htmlspecialchars($company_info['phone'])) ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <div>
                                <h5>Email</h5>
                                <p><?= nl2br(htmlspecialchars($company_info['email'])) ?></p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock contact-icon"></i>
                            <div>
                                <h5>Working Hours</h5>
                                <p><?= nl2br(htmlspecialchars($company_info['working_hours'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h3 class="mb-4">Send Us a Message</h3>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control" placeholder="Your Name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control" placeholder="Your Email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Subject" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-light btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?= htmlspecialchars($company_info['company_name']) ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php if ($company_info['facebook_url']): ?>
                    <a href="<?= htmlspecialchars($company_info['facebook_url']) ?>" class="text-white me-3" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($company_info['twitter_url']): ?>
                    <a href="<?= htmlspecialchars($company_info['twitter_url']) ?>" class="text-white me-3" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($company_info['instagram_url']): ?>
                    <a href="<?= htmlspecialchars($company_info['instagram_url']) ?>" class="text-white me-3" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($company_info['linkedin_url']): ?>
                    <a href="<?= htmlspecialchars($company_info['linkedin_url']) ?>" class="text-white" target="_blank">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- Admin Portal Access -->
    <div class="admin-access">
        <a href="dashboard.php" class="admin-btn">
            <i class="fas fa-lock me-2"></i>Admin Portal
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Auto-play carousel
        const carousel = new bootstrap.Carousel(document.querySelector('#heroCarousel'), {
            interval: 5000,
            wrap: true
        });

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease-out';
                }
            });
        }, observerOptions);

        // Observe all service cards and feature boxes
        document.querySelectorAll('.service-card, .feature-box').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
