<?php
require_once '../includes/config.php';

// التحقق من تسجيل الدخول
if(!isLoggedIn()) {
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

require_once '../includes/header.php';
?>

<div class="container-fluid">
<div class="sidebar bg-white border-end">
        <div class="text-center p-4">
            <img src="<?php echo !empty($_SESSION['profile_image']) ? UPLOAD_URL . $_SESSION['profile_image'] : SITE_URL . '/assets/images/default-avatar.png'; ?>" 
                 class="rounded-circle mb-3" 
                 width="80" 
                 height="80"
                 alt="الصورة الشخصية">
            <h6 class="mb-1"><?php echo $_SESSION['username']; ?></h6>
            <small class="text-muted d-block mb-3">
                <?php echo isUserType('parent') ? 'ولي أمر' : 'طفل'; ?>
            </small>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active d-flex align-items-center" href="dashboard.php">
                    <i class="fas fa-home ms-2"></i>
                    لوحة التحكم
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="settings.php">
                    <i class="fas fa-cog ms-2"></i>
                    الإعدادات
                </a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-danger d-flex align-items-center" href="logout.php">
                    <i class="fas fa-sign-out-alt ms-2"></i>
                    تسجيل الخروج
                </a>
            </li>
        </ul>
    </div>
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">مرحباً <?php echo $_SESSION['username']; ?></h1>
            </div>

            <?php if(isUserType('parent')): ?>
                <!-- رابط الدعوة لولي الأمر -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1">شارك هذا الرابط مع طفلك لإنشاء حساب مرتبط بك مباشرة:</h5>
                                <p class="text-muted small mb-0">* سيتم ربط حساب طفلك بحسابك تلقائياً عند التسجيل</p>
                            </div>
                            <button class="btn btn-warning" onclick="copyLink(this)" 
                                    data-link="<?php echo SITE_URL; ?>/pages/register.php?parent_id=<?php echo $_SESSION['user_id']; ?>">
                                <i class="fas fa-copy"></i>
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- قسم الأطفال/ولي الأمر -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <?php echo isUserType('parent') ? 'الأطفال المرتبطين' : 'معلومات ولي الأمر'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    if(isUserType('parent')) {
                        // عرض الأطفال المرتبطين لولي الأمر
                        $stmt = $conn->prepare("
                            SELECT c.*, pc.status 
                            FROM users c 
                            JOIN parent_child pc ON c.id = pc.child_id 
                            WHERE pc.parent_id = ? AND pc.status = 'approved'
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $children = $stmt->fetchAll();

                        if(count($children) > 0): ?>
                            <div class="row">
                                <?php foreach($children as $child): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="child-card">
                                            <img src="<?php echo !empty($child['profile_image']) ? UPLOAD_URL . $child['profile_image'] : SITE_URL . '/assets/images/default-avatar.png'; ?>" 
                                                 class="rounded-circle mb-2" 
                                                 width="60" 
                                                 height="60"
                                                 alt="صورة الطفل">
                                            <h6 class="mb-1"><?php echo $child['username']; ?></h6>
                                            <p class="text-muted small mb-2"><?php echo $child['email']; ?></p>
                                            <span class="badge bg-success">مرتبط</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <img src="<?php echo SITE_URL; ?>/assets/images/no-children.svg" 
                                     alt="لا يوجد أطفال" 
                                     class="mb-3" 
                                     width="150">
                                <h5 class="text-muted">لا يوجد أطفال مرتبطين حالياً</h5>
                                <p class="text-muted">قم بمشاركة رابط التسجيل مع طفلك</p>
                            </div>
                        <?php endif;
                    } else {
                        // عرض معلومات ولي الأمر للطفل
                        $stmt = $conn->prepare("
                            SELECT p.* 
                            FROM users p 
                            JOIN parent_child pc ON p.id = pc.parent_id 
                            WHERE pc.child_id = ? AND pc.status = 'approved'
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $parent = $stmt->fetch();

                        if($parent): ?>
                            <div class="text-center py-3">
                                <img src="<?php echo !empty($parent['profile_image']) ? UPLOAD_URL . $parent['profile_image'] : SITE_URL . '/assets/images/default-avatar.png'; ?>" 
                                     class="rounded-circle mb-3" 
                                     width="100"
                                     height="100"
                                     alt="صورة ولي الأمر">
                                <h5><?php echo $parent['username']; ?></h5>
                                <p class="text-muted"><?php echo $parent['email']; ?></p>
                                <span class="badge bg-success">مرتبط</span>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <img src="<?php echo SITE_URL; ?>/assets/images/no-parent.svg" 
                                     alt="لا يوجد ولي أمر" 
                                     class="mb-3" 
                                     width="150">
                                <h5 class="text-muted">لم يتم ربطك بولي أمر بعد</h5>
                            </div>
                        <?php endif;
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    right: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.5rem 1rem;
    margin: 0.2rem 0;
    border-radius: 5px;
}

.sidebar .nav-link:hover {
    background-color: #f8f9fa;
}

.sidebar .nav-link.active {
    background-color: #ffc107;
    color: #000;
}

.child-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.child-card:hover {
    transform: translateY(-5px);
}

.child-card img {
    border: 3px solid #ffc107;
    padding: 2px;
}

main {
    margin-right: 240px;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: static;
        padding-top: 0;
    }
    main {
        margin-right: 0;
    }
}
</style>

<script>
function copyLink(button) {
    const link = button.dataset.link;
    navigator.clipboard.writeText(link).then(() => {
        // تغيير نص الزر مؤقتاً
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
