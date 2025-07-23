<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=admission_forms.csv');

$output = fopen('php://output', 'w');
fputcsv($output, [
    "Applicant's Name", "Father's/Husband's Name", "Mother's/Husband's Name", "Photo", "DOB", "Gender", "Course", "Duration", "Address", "District", "State", "Pincode", "Contact No", "Alternate Mobile",
    "Matric Board", "Matric Year", "Matric Total", "Matric Obtained", "Matric %/CGP",
    "Degree Board", "Degree Year", "Degree Total", "Degree Obtained", "Degree %/CGP",
    "Other Board", "Other Year", "Other Total", "Other Obtained", "Other %/CGP",
    "Place", "Date", "Submitted At"
]);

$stmt = $pdo->query("SELECT applicant_name, father_name, mother_name, photo, dob, gender, course, duration, address, district, state, pincode, contact_no, alt_mobile, matric_board, matric_year, matric_total, matric_obtained, matric_percent, degree_board, degree_year, degree_total, degree_obtained, degree_percent, other_board, other_year, other_total, other_obtained, other_percent, place, form_date, created_at FROM admission_forms ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}
fclose($output);
exit; 