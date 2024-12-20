<?php
require_once '../includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'parent') {
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $child_email = cleanInput($_POST['child_email']);
        
        // التحقق من وجود الطفل
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND type = 'child'");
        $stmt->execute([$child_email]);
        $child = $stmt->fetch();
        
        if(!$child) {
            throw new Exception("لم يتم العثور على حساب طفل بهذا البريد");
        }
        
        // التحقق من عدم وجود ربط سابق
        $stmt = $conn->prepare("SELECT id FROM parent_child WHERE parent_id = ? AND child_id = ?");
        $stmt->execute([$_SESSION['user_id'], $child['id']]);
        if($stmt->rowCount() > 0) {
            throw new Exception("هذا الطفل مرتبط بحسابك بالفعل");
        }
        
        // إضافة الربط
        $stmt = $conn->prepare("INSERT INTO parent_child (parent_id, child_id, status) VALUES (?, ?, 'approved')");
        $stmt->execute([$_SESSION['user_id'], $child['id']]);
        
        $_SESSION['success'] = "تم ربط الطفل بنجاح";
        header("Location: " . SITE_URL . "/pages/dashboard.php");
        exit;
        
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="add-child-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="add-child-card">
                    <div class="card-body">
                        <h3 class="card-title">إضافة طفل</h3>
                        <p class="text-muted">أدخل البريد الإلكتروني للطفل لربطه بحسابك</p>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group mb-4">
                                <label class="form-label">البريد الإلكتروني للطفل</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           name="child_email" 
                                           class="form-control" 
                                           required
                                           placeholder="أدخل البريد الإلكتروني">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-plus"></i>
                                إضافة الطفل
                            </button>

                            <a href="<?php echo SITE_URL; ?>/pages/dashboard.php" 
                               class="btn btn-light w-100 mt-3">
                                <i class="fas fa-arrow-right"></i>
                                رجوع للوحة التحكم
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.add-child-section {
    background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
    min-height: calc(100vh - 70px);
}

.add-child-card {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.card-title {
    color: #333;
    margin-bottom: 10px;
}

.input-group-text {
    background: transparent;
    border-right: none;
}

.form-control {
    border-left: none;
    padding: 12px;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(255,193,7,0.25);
}

.btn {
    padding: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-warning {
    background: #ffc107;
    border: none;
}

.btn-warning:hover {
    background: #ffb300;
    transform: translateY(-2px);
}

.btn-light {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.alert {
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert i {
    font-size: 1.2rem;
}
</style>

<?php require_once '../includes/footer.php'; ?>
