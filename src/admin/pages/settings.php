<?php
// التحقق من الصلاحيات
if(!hasAdminPermission('admin')) {
    header("Location: " . SITE_URL);
    exit;
}

// متغير لتخزين رسائل النجاح
$successMessages = [];

// حفظ إعدادات الصفحة الرئيسية
if(isset($_POST['save_settings'])) {
    try {
        // حفظ النصوص
        saveAdminSetting('site_title', $_POST['site_title']);
        saveAdminSetting('site_description', $_POST['site_description']);
        saveAdminSetting('register_button_text', $_POST['register_button_text']);
        saveAdminSetting('video_button_text', $_POST['video_button_text']);
        saveAdminSetting('video_url', $_POST['video_url']);
        
        // معالجة الصورة
        if(isset($_FILES['hero_image']) && $_FILES['hero_image']['size'] > 0) {
            $file = $_FILES['hero_image'];
            if(isAllowedFileType($file['name'])) {
                $upload_dir = __DIR__ . '/../../asset/images/';
                $filename = 'hero-' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                
                if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                    saveAdminSetting('hero_image', '/asset/images/' . $filename);
                }
            }
        }
        
        $successMessages[] = "تم تحديث إعدادات الصفحة الرئيسية بنجاح";
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// حفظ إعدادات المميزات
if(isset($_POST['save_features'])) {
    try {
        for($i = 1; $i <= 3; $i++) {
            saveAdminSetting('feature'.$i.'_icon', $_POST['feature'.$i.'_icon']);
            saveAdminSetting('feature'.$i.'_title', $_POST['feature'.$i.'_title']);
            saveAdminSetting('feature'.$i.'_description', $_POST['feature'.$i.'_description']);
        }
        $successMessages[] = "تم تحديث المميزات بنجاح";
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!-- عرض رسائل النجاح -->
<?php if(!empty($successMessages)): ?>
    <?php foreach($successMessages as $message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $message; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- عرض رسائل الخطأ -->
<?php if(isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<!-- إعدادات الصفحة الرئيسية -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">إعدادات الصفحة الرئيسية</h5>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">عنوان الموقع الرئيسي</label>
                <input type="text" name="site_title" class="form-control" 
                       value="<?php echo getAdminSetting('site_title'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">وصف الموقع</label>
                <textarea name="site_description" class="form-control" rows="3" required>
                    <?php echo getAdminSetting('site_description'); ?>
                </textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">نص زر التسجيل</label>
                <input type="text" name="register_button_text" class="form-control" 
                       value="<?php echo getAdminSetting('register_button_text'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">نص زر الفيديو</label>
                <input type="text" name="video_button_text" class="form-control" 
                       value="<?php echo getAdminSetting('video_button_text'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">رابط الفيديو</label>
                <input type="url" name="video_url" class="form-control" 
                       value="<?php echo getAdminSetting('video_url'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">صورة القسم الرئيسي</label>
                <input type="file" name="hero_image" class="form-control" accept="image/*">
                <small class="text-muted">اترك فارغاً للإبقاء على الصورة الحالية</small>
                <?php if($hero_image = getAdminSetting('hero_image')): ?>
                    <div class="mt-2">
                        <img src="<?php echo SITE_URL . $hero_image; ?>" 
                             alt="الصورة الحالية" style="max-width: 200px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="save_settings" class="btn btn-primary">
                حفظ الإعدادات
            </button>
        </form>
    </div>
</div>

<!-- إعدادات المميزات -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">إعدادات المميزات</h5>
        
        <form method="POST">
            <?php for($i = 1; $i <= 3; $i++): ?>
                <div class="feature-settings mb-4 p-3 border rounded">
                    <h6>الميزة <?php echo $i; ?></h6>
                    
                    <div class="mb-3">
                        <label class="form-label">الأيقونة</label>
                        <select name="feature<?php echo $i; ?>_icon" class="form-control">
                            <option value="fa-project-diagram" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-project-diagram' ? 'selected' : ''; ?>>مشاريع</option>
                            <option value="fa-users" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-users' ? 'selected' : ''; ?>>مستخدمين</option>
                            <option value="fa-comments" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-comments' ? 'selected' : ''; ?>>تعليقات</option>
                            <option value="fa-code" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-code' ? 'selected' : ''; ?>>كود</option>
                            <option value="fa-laptop-code" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-laptop-code' ? 'selected' : ''; ?>>برمجة</option>
                            <option value="fa-brain" <?php echo getAdminSetting('feature'.$i.'_icon') == 'fa-brain' ? 'selected' : ''; ?>>تعلم</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="feature<?php echo $i; ?>_title" class="form-control"
                               value="<?php echo getAdminSetting('feature'.$i.'_title'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="feature<?php echo $i; ?>_description" class="form-control" rows="2" required>
                            <?php echo getAdminSetting('feature'.$i.'_description'); ?>
                        </textarea>
                    </div>
                </div>
            <?php endfor; ?>
            
            <button type="submit" name="save_features" class="btn btn-primary">
                حفظ المميزات
            </button>
        </form>
    </div>
</div>

<style>
.feature-settings {
    background-color: #f8f9fa;
}
.feature-settings:hover {
    background-color: #f1f3f5;
}
</style>
