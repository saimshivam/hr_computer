<?php
session_start();

require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    if (!$user_data) {
        // User not found, destroy session and redirect
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    $message = '<div class="alert alert-danger">Error fetching user data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $user_data = ['name' => '', 'email' => '', 'profile_picture' => 'default.jpg'];
}

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
        $message = '<div class="alert alert-danger">Only JPG, PNG, and GIF images are allowed.</div>';
    } elseif ($_FILES['profile_picture']['size'] > $max_size) {
        $message = '<div class="alert alert-danger">Image size should not exceed 5MB.</div>';
    } else {
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $upload_path = 'assets/images/profile_pictures/' . $new_filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            // Delete old profile picture if it's not the default
            if ($user_data['profile_picture'] !== 'default.jpg' && file_exists('assets/images/profile_pictures/' . $user_data['profile_picture'])) {
                unlink('assets/images/profile_pictures/' . $user_data['profile_picture']);
            }

            // Update database
            try {
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->execute([$new_filename, $user_id]);
                $user_data['profile_picture'] = $new_filename;
                $message = '<div class="alert alert-success">Profile picture updated successfully!</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger">Error updating profile picture: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Error uploading profile picture.</div>';
        }
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['profile_picture'])) {
    $new_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $new_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($new_name)) {
        $errors[] = "Name cannot be empty.";
    }
    if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    if (empty($errors)) {
        try {
            // Check if new email already exists for another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$new_email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "This email is already taken by another user.";
            } else {
                $sql = "UPDATE users SET name = ?, email = ?";
                $params = [$new_name, $new_email];

                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql .= ", password = ?";
                    $params[] = $hashed_password;
                }

                $sql .= " WHERE id = ?";
                $params[] = $user_id;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                // Update session variables
                $_SESSION['user_name'] = $new_name;
                $_SESSION['user_email'] = $new_email;
                log_user_activity($pdo, $user_id, 'User profile updated');
                $message = '<div class="alert alert-success">Profile updated successfully!</div>';
                
                // Re-fetch user data to display updated info
                $stmt = $pdo->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user_data = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error_item) {
            $message .= '<li>' . htmlspecialchars($error_item) . '</li>';
        }
        $message .= '</ul></div>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HR Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .profile-picture-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .profile-picture-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #4b6cb7;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .profile-picture-upload:hover {
            background: #182848;
        }
        .profile-picture-upload input {
            display: none;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .form-control:focus {
            border-color: #4b6cb7;
            box-shadow: 0 0 0 0.2rem rgba(75, 108, 183, 0.25);
        }
        .btn-primary {
            background: #4b6cb7;
            border-color: #4b6cb7;
        }
        .btn-primary:hover {
            background: #182848;
            border-color: #182848;
        }
        .btn-danger {
            background: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div class="profile-picture-container">
                        <img src="assets/images/profile_pictures/<?php echo htmlspecialchars($user_data['profile_picture']); ?>" 
                             alt="Profile Picture" 
                             class="profile-picture"
                             onerror="this.src='assets/images/profile_pictures/default.jpg'">
                        <label class="profile-picture-upload" title="Change Profile Picture">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="profile_picture" form="profile-picture-form" accept="image/*">
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($user_data['name']); ?></h1>
                    <p class="lead mb-0"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user_data['email']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <section class="profile-section py-5">
        <div class="container">
            <?php echo $message; ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <form method="POST" action="profile.php" id="profile-form">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="form-text text-muted">Min 6 characters if changing</small>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Profile</button>
                                    <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form id="profile-picture-form" method="POST" action="profile.php" enctype="multipart/form-data" style="display: none;"></form>

    <?php require_once 'includes/footer.php'; ?>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.profile-picture-upload input').addEventListener('change', function() {
            document.getElementById('profile-picture-form').submit();
        });
    </script>
</body>
</html> 