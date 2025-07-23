<?php
session_start();

require_once 'includes/db.php'; // Ensure DB is connected first

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php?msg=login_required');
    exit;
}

require_once 'includes/header.php';

$admission_message = '';

// Fetch courses for the dropdown
$available_courses = $pdo->query('SELECT title FROM courses ORDER BY title ASC')->fetchAll(PDO::FETCH_COLUMN);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize all fields
    $applicant_name = filter_input(INPUT_POST, 'applicant_name', FILTER_SANITIZE_STRING);
    $father_name = filter_input(INPUT_POST, 'father_name', FILTER_SANITIZE_STRING);
    $mother_name = filter_input(INPUT_POST, 'mother_name', FILTER_SANITIZE_STRING);
    $dob = filter_input(INPUT_POST, 'dob', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $course = filter_input(INPUT_POST, 'course', FILTER_SANITIZE_STRING);
    $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $district = filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
    $pincode = filter_input(INPUT_POST, 'pincode', FILTER_SANITIZE_STRING);
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
    $alt_mobile = filter_input(INPUT_POST, 'alt_mobile', FILTER_SANITIZE_STRING);
    $matric_board = filter_input(INPUT_POST, 'matric_board', FILTER_SANITIZE_STRING);
    $matric_year = filter_input(INPUT_POST, 'matric_year', FILTER_SANITIZE_STRING);
    $matric_total = filter_input(INPUT_POST, 'matric_total', FILTER_SANITIZE_STRING);
    $matric_obtained = filter_input(INPUT_POST, 'matric_obtained', FILTER_SANITIZE_STRING);
    $matric_percent = filter_input(INPUT_POST, 'matric_percent', FILTER_SANITIZE_STRING);
    $degree_board = filter_input(INPUT_POST, 'degree_board', FILTER_SANITIZE_STRING);
    $degree_year = filter_input(INPUT_POST, 'degree_year', FILTER_SANITIZE_STRING);
    $degree_total = filter_input(INPUT_POST, 'degree_total', FILTER_SANITIZE_STRING);
    $degree_obtained = filter_input(INPUT_POST, 'degree_obtained', FILTER_SANITIZE_STRING);
    $degree_percent = filter_input(INPUT_POST, 'degree_percent', FILTER_SANITIZE_STRING);
    $other_board = filter_input(INPUT_POST, 'other_board', FILTER_SANITIZE_STRING);
    $other_year = filter_input(INPUT_POST, 'other_year', FILTER_SANITIZE_STRING);
    $other_total = filter_input(INPUT_POST, 'other_total', FILTER_SANITIZE_STRING);
    $other_obtained = filter_input(INPUT_POST, 'other_obtained', FILTER_SANITIZE_STRING);
    $other_percent = filter_input(INPUT_POST, 'other_percent', FILTER_SANITIZE_STRING);
    $place = filter_input(INPUT_POST, 'place', FILTER_SANITIZE_STRING);
    $form_date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);

    // Handle photo upload
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $new_name = 'profile_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = 'assets/images/profile_pictures/' . $new_name;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo_path = $target;
        }
    }

    // Validation (basic)
    $errors = [];
    if (empty($applicant_name)) $errors[] = "Applicant's Name is required.";
    if (empty($father_name)) $errors[] = "Father's/Husband's Name is required.";
    if (empty($mother_name)) $errors[] = "Mother's/Husband's Name is required.";
    if (empty($dob)) $errors[] = "Date of Birth is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($course)) $errors[] = "Course selection is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($contact_no)) $errors[] = "Contact No is required.";
    if (!$photo_path) $errors[] = "Photo upload failed.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO admission_forms (
                applicant_name, father_name, mother_name, photo, dob, gender, course, duration, address, district, state, pincode, contact_no, alt_mobile,
                matric_board, matric_year, matric_total, matric_obtained, matric_percent,
                degree_board, degree_year, degree_total, degree_obtained, degree_percent,
                other_board, other_year, other_total, other_obtained, other_percent,
                place, form_date
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $applicant_name, $father_name, $mother_name, $photo_path, $dob, $gender, $course, $duration, $address, $district, $state, $pincode, $contact_no, $alt_mobile,
                $matric_board, $matric_year, $matric_total, $matric_obtained, $matric_percent,
                $degree_board, $degree_year, $degree_total, $degree_obtained, $degree_percent,
                $other_board, $other_year, $other_total, $other_obtained, $other_percent,
                $place, $form_date
            ]);
            $admission_message = '<div class="alert alert-success">Your admission form has been submitted successfully!</div>';
            if (isset($_SESSION['user_id'])) {
                log_user_activity($pdo, $_SESSION['user_id'], 'Submitted admission form for ' . htmlspecialchars($course));
            }
        } catch (PDOException $e) {
            $admission_message = '<div class="alert alert-danger">An error occurred during submission: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $admission_message = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $admission_message .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $admission_message .= '</ul></div>';
    }
}
?>

