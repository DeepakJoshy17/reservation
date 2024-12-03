<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $seat_ids = $data['seat_ids'];
    $seat_number = $data['seat_number'];
    $type = $data['type'];

    foreach ($seat_ids as $seat_id) {
        $stmt = $conn->prepare("UPDATE Seats SET seat_number = ?, type = ? WHERE seat_id = ?");
        $stmt->bind_param("ssi", $seat_number, $type, $seat_id);
        if (!$stmt->execute()) {
            echo "Error updating seat ID $seat_id.";
        }
        $stmt->close();
    }
    echo "Seats updated successfully.";
}
?>
