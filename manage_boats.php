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

function manage_boat($action) {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $boat_id = $_POST['boat_id'] ?? null;
        $boat_name = $_POST['boat_name'] ?? null;
        $capacity = $_POST['capacity'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($action == 'add') {
            $query = "INSERT INTO Boats (boat_name, capacity, status) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sis", $boat_name, $capacity, $status);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Boat added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'edit') {
            $query = "UPDATE Boats SET boat_name = ?, capacity = ?, status = ? WHERE boat_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sisi", $boat_name, $capacity, $status, $boat_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Boat updated successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'remove') {
            $query = "DELETE FROM Boats WHERE boat_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $boat_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Boat removed successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

// Fetch all boats for the dropdown
$boats = [];
$query = "SELECT boat_id, boat_name, capacity, status FROM Boats";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $boats[] = $row;
}

// Determine action based on request
$action = isset($_POST['action']) ? $_POST['action'] : '';
manage_boat($action);
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
  <link rel="stylesheet" href="css/manage.css" />
</head>


<body>
<?php include 'includes/headeradmin.php'; ?>
<br><br><br><br>
    <div class="container mt-5">
        <h1 class="mb-4">Manage Boats</h1>
        
        <!-- Add Boat -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Boat</h2>
                <form action="manage_boats.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="boat_name" class="form-label">Boat Name:</label>
                        <input type="text" id="boat_name" name="boat_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <input type="text" id="status" name="status" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Boat</button>
                </form>
            </div>
        </div>
        
        <!-- Edit Boat -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Edit Boat</h2>
                <form action="manage_boats.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <div class="mb-3">
                        <label for="boat_id_edit" class="form-label">Select Boat:</label>
                        <select id="boat_id_edit" name="boat_id" class="form-select" required onchange="populateBoatDetails(this.value)">
                            <option value="">-- Select a Boat --</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?= $boat['boat_id']; ?>"><?= $boat['boat_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="boat_name_edit" class="form-label">Boat Name:</label>
                        <input type="text" id="boat_name_edit" name="boat_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity_edit" class="form-label">Capacity:</label>
                        <input type="number" id="capacity_edit" name="capacity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status_edit" class="form-label">Status:</label>
                        <input type="text" id="status_edit" name="status" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Update Boat</button>
                </form>
            </div>
        </div>
        
        <!-- Remove Boat -->
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Remove Boat</h2>
                <form action="manage_boats.php" method="post">
                    <input type="hidden" name="action" value="remove">
                    <div class="mb-3">
                        <label for="boat_id_remove" class="form-label">Select Boat:</label>
                        <select id="boat_id_remove" name="boat_id" class="form-select" required>
                            <option value="">-- Select a Boat --</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?= $boat['boat_id']; ?>"><?= $boat['boat_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger">Remove Boat</button>
                </form>
            </div>
        </div>
    </div>
    <br><br><br>
    <?php include "includes/footer.php"; ?>
  
  <script src="js/jquery-1.9.1.min.js"></script>     
      <script src="slick/slick.min.js"></script>
      <script src="magnific-popup/jquery.magnific-popup.min.js"></script>
      <script src="js/easing.min.js"></script>
      <script src="js/jquery.singlePageNav.min.js"></script>     
      <script src="js/bootstrap.min.js"></script> 

    <script>
        // JavaScript to populate boat details when a boat is selected for editing
        function populateBoatDetails(boatId) {
            const boats = <?= json_encode($boats); ?>;
            const selectedBoat = boats.find(boat => boat.boat_id == boatId);
            if (selectedBoat) {
                document.getElementById('boat_name_edit').value = selectedBoat.boat_name;
                document.getElementById('capacity_edit').value = selectedBoat.capacity;
                document.getElementById('status_edit').value = selectedBoat.status;
            }
        }
    </script>
    <br><br><br>
</body>


</html>

