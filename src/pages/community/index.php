<?php
$page_title = "المجتمع";
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

// التحقق من تسجيل الدخول
if(!isLoggedIn()) {
    setFlashMessage('error', 'يجب تسجيل الدخول أولاً');
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

// جلب المنشورات
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$posts = getAllPosts($type, $sort);

// تضمين الهيدر
require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <!-- رسائل النجاح/الخطأ -->
    <?php include '../../includes/flash-messages.php'; ?>

    <!-- قسم إضافة منشور -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-edit"></i> شارك مع المجتمع
            </h5>
            <form id="addPostForm">
                <div class="mb-3">
                    <label class="form-label">عنوان المنشور</label>
                    <input type="text" name="title" class="form-control" required
                           placeholder="اكتب عنواناً مناسباً لمنشورك">
                </div>
                <div class="mb-3">
                    <label class="form-label">المحتوى</label>
                    <textarea name="content" class="form-control" rows="4" required
                              placeholder="اكتب محتوى منشورك هنا..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">نوع المنشور</label>
                    <select name="type" class="form-select" required>
                        <option value="">اختر نوع المنشور</option>
                        <option value="question">سؤال</option>
                        <option value="share">مشاركة</option>
                        <option value="tip">نصيحة</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> نشر
                </button>
            </form>
        </div>
    </div>

    <!-- قسم تصفية المنشورات -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="filter-buttons mb-2 mb-md-0">
                    <a href="?type=all" class="btn btn-outline-primary me-2 <?php echo $type == 'all' ? 'active' : ''; ?>">
                        <i class="fas fa-globe"></i> الكل
                    </a>
                    <a href="?type=question" class="btn btn-outline-primary me-2 <?php echo $type == 'question' ? 'active' : ''; ?>">
                        <i class="fas fa-question-circle"></i> الأسئلة
                    </a>
                    <a href="?type=share" class="btn btn-outline-primary me-2 <?php echo $type == 'share' ? 'active' : ''; ?>">
                        <i class="fas fa-share-alt"></i> المشاركات
                    </a>
                    <a href="?type=tip" class="btn btn-outline-primary <?php echo $type == 'tip' ? 'active' : ''; ?>">
                        <i class="fas fa-lightbulb"></i> النصائح
                    </a>
                </div>
                <div class="sort-select">
                    <select class="form-select" onchange="window.location.href='?type=<?php echo $type; ?>&sort=' + this.value">
                        <option value="latest" <?php echo $sort == 'latest' ? 'selected' : ''; ?>>الأحدث</option>
                        <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>الأكثر تفاعلاً</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم عرض المنشورات -->
    <div id="posts-container">
        <?php if(empty($posts)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-lg mb-2"></i>
                <p class="mb-0">لا توجد منشورات حالياً</p>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <div class="card shadow-sm mb-3 post-card">
                    <div class="card-body">
                        <!-- رأس المنشور -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo getUserAvatar($post['user_id']); ?>" 
                                     class="rounded-circle me-2" 
                                     width="40" height="40" 
                                     alt="صورة المستخدم">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($post['username']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo timeAgo($post['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                            <span class="badge bg-primary">
                                <i class="<?php echo getPostTypeIcon($post['type']); ?>"></i>
                                <?php echo getPostTypeName($post['type']); ?>
                            </span>
                        </div>

                        <!-- محتوى المنشور -->
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

                        <!-- أزرار التفاعل -->
                        <div class="post-actions">
                            <button class="btn btn-sm btn-outline-primary me-2 <?php echo hasUserLiked($post['id'], $_SESSION['user_id']) ? 'active' : ''; ?>" 
                                    onclick="likePost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-thumbs-up"></i>
                                <span id="likes-count-<?php echo $post['id']; ?>">
                                    <?php echo $post['likes_count']; ?>
                                </span>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary me-2" 
                                    onclick="toggleComments(<?php echo $post['id']; ?>)">
                                <i class="fas fa-comment"></i>
                                <span id="comments-count-<?php echo $post['id']; ?>">
                                    <?php echo $post['comments_count']; ?>
                                </span>
                            </button>
                            <?php if($post['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deletePost(<?php echo $post['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- قسم التعليقات -->
                        <div class="comments-section mt-3" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <hr>
                            <div class="comments-container" id="comments-container-<?php echo $post['id']; ?>">
                                <!-- سيتم تحميل التعليقات هنا -->
                            </div>
                            <form class="add-comment-form mt-2" onsubmit="addComment(event, <?php echo $post['id']; ?>)">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="أضف تعليقاً...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript -->
<script>
// إضافة منشور
document.getElementById('addPostForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري النشر...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('ajax/add-post.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إضافة المنشور');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة المنشور');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// الإعجاب بمنشور
function likePost(postId) {
    fetch('ajax/like-post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const btn = document.querySelector(`button[onclick="likePost(${postId})"]`);
            const count = document.getElementById('likes-count-' + postId);
            count.textContent = data.likes_count;
            
            if(data.action === 'liked') {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    });
}

// عرض/إخفاء التعليقات
function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    
    if(commentsSection.style.display === 'none') {
        commentsSection.style.display = 'block';
        loadComments(postId);
    } else {
        commentsSection.style.display = 'none';
    }
}

// تحميل التعليقات
function loadComments(postId) {
    fetch('ajax/get-comments.php?post_id=' + postId)
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('comments-container-' + postId);
        if(data.comments.length === 0) {
            container.innerHTML = '<div class="text-muted text-center">لا توجد تعليقات</div>';
        } else {
            container.innerHTML = data.comments.map(comment => `
                <div class="comment mb-2">
                    <div class="d-flex align-items-center mb-1">
                        <img src="${comment.avatar || '../../assets/images/default-avatar.png'}" 
                             class="rounded-circle me-2" 
                             width="24" height="24">
                        <small class="fw-bold">${comment.username}</small>
                        <small class="text-muted mx-2">•</small>
                        <small class="text-muted">${comment.created_at}</small>
                    </div>
                    <div class="comment-content">
                        ${comment.content}
                    </div>
                </div>
            `).join('');
        }
    });
}

// إضافة تعليق
function addComment(event, postId) {
    event.preventDefault();
    const form = event.target;
    const input = form.querySelector('input');
    const content = input.value.trim();
    
    if(!content) return;
    
    fetch('ajax/add-comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            loadComments(postId);
            input.value = '';
            const count = document.getElementById('comments-count-' + postId);
            count.textContent = parseInt(count.textContent) + 1;
        } else {
            alert(data.message || 'حدث خطأ أثناء إضافة التعليق');
        }
    });
}

// حذف منشور
function deletePost(postId) {
    if(confirm('هل أنت متأكد من حذف هذا المنشور؟')) {
        fetch('ajax/delete-post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء حذف المنشور');
            }
        });
    }
}
</script>
<style>
.post-card {
    transition: transform 0.2s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.post-actions {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.post-actions button {
    transition: all 0.3s ease;
}

.post-actions button:hover {
    transform: scale(1.05);
}

.filter-buttons .btn {
    transition: all 0.3s ease;
}

.filter-buttons .btn:hover {
    transform: translateY(-1px);
}

.filter-buttons .btn.active {
    background-color: var(--bs-primary);
    color: white;
}

@media (max-width: 768px) {
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .filter-buttons .btn {
        flex: 1;
        min-width: 120px;
    }
}
</style>

<?php require_once '../../includes/footer.php'; ?>