<!-- Admission Form Section -->
<section class="admission-form-section py-5" style="padding-top:0;">
    <div class="container">
        <h1 class="mb-4 text-center" style="margin-top:0;">Admission Form</h1>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php echo $admission_message; ?>
                        
                        <form method="POST" action="" class="admission-form" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-12">
                                    <h4 class="mb-3">Personal Information</h4>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group mb-2">
                                        <label for="applicant_name" class="form-label">Applicant's Name</label>
                                        <input type="text" class="form-control" id="applicant_name" name="applicant_name" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="father_name" class="form-label">Father's/Husband's Name</label>
                                        <input type="text" class="form-control" id="father_name" name="father_name" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="mother_name" class="form-label">Mother's/Husband's Name</label>
                                        <input type="text" class="form-control" id="mother_name" name="mother_name" required>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex flex-column align-items-center">
                                    <label class="form-label">Photo</label>
                                    <input type="file" class="form-control mb-2" name="photo" accept="image/*" required>
                                    <small class="text-muted">Upload recent passport size photo</small>
                                </div>
                                <div class="col-md-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="course" class="form-label">Course</label>
                                    <select class="form-select" id="course" name="course" required>
                                        <option value="">Choose...</option>
                                        <?php foreach ($available_courses as $c): ?>
                                            <option value="<?php echo htmlspecialchars($c); ?>" data-duration="<?php echo htmlspecialchars($pdo->query('SELECT duration FROM courses WHERE title = "' . $c . '"')->fetchColumn()); ?>">
                                                <?php echo htmlspecialchars($c); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="duration" class="form-label">Duration of Course</label>
                                    <input type="text" class="form-control" id="duration" name="duration" readonly>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Postal Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="district" class="form-label">District</label>
                                    <input type="text" class="form-control" id="district" name="district">
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state">
                                </div>
                                <div class="col-md-4">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text" class="form-control" id="pincode" name="pincode">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_no" class="form-label">Contact No</label>
                                    <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="alt_mobile" class="form-label">Alternate Mobile Number</label>
                                    <input type="text" class="form-control" id="alt_mobile" name="alt_mobile">
                                </div>
                                <div class="col-12 mt-4">
                                    <h4 class="mb-3">Details of Qualifying Examination</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Degree or Qualification</th>
                                                    <th>Board/University</th>
                                                    <th>Passing Year</th>
                                                    <th>Total Marks</th>
                                                    <th>Obtained Marks</th>
                                                    <th>Percentage/CGP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Matriculation</td>
                                                    <td><input type="text" class="form-control" name="matric_board"></td>
                                                    <td><input type="text" class="form-control" name="matric_year"></td>
                                                    <td><input type="text" class="form-control" name="matric_total"></td>
                                                    <td><input type="text" class="form-control" name="matric_obtained"></td>
                                                    <td><input type="text" class="form-control" name="matric_percent"></td>
                                                </tr>
                                                <tr>
                                                    <td>Degree</td>
                                                    <td><input type="text" class="form-control" name="degree_board"></td>
                                                    <td><input type="text" class="form-control" name="degree_year"></td>
                                                    <td><input type="text" class="form-control" name="degree_total"></td>
                                                    <td><input type="text" class="form-control" name="degree_obtained"></td>
                                                    <td><input type="text" class="form-control" name="degree_percent"></td>
                                                </tr>
                                                <tr>
                                                    <td>Other</td>
                                                    <td><input type="text" class="form-control" name="other_board"></td>
                                                    <td><input type="text" class="form-control" name="other_year"></td>
                                                    <td><input type="text" class="form-control" name="other_total"></td>
                                                    <td><input type="text" class="form-control" name="other_obtained"></td>
                                                    <td><input type="text" class="form-control" name="other_percent"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <h5>Declaration</h5>
                                    <p>I certify that the information I have written on the application form and the documents I have submitted to be true and accurate.</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="place" class="form-label">Place</label>
                                            <input type="text" class="form-control" id="place" name="place">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date" name="date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                                </div>
                                <div class="col-12 mt-3">
                                    <small class="text-muted">All candidates are informed: Attach the following certificate (Xerox copy) with their form: 1. Aadhar Card, 2. All qualified certificates, 3. Two Photographs</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseSelect = document.getElementById('course');
    var durationInput = document.getElementById('duration');
    courseSelect.addEventListener('change', function() {
        var selected = courseSelect.options[courseSelect.selectedIndex];
        var duration = selected.getAttribute('data-duration') || '';
        durationInput.value = duration;
    });
});
</script>

<?php
require_once 'includes/footer.php';
?> 