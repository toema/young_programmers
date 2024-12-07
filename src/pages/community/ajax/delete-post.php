<?php
require_once '../../../includes/config.php';
require_once '../../../includes/functions.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    die(json_encode([
        'success' => false, 
        'message' => 'يجب تسجيل الدخول أولاً'
    ]));
}

if(!isset($_POST['post_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'معرف المنشور مطلوب'
    ]));
}

try {
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    
    // التحقق من ملكية المنشور
    $post = getPost($post_id);
    if(!$post || ($post['user_id'] != $user_id && !isAdmin())) {
        throw new Exception("غير مسموح لك بحذف هذا المنشور");
    }
    
    if(deletePost($post_id, $user_id)) {
        echo json_encode([
            'success' => true,
            'message' => 'تم حذف المنشور بنجاح'
        ]);
    } else {
        throw new Exception("فشل حذف المنشور");
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
