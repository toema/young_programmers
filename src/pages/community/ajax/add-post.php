<?php
require_once '../../../includes/config.php';
require_once '../../../includes/functions.php';

// تفعيل عرض الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// طباعة البيانات المستلمة
echo "Received POST data:";
print_r($_POST);

if(!isLoggedIn()) {
    die(json_encode([
        'success' => false, 
        'message' => 'يجب تسجيل الدخول أولاً'
    ]));
}

try {
    if(empty($_POST['title']) || empty($_POST['content']) || empty($_POST['type'])) {
        throw new Exception("جميع الحقول مطلوبة");
    }

    $post_data = [
        'user_id' => $_SESSION['user_id'],
        'title' => trim($_POST['title']),
        'content' => trim($_POST['content']),
        'type' => $_POST['type']
    ];

    // طباعة بيانات المنشور
    echo "Post data to be inserted:";
    print_r($post_data);

    if(addPost($post_data)) {
        echo json_encode([
            'success' => true,
            'message' => 'تم إضافة المنشور بنجاح'
        ]);
    } else {
        throw new Exception("حدث خطأ أثناء إضافة المنشور");
    }

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
