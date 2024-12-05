<?php
require_once '../includes/config.php';

if(!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// جلب بيانات المستخدم
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // تحديث الصورة الشخصية
        if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(!in_array($ext, $allowed)) {
                throw new Exception("نوع الملف غير مسموح به. الأنواع المسموحة: " . implode(', ', $allowed));
            }
            
            // إنشاء اسم فريد للملف
            $newname = uniqid() . '.' . $ext;
            
            // التأكد من وجود المجلد
            if (!file_exists(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0777, true);
            }
            
            // رفع الملف
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], UPLOAD_PATH . $newname)) {
                // حذف الصورة القديمة
                if(!empty($user['profile_image'])) {
                    $old_file = UPLOAD_PATH . $user['profile_image'];
                    if(file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                // تحديث في قاعدة البيانات
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                if($stmt->execute([$newname, $_SESSION['user_id']])) {
                    $_SESSION['profile_image'] = $newname;
                    $success = "تم تحديث الصورة الشخصية بنجاح";
                }
            }
        }
        
        // تحديث البيانات الأخرى
        if(isset($_POST['update_profile'])) {
            $username = cleanInput($_POST['username']);
            $email = cleanInput($_POST['email']);
            
            // التحقق من البريد الإلكتروني
            if($email !== $user['email']) {
                $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $check->execute([$email, $_SESSION['user_id']]);
                if($check->rowCount() > 0) {
                    throw new Exception("البريد الإلكتروني مستخدم بالفعل");
                }
            }
            
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            if($stmt->execute([$username, $email, $_SESSION['user_id']])) {
                $_SESSION['username'] = $username;
                $success = "تم تحديث البيانات بنجاح";
            }
        }
        
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">الإعدادات الشخصية</h4>

                    <?php if(isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- رفع الصورة الشخصية -->
                    <div class="text-center mb-4">
                        <img src="<?php echo !empty($user['profile_image']) ? UPLOAD_URL . $user['profile_image'] : SITE_URL . '/assets/images/default-avatar.png'; ?>" 
                             class="rounded-circle mb-3" 
                             width="150" 
                             height="150"
                             alt="الصورة الشخصية">
                        
                        <form method="POST" enctype="multipart/form-data" class="mt-3">
                            <div class="d-flex justify-content-center">
                                <div class="upload-btn-wrapper">
                                    <button class="btn btn-outline-warning" type="button">
                                        <i class="fas fa-camera"></i>
                                        تغيير الصورة
                                    </button>
                                    <input type="file" 
                                           name="profile_image" 
                                           accept="image/*"
                                           onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تحديث البيانات -->
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="username" 
                                   value="<?php echo $user['username']; ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" 
                                   class="form-control" 
                                   name="email" 
                                   value="<?php echo $user['email']; ?>" 
                                   required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.upload-btn-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.upload-btn-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}
</style>

<?php require_once '../includes/footer.php'; ?>
