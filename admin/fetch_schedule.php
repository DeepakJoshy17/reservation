<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo "You must be logged in to perform this action.";
    exit;
}

$schedule_id = $_POST['schedule_id'] ?? null;

if ($schedule_id) {
    $query = "SELECT * FROM Schedules WHERE schedule_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
        echo json_encode($schedule);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
