<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; // Database connection

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch latest notice
$latest_notice = null;
try {
    $stmt_notice = $pdo->query('SELECT title, content FROM notices ORDER BY created_at DESC LIMIT 1');
    $latest_notice = $stmt_notice->fetch();
} catch (PDOException $e) {
    // Handle or log error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Computer - Educational Institute</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/images/logo1.png" alt="HR Computer" height="45" class="me-2 rounded">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                $pages = [
                    'index.php' => ['Home', 'fa-home'],
                    'about.php' => ['About', 'fa-info-circle'],
                    'gallery.php' => ['Gallery', 'fa-images'],
                    'courses.php' => ['Courses', 'fa-book-open'],
                    'admission.php' => ['Admission Form', 'fa-user-graduate'],
                    'payment.php' => ['Payment', 'fa-money-bill'],
                    'check_result.php' => ['Check Result', 'fa-file-alt'],
                    'contact.php' => ['Contact', 'fa-envelope'],
                ];

                foreach ($pages as $file => $info) {
                    $active = $current_page === $file ? 'active' : '';
                    echo "<li class='nav-item'>
                            <a class='nav-link $active' href='$file'>
                                <i class='fas {$info[1]} me-1'></i>{$info[0]}
                            </a>
                          </li>";
                }
                ?>
            </ul>

            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'signup.php' ? 'active' : ''; ?>" href="signup.php">
                            <i class="fas fa-user-plus me-1"></i> Sign Up
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Admin
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">
                            <i class="fas fa-lock me-1"></i> Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Notice Banner -->
<?php if ($latest_notice): ?>
    <div class="alert alert-info alert-dismissible fade show text-center mb-0" role="alert" style="margin-top: 58px; border-radius: 0;">
        <strong><?php echo htmlspecialchars($latest_notice['title']); ?>:</strong> <?php echo htmlspecialchars($latest_notice['content']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Main Content Padding -->
<div class="main-content" style="padding-top: <?php echo $latest_notice ? '110px' : '76px'; ?>;">
