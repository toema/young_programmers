<?php
// التحقق من الصلاحيات
if(!isAdmin()) {
    header("Location: " . SITE_URL);
    exit;
}

// إضافة مدير جديد
if(isset($_POST['add_admin']) && hasAdminPermission()) {
    try {
        $username = cleanInput($_POST['username']);
        $email = cleanInput($_POST['email']);
        $password = cleanInput($_POST['password']);
        $role = cleanInput($_POST['role']);
        
        if(addAdmin([
            'username' => $username, 
            'email' => $email, 
            'password' => $password,
            'role' => $role
        ])) {
            $success = "تم إضافة المستخدم بنجاح";
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

// تحديث الصلاحيات
if(isset($_POST['role']) && isset($_POST['admin_id']) && hasAdminPermission()) {
    $admin_id = (int)$_POST['admin_id'];
    $role = cleanInput($_POST['role']);
    if(updateAdminRole($admin_id, $role)) {
        $success = "تم تحديث الصلاحيات بنجاح";
    }
}

// حذف مدير
if(isset($_POST['delete_admin']) && hasAdminPermission()) {
    $admin_id = (int)$_POST['admin_id'];
    if($admin_id != $_SESSION['admin_id']) {
        if(deleteAdmin($admin_id)) {
            $success = "تم حذف المستخدم بنجاح";
        }
    }
}

$admins = getAllAdmins();
?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title">إدارة المستخدمين</h5>
            <?php if(hasAdminPermission()): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="fas fa-plus"></i> إضافة
            </button>
            <?php endif; ?>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الصلاحيات</th>
                        <th>تاريخ الإضافة</th>
                        <?php if(hasAdminPermission()): ?>
                        <th>الإجراءات</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($admins as $admin): ?>
                    <tr>
                        <td><?php echo $admin['id']; ?></td>
                        <td><?php echo $admin['username']; ?></td>
                        <td><?php echo $admin['email']; ?></td>
                        <td>
                            <?php if($admin['role'] == 'admin'): ?>
                                <span class="badge bg-primary">مدير</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">مشاهد</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatDate($admin['created_at']); ?></td>
                        <?php if(hasAdminPermission()): ?>
                        <td>
                            <?php if($admin['id'] != $_SESSION['admin_id']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="admin" <?php echo $admin['role'] == 'admin' ? 'selected' : ''; ?>>مدير</option>
                                        <option value="viewer" <?php echo $admin['role'] == 'viewer' ? 'selected' : ''; ?>>مشاهد</option>
                                    </select>
                                </form>
                                <?php if($admin['role'] != 'admin'): ?>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                        <button type="submit" name="delete_admin" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(hasAdminPermission()): ?>
<!-- Modal إضافة -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات</label>
                        <select name="role" class="form-select" required>
                            <option value="viewer">مشاهد</option>
                            <option value="admin">مدير</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" name="add_admin" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
