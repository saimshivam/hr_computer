<?php
$host = 'localhost';
$dbname = 'hr_computer';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Function to log user activities
function log_user_activity($pdo, $user_id, $activity_description) {
    try {
        $stmt = $pdo->prepare('INSERT INTO user_activities (user_id, activity) VALUES (?, ?)');
        $stmt->execute([$user_id, $activity_description]);
    } catch (PDOException $e) {
        // Log the error for debugging, but don't stop the application
        error_log("Error logging user activity: " . $e->getMessage());
    }
}
?> 