<?php
session_start(); // Start the session

// Include database connection
include 'db_connection.php';

// Initialize variables for user data
$name = '';
$email = '';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Fetch user details from the database
    $user_id = $_SESSION['user_id'];
    $query = "SELECT name, email FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    $stmt->fetch();
    $stmt->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Optional: Capture user_id if available

    // Insert enquiry into the database
    $query = "INSERT INTO Enquiries (user_id, name, email, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $name, $email, $message);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Your enquiry has been submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }
}
?>
