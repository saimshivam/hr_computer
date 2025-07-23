<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

// Handle image upload
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && !isset($_POST['edit_id'])) {
    $file = $_FILES['image'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid('gallery_', true) . '.' . $ext;
            $target = '../assets/images/gallery/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $caption = trim($_POST['caption'] ?? '');
                $stmt = $pdo->prepare('INSERT INTO gallery_images (filename, caption) VALUES (?, ?)');
                $stmt->execute([$filename, $caption]);
            } else {
                $upload_error = 'Failed to move uploaded file.';
            }
        } else {
            $upload_error = 'Invalid file type.';
        }
    } else {
        $upload_error = 'File upload error.';
    }
}

// Handle edit/update
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $caption = trim($_POST['edit_caption'] ?? '');
    $filename = null;
    if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['edit_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid('gallery_', true) . '.' . $ext;
            $target = '../assets/images/gallery/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $target)) {
                // Delete old image
                $stmt = $pdo->prepare('SELECT filename FROM gallery_images WHERE id = ?');
                $stmt->execute([$id]);
                $img = $stmt->fetch();
                if ($img) {
                    @unlink('../assets/images/gallery/' . $img['filename']);
                }
                $pdo->prepare('UPDATE gallery_images SET filename = ?, caption = ? WHERE id = ?')->execute([$filename, $caption, $id]);
            }
        }
    } else {
        $pdo->prepare('UPDATE gallery_images SET caption = ? WHERE id = ?')->execute([$caption, $id]);
    }
    header('Location: gallery.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare('SELECT filename FROM gallery_images WHERE id = ?');
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    if ($img) {
        @unlink('../assets/images/gallery/' . $img['filename']);
        $pdo->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$id]);
    }
    header('Location: gallery.php');
    exit;
}

// Fetch all images
$images = $pdo->query('SELECT * FROM gallery_images ORDER BY uploaded_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gallery - HR Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light me-2"><i class="fas fa-arrow-left"></i> Back</a>
                <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <h2 class="mb-4">Manage Gallery</h2>
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="file" name="image" class="form-control" required accept="image/*">
                </div>
                <div class="col-md-4">
                    <input type="text" name="caption" class="form-control" placeholder="Caption (optional)">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </div>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger mt-2"><?php echo htmlspecialchars($upload_error); ?></div>
            <?php endif; ?>
        </form>
        <div class="row g-3">
            <?php foreach ($images as $img): ?>
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="card">
                        <img src="../assets/images/gallery/<?php echo htmlspecialchars($img['filename']); ?>" class="card-img-top" alt="Gallery Image">
                        <div class="card-body p-2">
                            <?php if ($edit_id === (int)$img['id']): ?>
                                <form method="POST" enctype="multipart/form-data" class="mb-2">
                                    <input type="hidden" name="edit_id" value="<?php echo $img['id']; ?>">
                                    <input type="text" name="edit_caption" class="form-control mb-2" value="<?php echo htmlspecialchars($img['caption']); ?>" placeholder="Caption">
                                    <input type="file" name="edit_image" class="form-control mb-2" accept="image/*">
                                    <button type="submit" class="btn btn-success btn-sm w-100 mb-1"><i class="fas fa-save"></i> Save</button>
                                    <a href="gallery.php" class="btn btn-secondary btn-sm w-100">Cancel</a>
                                </form>
                            <?php else: ?>
                                <p class="card-text small mb-2"><?php echo htmlspecialchars($img['caption']); ?></p>
                                <a href="?edit=<?php echo $img['id']; ?>" class="btn btn-sm btn-info w-100 mb-1"><i class="fas fa-edit"></i> Edit</a>
                                <a href="?delete=<?php echo $img['id']; ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Delete this image?');"><i class="fas fa-trash"></i> Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 