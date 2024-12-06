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
    
    // التحقق من وجود إعجاب سابق
    $existing_like = hasUserLiked($post_id, $user_id);
    
    if($existing_like) {
        // إزالة الإعجاب
        removeLike($post_id, $user_id);
        $action = 'unliked';
    } else {
        // إضافة إعجاب
        addLike($post_id, $user_id);
        $action = 'liked';
    }
    
    echo json_encode([
        'success' => true,
        'action' => $action,
        'likes_count' => getLikesCount($post_id)
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
