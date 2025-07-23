<?php
require_once 'includes/db.php';
$courses = $pdo->query('SELECT * FROM courses ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - HR Computer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <section class="bg-light" style="min-height: 80vh; padding-top: 0; padding-bottom: 5px; display: flex; align-items: flex-start;">
        <div class="container">
            <h1 class="mb-4 text-center" style="margin-top:0;">Our Courses</h1>
            <div class="row g-4">
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <?php if ($course['icon']): ?>
                                        <i class="<?php echo htmlspecialchars($course['icon']); ?> fa-3x mb-3 text-primary"></i>
                                    <?php else: ?>
                                        <i class="fas fa-book-open fa-3x mb-3 text-primary"></i>
                                    <?php endif; ?>
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                                    <div class="d-flex justify-content-center align-items-baseline mb-2">
                                        <?php if ($course['discount'] > 0): ?>
                                            <span class="text-decoration-line-through text-muted me-2">₹<?php echo htmlspecialchars(number_format($course['fees'], 2)); ?></span>
                                            <span class="fw-bold text-success">₹<?php echo htmlspecialchars(number_format($course['fees'] - ($course['fees'] * $course['discount'] / 100), 2)); ?></span>
                                            <span class="badge bg-danger ms-2">-<?php echo htmlspecialchars(number_format($course['discount'], 0)); ?>%</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-primary">₹<?php echo htmlspecialchars(number_format($course['fees'], 2)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($course['duration']): ?>
                                        <p class="card-text"><i class="far fa-clock me-1"></i> <?php echo htmlspecialchars($course['duration']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">No courses available yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php require_once 'includes/footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 