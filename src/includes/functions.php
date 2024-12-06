<?php
// دالة تنظيف المدخلات
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// دالة تحقق من تسجيل الدخول
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// دالة تحقق من نوع المستخدم
function isUserType($type) {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === $type;
}

// دالة التحقق من الأدمن
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// دالة للحصول على عدد المستخدمين
function getUsersCount() {
    global $conn;
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}

// دالة للحصول على جميع المستخدمين
function getAllUsers() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
    return $stmt->fetchAll();
}

// دالة للتحقق من صحة البريد الإلكتروني
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// دالة للتحقق من وجود البريد الإلكتروني
function emailExists($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->rowCount() > 0;
}

// دالة لإنشاء توكن عشوائي
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// دالة للتحقق من نوع الملف
function isAllowedFileType($filename, $allowed = ['jpg', 'jpeg', 'png', 'gif']) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowed);
}

// دالة للحصول على معلومات المستخدم
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// دالة لتحديث معلومات المستخدم
function updateUser($id, $data) {
    global $conn;
    $fields = [];
    $values = [];
    foreach($data as $key => $value) {
        $fields[] = "$key = ?";
        $values[] = $value;
    }
    $values[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute($values);
}

// دالة لحذف المستخدم
function deleteUser($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// دالة للحصول على الأطفال المرتبطين بولي الأمر
function getChildrenByParentId($parent_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT users.* FROM users 
                           JOIN parent_child ON users.id = parent_child.child_id 
                           WHERE parent_child.parent_id = ?");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll();
}

// دالة للتحقق من العمر
function calculateAge($birthdate) {
    $today = new DateTime();
    $birth = new DateTime($birthdate);
    return $birth->diff($today)->y;
}

// دالة لإنشاء رابط دعوة
function createInviteLink($parent_id) {
    global $conn;
    $token = generateToken();
    $stmt = $conn->prepare("INSERT INTO parent_invites (parent_id, token) VALUES (?, ?)");
    $stmt->execute([$parent_id, $token]);
    return SITE_URL . "/pages/register.php?parent_token=" . $token;
}

// دالة للتحقق من صلاحية رابط الدعوة
function validateInviteToken($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM parent_invites 
                           WHERE token = ? AND used = 0 AND expired = 0 
                           AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

// دالة لتنسيق التاريخ
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// دالة لإنشاء رسالة تنبيه
function createAlert($message, $type = 'success') {
    return "<div class='alert alert-{$type}'>{$message}</div>";
}

// دالة للتحقق من الصلاحيات
function checkPermission($permission) {
    if(!isAdmin()) {
        header("Location: " . SITE_URL);
        exit;
    }
}

// دالة للحصول على عدد مشاريع الأدمن
function getProjectsCount() {
    global $conn;
    $stmt = $conn->query("SELECT COUNT(*) FROM projects_admin");
    return $stmt->fetchColumn();
}

// دالة للحصول على جميع مشاريع الأدمن
function getAllAdminProjects() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM projects_admin ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// دالة لإضافة مشروع أدمن جديد
function addAdminProject($data) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO projects_admin 
                           (title, description, category, age_range, required_skills, price, status, deadline) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['category'],
        $data['age_range'],
        $data['required_skills'],
        $data['price'],
        $data['status'] ?? 'draft',
        $data['deadline']
    ]);
}

// دالة لتحديث مشروع أدمن
function updateAdminProject($id, $data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE projects_admin 
                           SET title = ?, description = ?, category = ?, 
                               age_range = ?, required_skills = ?, price = ?, 
                               status = ?, deadline = ? 
                           WHERE id = ?");
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['category'],
        $data['age_range'],
        $data['required_skills'],
        $data['price'],
        $data['status'],
        $data['deadline'],
        $id
    ]);
}

// دالة لحذف مشروع أدمن
function deleteAdminProject($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM projects_admin WHERE id = ?");
    return $stmt->execute([$id]);
}

// دالة للحصول على جميع المديرين
function getAllAdmins() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM admins ORDER BY id DESC");
    return $stmt->fetchAll();
}

// دالة لإضافة مدير جديد
function addAdmin($data) {
    global $conn;
    
    // التحقق من البريد الإلكتروني
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$data['email']]);
    if($stmt->rowCount() > 0) {
        throw new Exception("البريد الإلكتروني مستخدم بالفعل");
    }
    
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
    return $stmt->execute([
        $data['username'],
        $data['email'],
        $data['password'],
        $data['role'] ?? 'viewer'
    ]);
}

