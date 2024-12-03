<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stop_id = isset($_POST['stop_id']) ? intval($_POST['stop_id']) : null;

if ($stop_id) {
    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Delete from Route_Stop_Times
        $query = "DELETE FROM Route_Stop_Times WHERE stop_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $stop_id);
        $stmt->execute();
        $stmt->close();

        // Delete from Route_Stops
        $query = "DELETE FROM Route_Stops WHERE stop_id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("i", $stop_id);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid input']);
}

$conn->close();
?>
