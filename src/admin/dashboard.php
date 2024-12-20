<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// التحقق من تسجيل الدخول
if(!isAdmin()) {
    header("Location: login.php");
    exit;
}

// التحقق من الصفحة المطلوبة
$page = isset($_GET['page']) ? $_GET['page'] : 'overview';
$allowed_pages = ['overview', 'users', 'projects', 'admins', 'settings'];
if(!in_array($page, $allowed_pages)) {
    $page = 'overview';
}

// التحقق من صلاحيات المستخدم
$isViewer = $_SESSION['admin_role'] == 'viewer';
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
        }
        .list-group-item {
            border-radius: 0 !important;
        }
        .list-group-item.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        <?php if($isViewer): ?>
        .btn-add, .btn-edit, .btn-delete, 
        input[type="submit"], button[type="submit"],
        [data-bs-toggle="modal"] {
            display: none !important;
        }
        select:disabled {
            background: #e9ecef;
            cursor: not-allowed;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">لوحة التحكم</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text mx-3">
                    مرحباً، <?php echo $_SESSION['admin_username']; ?>
                    <?php if($isViewer): ?>
                        <span class="badge bg-secondary">مشاهد</span>
                    <?php else: ?>
                        <span class="badge bg-primary">مدير</span>
                    <?php endif; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    تسجيل خروج
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="card">
                    <div class="list-group list-group-flush">
                        <a href="?page=overview" class="list-group-item list-group-item-action <?php echo $page == 'overview' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>نظرة عامة
                        </a>
                        <a href="?page=users" class="list-group-item list-group-item-action <?php echo $page == 'users' ? 'active' : ''; ?>">
                            <i class="fas fa-users me-2"></i>المستخدمين
                        </a>
                        <a href="?page=projects" class="list-group-item list-group-item-action <?php echo $page == 'projects' ? 'active' : ''; ?>">
                            <i class="fas fa-project-diagram me-2"></i>المشاريع
                        </a>
                        <?php if(!$isViewer): ?>
                        <a href="?page=admins" class="list-group-item list-group-item-action <?php echo $page == 'admins' ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield me-2"></i>المستخدمين
                        </a>
                        <a href="?page=settings" class="list-group-item list-group-item-action <?php echo $page == 'settings' ? 'active' : ''; ?>">
                            <i class="fas fa-cog me-2"></i>الإعدادات
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9 col-lg-10">
                <?php if($isViewer): ?>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        أنت في وضع المشاهدة فقط. لا يمكنك إجراء تعديلات.
                    </div>
                <?php endif; ?>

                <?php
                switch($page) {
                    case 'overview':
                        include 'pages/overview.php';
                        break;
                    case 'users':
                        include 'pages/users.php';
                        break;
                    case 'projects':
                        include 'pages/projects.php';
                        break;
                    case 'admins':
                        if(!$isViewer) include 'pages/admins.php';
                        break;
                    case 'settings':
                        if(!$isViewer) include 'pages/settings.php';
                        break;
                    default:
                        include 'pages/overview.php';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        <?php if($isViewer): ?>
        // تعطيل جميع الأزرار والنماذج للمشاهد
        $(document).ready(function() {
            $('form').submit(function(e) {
                e.preventDefault();
                return false;
            });
            
            $('select, input, textarea').prop('disabled', true);
            $('a[data-bs-toggle="modal"]').removeAttr('data-bs-toggle');
        });
        <?php else: ?>
        // تفعيل التلميحات
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // تأكيد الحذف
        function confirmDelete() {
            return confirm('هل أنت متأكد من الحذف؟');
        }
        <?php endif; ?>
    </script>
</body>
</html>
