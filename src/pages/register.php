<!-- register.php -->
<div class="register-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="register-card">
                    <div class="register-header text-center">
                        <img src="<?php echo SITE_URL; ?>/asset/images/welcome-kid.svg" alt="مرحباً" class="welcome-img">
                        <h2>انضم لمجتمع المبرمجين الصغار! 🚀</h2>
                        <p>يلا نبدأ مغامرة البرمجة مع أصدقاء جدد</p>
                    </div>

                    <form class="register-form" method="POST" action="">
                        <div class="form-group mb-4">
                            <label class="fun-label">
                                <i class="fas fa-user"></i>
                                اسمك إيه؟
                            </label>
                            <input type="text" class="form-control fun-input" name="name" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fun-label">
                                <i class="fas fa-calendar"></i>
                                عندك كام سنة؟
                            </label>
                            <select class="form-control fun-input" name="age" required>
                                <option value="">اختار عمرك</option>
                                <?php for($i=10; $i<=18; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> سنة</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fun-label">
                                <i class="fas fa-envelope"></i>
                                الإيميل بتاعك
                            </label>
                            <input type="email" class="form-control fun-input" name="email" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fun-label">
                                <i class="fas fa-lock"></i>
                                اختار كلمة سر سرية
                            </label>
                            <input type="password" class="form-control fun-input" name="password" required>
                            <small class="text-muted">لازم تكون 8 حروف على الأقل</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fun-label">
                                <i class="fas fa-code"></i>
                                بتحب إيه في البرمجة؟
                            </label>
                            <div class="interest-buttons">
                                <input type="checkbox" id="web" name="interests[]" value="web">
                                <label for="web" class="interest-btn">
                                    <i class="fas fa-globe"></i>
                                    مواقع
                                </label>

                                <input type="checkbox" id="games" name="interests[]" value="games">
                                <label for="games" class="interest-btn">
                                    <i class="fas fa-gamepad"></i>
                                    ألعاب
                                </label>

                                <input type="checkbox" id="apps" name="interests[]" value="apps">
                                <label for="apps" class="interest-btn">
                                    <i class="fas fa-mobile-alt"></i>
                                    تطبيقات
                                </label>

                                <input type="checkbox" id="ai" name="interests[]" value="ai">
                                <label for="ai" class="interest-btn">
                                    <i class="fas fa-robot"></i>
                                    ذكاء اصطناعي
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 register-btn">
                            <i class="fas fa-rocket"></i>
                            يلا نبدأ المغامرة!
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
