<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stop_id = isset($_POST['stop_id']) ? intval($_POST['stop_id']) : null;
$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';

if ($stop_id && $field && $value !== '') {
    $query = "UPDATE Route_Stops SET $field = ? WHERE stop_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        if ($field == 'stop_order') {
            $stmt->bind_param("ii", $value, $stop_id);
        } else {
            $stmt->bind_param("si", $value, $stop_id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Prepare failed']);
    }
} else {
    echo json_encode(['error' => 'Invalid input']);
}

$conn->close();
?>


