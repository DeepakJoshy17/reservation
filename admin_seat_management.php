<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';

// Fetch boats for selection
$boats = $conn->query("SELECT * FROM Boats");

// Handle add, edit, delete seat operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_seat'])) {
        // Add seat
        $boat_id = $_POST['boat_id'];
        $seat_number = $_POST['seat_number'];
        $type = $_POST['type'];

        // Check if the number of seats exceeds capacity
        $result = $conn->query("SELECT COUNT(*) as seat_count FROM Seats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $seat_count = $row['seat_count'];

        $result = $conn->query("SELECT capacity FROM Boats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $capacity = $row['capacity'];

        if ($seat_count < $capacity) {
            $stmt = $conn->prepare("INSERT INTO Seats (boat_id, seat_number, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $boat_id, $seat_number, $type);
            $stmt->execute();
            $stmt->close();
        } else {
            echo '<div class="alert alert-danger">Cannot add more seats. Boat is at full capacity.</div>';
        }
    } elseif (isset($_POST['edit_seat'])) {
        // Edit seat
        $seat_id = $_POST['seat_id'];
        $seat_number = $_POST['seat_number'];
        $type = $_POST['type'];

        $stmt = $conn->prepare("UPDATE Seats SET seat_number = ?, type = ? WHERE seat_id = ?");
        $stmt->bind_param("ssi", $seat_number, $type, $seat_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_seat'])) {
        // Delete seat
        $seat_id = $_POST['seat_id'];

        $stmt = $conn->prepare("DELETE FROM Seats WHERE seat_id = ?");
        $stmt->bind_param("i", $seat_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['add_max_capacity'])) {
        // Add seats to maximum capacity
        $boat_id = $_POST['boat_id'];

        $result = $conn->query("SELECT COUNT(*) as seat_count FROM Seats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $seat_count = $row['seat_count'];

        $result = $conn->query("SELECT capacity FROM Boats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $capacity = $row['capacity'];

        if ($seat_count < $capacity) {
            $seats_to_add = $capacity - $seat_count;
            $stmt = $conn->prepare("INSERT INTO Seats (boat_id, seat_number, type) VALUES (?, ?, 'Regular')");

            for ($i = 1; $i <= $seats_to_add; $i++) {
                $seat_number = ($seat_count + $i);
                $stmt->bind_param("is", $boat_id, $seat_number);
                $stmt->execute();
            }

            $stmt->close();
        } else {
            echo '<div class="alert alert-danger">Boat is already at full capacity.</div>';
        }
    } elseif (isset($_POST['add_custom_seats'])) {
        // Add custom number of seats
        $boat_id = $_POST['boat_id'];
        $seat_number_prefix = $_POST['seat_number_prefix'];
        $seat_number_start = $_POST['seat_number_start'];
        $num_seats = $_POST['num_seats'];
        $type = $_POST['type'];

        $result = $conn->query("SELECT capacity FROM Boats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $capacity = $row['capacity'];

        $result = $conn->query("SELECT COUNT(*) as seat_count FROM Seats WHERE boat_id = $boat_id");
        $row = $result->fetch_assoc();
        $seat_count = $row['seat_count'];

        if (($seat_count + $num_seats) <= $capacity) {
            $stmt = $conn->prepare("INSERT INTO Seats (boat_id, seat_number, type) VALUES (?, ?, ?)");

            for ($i = 0; $i < $num_seats; $i++) {
                $seat_number = $seat_number_prefix . ($seat_number_start + $i);
                $stmt->bind_param("iss", $boat_id, $seat_number, $type);
                $stmt->execute();
            }

            $stmt->close();
        } else {
            echo '<div class="alert alert-danger">Adding these seats exceeds the boat capacity.</div>';
        }
    } elseif (isset($_POST['edit_group_seats'])) {
        // Edit group of seats
        $seat_ids = $_POST['seat_ids'];
        $seat_number = $_POST['seat_number'];
        $type = $_POST['type'];

        $stmt = $conn->prepare("UPDATE Seats SET seat_number = ?, type = ? WHERE seat_id IN (" . implode(',', array_fill(0, count($seat_ids), '?')) . ")");
        $stmt->bind_param(str_repeat('i', count($seat_ids)), ...$seat_ids);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch seats for selected boat
$selected_boat_id = isset($_GET['boat_id']) ? (int)$_GET['boat_id'] : 0;
$seats = $conn->query("SELECT * FROM Seats WHERE boat_id = $selected_boat_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="fontawesome-5.5/css/all.min.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/tooplate-infinite-loop.css" />
</head>
<body>
    <?php include 'includes/headeradmin.php'; ?>
    <br><br><br>

    <div class="container mt-5">
        <h1 class="mb-4">Admin Seat Management</h1>

        <!-- Boat Selection Form -->
        <form method="get" action="" class="mb-4">
            <div class="mb-3">
                <label for="boat_id" class="form-label">Select Boat:</label>
                <select id="boat_id" name="boat_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Boat --</option>
                    <?php while ($boat = $boats->fetch_assoc()): ?>
                        <option value="<?= $boat['boat_id'] ?>" <?= $boat['boat_id'] == $selected_boat_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($boat['boat_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <?php if ($selected_boat_id): ?>
            <h2 class="mb-4">Seats for Boat ID <?= $selected_boat_id ?></h2>

            <!-- Add Seat Form -->
            <form method="post" action="" class="mb-4">
                <input type="hidden" name="boat_id" value="<?= $selected_boat_id ?>">
                <div class="mb-3">
                    <label for="seat_number" class="form-label">Seat Number:</label>
                    <input type="text" id="seat_number" name="seat_number" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Seat Type:</label>
                    <input type="text" id="type" name="type" class="form-control" required>
                </div>
                <button type="submit" name="add_seat" class="btn btn-primary">Add Seat</button>
            </form>

            <!-- Add Seats to Maximum Capacity Form -->
            <form method="post" action="" class="mb-4">
                <input type="hidden" name="boat_id" value="<?= $selected_boat_id ?>">
                <button type="submit" name="add_max_capacity" class="btn btn-success">Add Seats to Max Capacity</button>
            </form>

            <!-- Add Custom Seats Form -->
            <form method="post" action="" class="mb-4">
                <input type="hidden" name="boat_id" value="<?= $selected_boat_id ?>">
                <div class="mb-3">
                    <label for="seat_number_prefix" class="form-label">Seat Number Prefix:</label>
                    <input type="text" id="seat_number_prefix" name="seat_number_prefix" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="seat_number_start" class="form-label">Starting Seat Number:</label>
                    <input type="number" id="seat_number_start" name="seat_number_start" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="num_seats" class="form-label">Number of Seats:</label>
                    <input type="number" id="num_seats" name="num_seats" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Seat Type:</label>
                    <input type="text" id="type" name="type" class="form-control" required>
                </div>
                <button type="submit" name="add_custom_seats" class="btn btn-info">Add Custom Seats</button>
            </form>

            <!-- Edit Group of Seats Form -->
            <form method="post" action="" class="mb-4">
                <input type="hidden" name="boat_id" value="<?= $selected_boat_id ?>">
                <div class="mb-3">
                    <label for="seat_ids" class="form-label">Select Seats to Edit:</label>
                    <select id="seat_ids" name="seat_ids[]" class="form-select" multiple required>
                        <?php 
                        $seats_options = $conn->query("SELECT seat_id, seat_number FROM Seats WHERE boat_id = $selected_boat_id");
                        while ($seat_option = $seats_options->fetch_assoc()): ?>
                            <option value="<?= $seat_option['seat_id'] ?>">
                                <?= htmlspecialchars($seat_option['seat_number']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="seat_number" class="form-label">New Seat Number:</label>
                    <input type="text" id="seat_number" name="seat_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">New Seat Type:</label>
                    <input type="text" id="type" name="type" class="form-control">
                </div>
                <button type="submit" name="edit_group_seats" class="btn btn-warning">Edit Group of Seats</button>
            </form>

            <!-- Existing Seats Table -->
            <h3>Existing Seats</h3>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Seat ID</th>
                        <th>Seat Number</th>
                        <th>Seat Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($seat = $seats->fetch_assoc()): ?>
                        <tr>
                            <form method="post" action="">
                                <td><?= $seat['seat_id'] ?></td>
                                <td><input type="text" name="seat_number" value="<?= htmlspecialchars($seat['seat_number']) ?>" class="form-control" required></td>
                                <td><input type="text" name="type" value="<?= htmlspecialchars($seat['type']) ?>" class="form-control" required></td>
                                <td>
                                    <input type="hidden" name="seat_id" value="<?= $seat['seat_id'] ?>">
                                    <button type="submit" name="edit_seat" class="btn btn-warning">Edit</button>
                                    <button type="submit" name="delete_seat" class="btn btn-danger">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <br><br><br>
    <?php include "includes/footer.php"; ?>
  
<script src="js/jquery-1.9.1.min.js"></script>     
    <script src="slick/slick.min.js"></script>
    <script src="magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/easing.min.js"></script>
    <script src="js/jquery.singlePageNav.min.js"></script>     
    <script src="js/bootstrap.min.js"></script> 
    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <br> <br> <br> <br> <br> <br> <br> <br> <br>
</body>

</html>
