<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-end">
                <h1>مرحباً بك في منصة المبرمج الصغير</h1>
                <p class="lead">طور مهاراتك البرمجية وشارك في مشاريع حقيقية مع مجتمع من المبرمجين الصغار</p>
                <div class="mt-4">
                    <a href="<?php echo SITE_URL; ?>/register" class="btn btn-warning">سجل الآن</a>
                    <a href="#" class="btn btn-outline-warning ms-2">
                        <i class="fas fa-play-circle ms-1"></i>
                        شاهد فيديو
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <img src="<?php echo SITE_URL; ?>/asset/images/logo.png" alt="المبرمج الصغير" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                    <h3>مشاريع برمجية</h3>
                    <p>تحتاج لتنفيذ مشروع؟ اطلب - مبرمج موهوب؟ نفذ مشاريع</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3>فرق برمجية</h3>
                    <p>ابحث عن فريق أو كوّن فريقك الخاص للعمل على المشاريع</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                    <h3>مجتمع برمجي</h3>
                    <p>اطرح أسئلتك وشارك خبراتك مع مجتمع المبرمجين الصغار</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="categories-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">
                <i class="fas fa-th"></i>
                أقسامنا
            </h2>
            <p class="text-muted">اختر القسم المناسب لمهاراتك</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="category-content">
                        <h3>برمجة وتطوير</h3>
                        <p>مواقع وتطبيقات</p>
                        <a href="#" class="category-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <div class="category-content">
                        <h3>تصميم</h3>
                        <p>واجهات وجرافيك</p>
                        <a href="#" class="category-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="category-content">
                        <h3>تطوير ألعاب</h3>
                        <p>ألعاب تفاعلية</p>
                        <a href="#" class="category-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="category-content">
                        <h3>الذكاء الاصطناعي</h3>
                        <p>تعلم الآلة والروبوتات</p>
                        <a href="#" class="category-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Us Section -->
<div class="why-us-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i>
                لماذا نحن؟
            </h2>
            <p class="text-muted">منصة آمنة للمبرمجين الصغار للتعلم والعمل معاً</p>
        </div>

        <div class="row align-items-center">
    <div class="col-lg-6">
        <div class="benefits-list">
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="benefit-content">
                    <h4>بيئة آمنة ومناسبة لعمرك</h4>
                    <p>موقعنا مخصص للمبرمجين من سن 10 إلى 18 سنة</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="benefit-content">
                    <h4>مجتمع من نفس عمرك</h4>
                    <p>تواصل وتعاون مع مبرمجين في مثل سنك</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="benefit-content">
                    <h4>تعلم وتطور مهاراتك</h4>
                    <p>اكتسب خبرة عملية من خلال المشاريع الحقيقية</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="benefit-content">
                    <h4>إشراف ومتابعة</h4>
                    <p>نظام متكامل للإشراف وحماية المستخدمين</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="why-us-image">
            <div class="image-wrapper">
                <img src="<?php echo SITE_URL; ?>/asset/images/young-programmer.png" alt="مبرمج صغير" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
