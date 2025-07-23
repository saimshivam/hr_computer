<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$message = '';

// Handle Add/Update Notice
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($title) || empty($content)) {
        $message = '<div class="alert alert-danger">Title and content are required.</div>';
    } else {
        try {
            if ($id > 0) {
                // Update existing notice
                $stmt = $pdo->prepare('UPDATE notices SET title = ?, content = ? WHERE id = ?');
                $stmt->execute([$title, $content, $id]);
                $message = '<div class="alert alert-success">Notice updated successfully!</div>';
            } else {
                // Add new notice
                $stmt = $pdo->prepare('INSERT INTO notices (title, content) VALUES (?, ?)');
                $stmt->execute([$title, $content]);
                $message = '<div class="alert alert-success">Notice added successfully!</div>';
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Handle Delete Notice
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $pdo->prepare('DELETE FROM notices WHERE id = ?')->execute([$id]);
        $message = '<div class="alert alert-success">Notice deleted successfully!</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Fetch notices for display
$notices = $pdo->query('SELECT * FROM notices ORDER BY created_at DESC')->fetchAll();

// Fetch notice for editing if edit_id is set
$edit_notice = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $pdo->prepare('SELECT * FROM notices WHERE id = ?');
    $stmt->execute([$edit_id]);
    $edit_notice = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices - HR Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light me-2"><i class="fas fa-arrow-left"></i> Back</a>
                <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div id="notice-alert" class="alert alert-warning alert-dismissible fade show mb-0" role="alert" style="margin-top: 56px;">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div id="main-content">
        <div class="container py-4">
            <h2 class="mb-4">Manage Notices</h2>
            
            <form method="POST" class="mb-4">
                <input type="hidden" name="id" value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['id']) : ''; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Notice Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $edit_notice ? htmlspecialchars($edit_notice['title']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Notice Content</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required><?php echo $edit_notice ? htmlspecialchars($edit_notice['content']) : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?php echo $edit_notice ? 'save' : 'plus'; ?>"></i> <?php echo $edit_notice ? 'Update Notice' : 'Add New Notice'; ?></button>
                <?php if ($edit_notice): ?>
                    <a href="notices.php" class="btn btn-secondary ms-2">Cancel Edit</a>
                <?php endif; ?>
            </form>

            <h4 class="mb-3 mt-5">Existing Notices</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($notices) > 0): ?>
                            <?php foreach ($notices as $i => $notice): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td><?php echo htmlspecialchars($notice['title']); ?></td>
                                    <td><?php echo htmlspecialchars($notice['content']); ?></td>
                                    <td><?php echo htmlspecialchars($notice['created_at']); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $notice['id']; ?>" class="btn btn-info btn-sm me-2"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?delete=<?php echo $notice['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this notice?');"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No notices found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector('.navbar.fixed-top');
        const notice = document.getElementById('notice-alert');
        const mainContent = document.getElementById('main-content');

        function updatePadding() {
            const navbarHeight = navbar ? navbar.offsetHeight : 0;
            const noticeHeight = (notice && notice.offsetParent !== null) ? notice.offsetHeight : 0;
            mainContent.style.paddingTop = (navbarHeight + noticeHeight) + "px";
        }

        // Initial padding
        updatePadding();

        // Listen for Bootstrap alert close event
        if (notice) {
            notice.addEventListener('closed.bs.alert', function () {
                updatePadding();
            });
            notice.addEventListener('shown.bs.alert', function () {
                updatePadding();
            });
        }

        // In case of window resize
        window.addEventListener('resize', updatePadding);
    });
    </script>
</body>
</html> 