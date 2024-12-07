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

if(!isset($_GET['post_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'معرف المنشور مطلوب'
    ]));
}

try {
    $post_id = (int)$_GET['post_id'];
    $comments = getPostComments($post_id);
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