// دالة لحذف مدير
function deleteAdmin($id) {
    global $conn;
    // لا يمكن حذف المدير الرئيسي
    $stmt = $conn->prepare("SELECT role FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();
    if($admin['role'] == 'admin') {
        return false;
    }
    
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    return $stmt->execute([$id]);
}

// دالة للتحقق من صلاحيات المدير
function hasAdminPermission($permission = 'admin') {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == $permission;
}

// دالة لتحديث صلاحيات المدير
function updateAdminRole($id, $role) {
    global $conn;
    $stmt = $conn->prepare("UPDATE admins SET role = ? WHERE id = ?");
    return $stmt->execute([$role, $id]);
}

// دالة لحفظ إعداد
function saveAdminSetting($key, $value) {
    global $conn;
    
    // تنظيف البيانات
    $key = cleanInput($key);
    $value = cleanInput($value);
    
    // التحقق من وجود الإعداد
    $stmt = $conn->prepare("SELECT id FROM settings_admin WHERE setting_key = ?");
    $stmt->execute([$key]);
    
    if($stmt->rowCount() > 0) {
        // تحديث الإعداد الموجود
        $stmt = $conn->prepare("UPDATE settings_admin SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    } else {
        // إضافة إعداد جديد
        $stmt = $conn->prepare("INSERT INTO settings_admin (setting_key, setting_value) VALUES (?, ?)");
        return $stmt->execute([$key, $value]);
    }
}

// دالة للحصول على إعداد
function getAdminSetting($key) {
    global $conn;
    
    // تنظيف البيانات
    $key = cleanInput($key);
    
    $stmt = $conn->prepare("SELECT setting_value FROM settings_admin WHERE setting_key = ?");
    $stmt->execute([$key]);
    
    $result = $stmt->fetch(PDO::FETCH_COLUMN);
    return $result !== false ? $result : null;
}

// دالة للحصول على جميع الإعدادات
function getAllAdminSettings() {
    global $conn;
    $stmt = $conn->query("SELECT setting_key, setting_value FROM settings_admin");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

// دالة لحذف إعداد
function deleteAdminSetting($key) {
    global $conn;
    $key = cleanInput($key);
    $stmt = $conn->prepare("DELETE FROM settings_admin WHERE setting_key = ?");
    return $stmt->execute([$key]);
}

// دالة للتحقق من وجود إعداد
function adminSettingExists($key) {
    global $conn;
    $key = cleanInput($key);
    $stmt = $conn->prepare("SELECT id FROM settings_admin WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->rowCount() > 0;
}

// دالة لتحديث مجموعة من الإعدادات
function updateAdminSettings($settings) {
    global $conn;
    try {
        $conn->beginTransaction();
        foreach($settings as $key => $value) {
            saveAdminSetting($key, $value);
        }
        $conn->commit();
        return true;
    } catch(Exception $e) {
        $conn->rollBack();
        return false;
    }
}

// دالة لاسترجاع الإعدادات الافتراضية
function resetAdminSettings() {
    $default_settings = [
        'site_title' => 'مرحباً بك في منصة المبرمج الصغير',
        'site_description' => 'طور مهاراتك البرمجية وشارك في مشاريع حقيقية مع مجتمع من المبرمجين الصغار'
    ];
    return updateAdminSettings($default_settings);
}

// دالة للتحقق من صلاحيات تعديل الإعدادات
function canManageSettings() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 'admin';
}

// دالة لتنظيف وتحقق من الإعدادات
function validateSetting($key, $value) {
    $key = cleanInput($key);
    $value = cleanInput($value);
    
    if(strlen($key) < 3 || strlen($key) > 50) {
        throw new Exception("مفتاح الإعداد يجب أن يكون بين 3 و 50 حرف");
    }
    
    if(!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
        throw new Exception("مفتاح الإعداد يجب أن يحتوي على أحرف وأرقام وشرطة سفلية فقط");
    }
    
    if(empty($value)) {
        throw new Exception("قيمة الإعداد لا يمكن أن تكون فارغة");
    }
    
    return true;
}

// دالة لحفظ إعداد مع التحقق
function saveSetting($key, $value) {
    try {
        if(!canManageSettings()) {
            throw new Exception("ليس لديك صلاحية لتعديل الإعدادات");
        }
        
        validateSetting($key, $value);
        return saveAdminSetting($key, $value);
    } catch(Exception $e) {
        throw $e;
    }
}
// دالة جلب جميع المنشورات
// دالة جلب جميع المنشورات
function getAllPosts($type = null, $sort = 'latest') {
    global $conn;
    
    try {
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        // إضافة فلتر النوع
        if($type && $type !== 'all') {
            $sql .= " AND p.type = ?";
            $params[] = $type;
        }
        
        // إضافة الترتيب
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // تسجيل للتأكد
        error_log("Found " . count($posts) . " posts");
        error_log("SQL Query: " . $sql);
        error_log("Posts data: " . print_r($posts, true));
        
        return $posts;
    } catch(PDOException $e) {
        error_log("Error in getAllPosts: " . $e->getMessage());
        return [];
    }
}

// دالة إضافة منشور
function addPost($data) {
    global $conn;
    
    try {
        // طباعة البيانات المستلمة
        echo "Adding post with data:";
        print_r($data);
        
        $sql = "INSERT INTO posts (user_id, title, content, type) VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['content'],
            $data['type']
        ]);
        
        if($result) {
            // طباعة معرف المنشور الجديد
            echo "New post ID: " . $conn->lastInsertId();
            return true;
        }
        
        return false;
    } catch(PDOException $e) {
        echo "Error adding post: " . $e->getMessage();
        return false;
    }
}

// دالة جلب منشور واحد
function getPost($post_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT p.*, u.username 
                               FROM posts p 
                               JOIN users u ON p.user_id = u.id 
                               WHERE p.id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error in getPost: " . $e->getMessage());
        return null;
    }
}

