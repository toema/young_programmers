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

if(!isset($_POST['post_id']) || !isset($_POST['content'])) {
    die(json_encode([
        'success' => false,
        'message' => 'جميع الحقول مطلوبة'
    ]));
}

try {
    $post_id = (int)$_POST['post_id'];
    $content = trim($_POST['content']);
    
    if(empty($content)) {
        throw new Exception("محتوى التعليق مطلوب");
    }
    
    if(strlen($content) < 2) {
        throw new Exception("التعليق قصير جداً");
    }
    
    if(strlen($content) > 500) {
        throw new Exception("التعليق طويل جداً");
    }
    
    $comment_data = [
        'post_id' => $post_id,
        'user_id' => $_SESSION['user_id'],
        'content' => $content
    ];
    
    if(addComment($comment_data)) {
        echo json_encode([
            'success' => true,
            'message' => 'تم إضافة التعليق بنجاح',
            'comments_count' => getCommentsCount($post_id)
        ]);
    } else {
        throw new Exception("حدث خطأ أثناء إضافة التعليق");
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
