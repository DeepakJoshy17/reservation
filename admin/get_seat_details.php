<?php
include 'db_connection.php';

if (isset($_GET['seat_id'])) {
    $seat_id = (int)$_GET['seat_id'];
    $stmt = $conn->prepare("SELECT * FROM Seats WHERE seat_id = ?");
    $stmt->bind_param("i", $seat_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
    $stmt->close();
}
?>
