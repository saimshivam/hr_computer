<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

// Handle add course
$add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && !isset($_POST['edit_id'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $fees = floatval($_POST['fees']);
    $discount = floatval($_POST['discount']); // Now handled as percentage
    $duration = trim($_POST['duration']);

    if ($title && $description) {
        $stmt = $pdo->prepare('INSERT INTO courses (title, description, icon, fees, discount, duration) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $icon, $fees, $discount, $duration]);
    } else {
        $add_error = 'Title and description are required.';
    }
}

// Handle edit/update
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $title = trim($_POST['edit_title']);
    $description = trim($_POST['edit_description']);
    $icon = trim($_POST['edit_icon']);
    $fees = floatval($_POST['edit_fees']);
    $discount = floatval($_POST['edit_discount']); // Now handled as percentage
    $duration = trim($_POST['edit_duration']);

    if ($title && $description) {
        $pdo->prepare('UPDATE courses SET title = ?, description = ?, icon = ?, fees = ?, discount = ?, duration = ? WHERE id = ?')->execute([$title, $description, $icon, $fees, $discount, $duration, $id]);
    }
    header('Location: courses.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare('DELETE FROM courses WHERE id = ?')->execute([$id]);
    header('Location: courses.php');
    exit;
}

// Fetch all courses
$courses = $pdo->query('SELECT * FROM courses ORDER BY created_at DESC')->fetchAll();

// Fetch course for editing if edit_id is set
$edit_course_data = null;
if ($edit_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([$edit_id]);
    $edit_course_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Courses - HR Computer</title>
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
        <h2 class="mb-4">Manage Courses</h2>
        <form method="POST" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="title" class="form-control" placeholder="Course Title" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="description" class="form-control" placeholder="Description" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="icon" class="form-control" placeholder="Icon (FontAwesome, optional)">
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="fees" class="form-control" placeholder="Fees (e.g. 9999.00)" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="number" name="discount" class="form-control" placeholder="Discount (e.g. 10)" step="0.01" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="text" name="duration" class="form-control" placeholder="Duration (e.g. 3 months)">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
            <?php if ($add_error): ?>
                <div class="alert alert-danger mt-2"><?php echo htmlspecialchars($add_error); ?></div>
            <?php endif; ?>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Fees</th>
                        <th>Discount</th>
                        <th>Duration</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $i => $course): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <?php if ($edit_id === (int)$course['id']): ?>
                                <form method="POST">
                                    <input type="hidden" name="edit_id" value="<?php echo $course['id']; ?>">
                                    <td><input type="text" name="edit_title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required></td>
                                    <td><input type="text" name="edit_description" class="form-control" value="<?php echo htmlspecialchars($course['description']); ?>" required></td>
                                    <td><input type="text" name="edit_icon" class="form-control" value="<?php echo htmlspecialchars($course['icon']); ?>"></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="edit_fees" class="form-control" value="<?php echo htmlspecialchars($course['fees']); ?>" step="0.01" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="edit_discount" class="form-control" value="<?php echo htmlspecialchars($course['discount']); ?>" step="0.01" min="0" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td><input type="text" name="edit_duration" class="form-control" value="<?php echo htmlspecialchars($course['duration']); ?>"></td>
                                    <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                                    <td style="min-width:120px">
                                        <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fas fa-save"></i> Save</button>
                                        <a href="courses.php" class="btn btn-secondary btn-sm">Cancel</a>
                                    </td>
                                </form>
                            <?php else: ?>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td><?php echo htmlspecialchars($course['description']); ?></td>
                                <td><?php if ($course['icon']): ?><i class="<?php echo htmlspecialchars($course['icon']); ?>"></i> <?php echo htmlspecialchars($course['icon']); ?><?php endif; ?></td>
                                <td>₹<?php echo htmlspecialchars(number_format($course['fees'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($course['discount']); ?>%</td>
                                <td><?php echo htmlspecialchars($course['duration']); ?></td>
                                <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                                <td style="min-width:120px">
                                    <a href="?edit=<?php echo $course['id']; ?>" class="btn btn-info btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?delete=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this course?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 