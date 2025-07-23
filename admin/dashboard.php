<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

// Fetch summary counts
$total_admissions = $pdo->query('SELECT COUNT(*) FROM admission_forms')->fetchColumn();
$total_gallery = $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn();
$total_courses = $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$total_users = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$total_activities = $pdo->query('SELECT COUNT(*) FROM user_activities')->fetchColumn();
$total_notices = $pdo->query('SELECT COUNT(*) FROM notices')->fetchColumn();

// Fetch recent admissions (limit 5)
$stmt = $pdo->query("SELECT * FROM admission_forms ORDER BY created_at DESC LIMIT 5");
$recent_admissions = $stmt->fetchAll();

// Fetch recent contact messages (limit 5)
$contact_stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_contacts = $contact_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HR Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
            <div class="d-flex">
                <a href="download.php" class="btn btn-success me-2"><i class="fas fa-download"></i> Download CSV</a>
                <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>
    <div class="container py-4">
        <h2 class="mb-4">Welcome, Admin!</h2>
        <div class="row g-4 mb-4">
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_admissions; ?></h5>
                        <small>Admissions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-images fa-2x text-success mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_gallery; ?></h5>
                        <small>Gallery Images</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-book-open fa-2x text-warning mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_courses; ?></h5>
                        <small>Courses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_users; ?></h5>
                        <small>Users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-list fa-2x text-secondary mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_activities; ?></h5>
                        <small>User Activities</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-bullhorn fa-2x text-danger mb-2"></i>
                        <h5 class="card-title mb-0"><?php echo $total_notices; ?></h5>
                        <small>Notices</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <a href="gallery.php" class="btn btn-outline-success w-100"><i class="fas fa-images"></i> Manage Gallery</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="courses.php" class="btn btn-outline-warning w-100"><i class="fas fa-book-open"></i> Manage Courses</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="notices.php" class="btn btn-outline-danger w-100"><i class="fas fa-bullhorn"></i> Manage Notices</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="activities.php" class="btn btn-outline-secondary w-100"><i class="fas fa-list"></i> User Activities</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="dashboard.php" class="btn btn-outline-primary w-100"><i class="fas fa-file-alt"></i> Admissions</a>
            </div>
        </div>
        <h4 class="mb-3">Recent Admissions</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Applicant's Name</th>
                        <th>Father's/Husband's Name</th>
                        <th>Mother's/Husband's Name</th>
                        <th>Photo</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Course</th>
                        <th>Duration</th>
                        <th>Address</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Pincode</th>
                        <th>Contact No</th>
                        <th>Alternate Mobile</th>
                        <th>Matric (Board/Year/Total/Obtained/%/CGP)</th>
                        <th>Degree (Board/Year/Total/Obtained/%/CGP)</th>
                        <th>Other (Board/Year/Total/Obtained/%/CGP)</th>
                        <th>Place</th>
                        <th>Date</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_admissions) > 0): ?>
                        <?php foreach ($recent_admissions as $i => $row): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['mother_name']); ?></td>
                                <td><?php if ($row['photo']) { echo '<img src="../' . htmlspecialchars($row['photo']) . '" alt="Photo" style="width:50px;height:50px;object-fit:cover;">'; } ?></td>
                                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['course']); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['district']); ?></td>
                                <td><?php echo htmlspecialchars($row['state']); ?></td>
                                <td><?php echo htmlspecialchars($row['pincode']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['alt_mobile']); ?></td>
                                <td><?php echo htmlspecialchars($row['matric_board']) . ' / ' . htmlspecialchars($row['matric_year']) . ' / ' . htmlspecialchars($row['matric_total']) . ' / ' . htmlspecialchars($row['matric_obtained']) . ' / ' . htmlspecialchars($row['matric_percent']); ?></td>
                                <td><?php echo htmlspecialchars($row['degree_board']) . ' / ' . htmlspecialchars($row['degree_year']) . ' / ' . htmlspecialchars($row['degree_total']) . ' / ' . htmlspecialchars($row['degree_obtained']) . ' / ' . htmlspecialchars($row['degree_percent']); ?></td>
                                <td><?php echo htmlspecialchars($row['other_board']) . ' / ' . htmlspecialchars($row['other_year']) . ' / ' . htmlspecialchars($row['other_total']) . ' / ' . htmlspecialchars($row['other_obtained']) . ' / ' . htmlspecialchars($row['other_percent']); ?></td>
                                <td><?php echo htmlspecialchars($row['place']); ?></td>
                                <td><?php echo htmlspecialchars($row['form_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="21" class="text-center">No recent admissions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <h4 class="mb-3 mt-5">Recent Contact Messages</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_contacts) > 0): ?>
                        <?php foreach ($recent_contacts as $i => $row): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No contact messages found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 