// دالة حذف منشور
function deletePost($post_id, $user_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("DELETE FROM posts 
                               WHERE id = ? AND user_id = ?");
        return $stmt->execute([$post_id, $user_id]);
    } catch(PDOException $e) {
        error_log("Error in deletePost: " . $e->getMessage());
        return false;
    }
}

// دالة تحويل نوع المنشور إلى نص عربي
function getPostTypeName($type) {
    $types = [
        'question' => 'سؤال',
        'share' => 'مشاركة',
        'tip' => 'نصيحة'
    ];
    return $types[$type] ?? $type;
}

// دالة تنسيق التاريخ
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $time_difference = time() - $time;

    if($time_difference < 1) { return 'الآن'; }
    
    $conditions = [
        12 * 30 * 24 * 60 * 60 => 'سنة',
        30 * 24 * 60 * 60      => 'شهر',
        24 * 60 * 60           => 'يوم',
        60 * 60                => 'ساعة',
        60                     => 'دقيقة',
        1                      => 'ثانية'
    ];

    foreach($conditions as $secs => $str) {
        $d = $time_difference / $secs;
        if($d >= 1) {
            $t = round($d);
            return 'منذ ' . $t . ' ' . $str . ($t > 1 ? 'ة' : '');
        }
    }
}
// دالة التحقق من إعجاب المستخدم
function hasUserLiked($post_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM reactions WHERE post_id = ? AND user_id = ? AND type = 'like'");
    $stmt->execute([$post_id, $user_id]);
    return $stmt->rowCount() > 0;
}

// دالة إضافة إعجاب
function addLike($post_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, 'like')");
    return $stmt->execute([$post_id, $user_id]);
}

// دالة إزالة إعجاب
function removeLike($post_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM reactions WHERE post_id = ? AND user_id = ? AND type = 'like'");
    return $stmt->execute([$post_id, $user_id]);
}

// دالة جلب عدد الإعجابات
function getLikesCount($post_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reactions WHERE post_id = ? AND type = 'like'");
    $stmt->execute([$post_id]);
    return $stmt->fetchColumn();
}

// دالة إضافة تعليق
function addComment($data) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    return $stmt->execute([
        $data['post_id'],
        $data['user_id'],
        $data['content']
    ]);
}

// دالة جلب تعليقات منشور
function getPostComments($post_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT c.*, u.username 
                           FROM comments c 
                           JOIN users u ON c.user_id = u.id 
                           WHERE c.post_id = ? 
                           ORDER BY c.created_at DESC");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// دالة جلب عدد التعليقات
function getCommentsCount($post_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return $stmt->fetchColumn();
}
