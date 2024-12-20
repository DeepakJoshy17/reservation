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

function manage_route($action) {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $route_name = $_POST['route_name'] ?? null;
        $start_location = $_POST['start_location'] ?? null;
        $end_location = $_POST['end_location'] ?? null;
        $is_return = isset($_POST['is_return']) ? 1 : 0;

        if ($action == 'add') {
            $query = "INSERT INTO Routes (route_name, start_location, end_location, is_return) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $route_name, $start_location, $end_location, $is_return);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Route added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'edit') {
            $route_id = $_POST['route_id'];
            $query = "UPDATE Routes SET route_name = ?, start_location = ?, end_location = ?, is_return = ? WHERE route_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssii", $route_name, $start_location, $end_location, $is_return, $route_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Route updated successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'remove') {
            $route_id = $_POST['route_id'];
            $query = "DELETE FROM Routes WHERE route_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $route_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Route removed successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}

// Fetch all routes for the dropdown
$routes = [];
$query = "SELECT route_id, route_name, start_location, end_location, is_return FROM Routes";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}

// Determine action based on request
$action = isset($_POST['action']) ? $_POST['action'] : '';
manage_route($action);
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
    <br><br><br><br>

    <div class="container mt-5">
        <h1 class="mb-4">Manage Routes</h1>

        <!-- Add Route -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Route</h2>
                <form action="manage_routes.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="route_name" class="form-label">Route Name:</label>
                        <input type="text" id="route_name" name="route_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_location" class="form-label">Start Location:</label>
                        <input type="text" id="start_location" name="start_location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_location" class="form-label">End Location:</label>
                        <input type="text" id="end_location" name="end_location" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_return" name="is_return">
                        <label class="form-check-label" for="is_return">Is Return Route</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Route</button>
                </form>
            </div>
        </div>

        <!-- Edit Route -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Edit Route</h2>
                <form action="manage_routes.php" method="post">
                    <input type="hidden" name="action" value="edit">
                    <div class="mb-3">
                        <label for="route_id_edit" class="form-label">Select Route:</label>
                        <select id="route_id_edit" name="route_id" class="form-select" required onchange="populateRouteDetails(this.value)">
                            <option value="">-- Select a Route --</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= $route['route_id']; ?>"><?= $route['route_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="route_name_edit" class="form-label">Route Name:</label>
                        <input type="text" id="route_name_edit" name="route_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_location_edit" class="form-label">Start Location:</label>
                        <input type="text" id="start_location_edit" name="start_location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_location_edit" class="form-label">End Location:</label>
                        <input type="text" id="end_location_edit" name="end_location" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_return_edit" name="is_return">
                        <label class="form-check-label" for="is_return_edit">Is Return Route</label>
                    </div>
                    <button type="submit" class="btn btn-warning">Update Route</button>
                </form>
            </div>
        </div>

        <!-- Remove Route -->
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Remove Route</h2>
                <form action="manage_routes.php" method="post">
                    <input type="hidden" name="action" value="remove">
                    <div class="mb-3">
                        <label for="route_id_remove" class="form-label">Select Route:</label>
                        <select id="route_id_remove" name="route_id" class="form-select" required>
                            <option value="">-- Select a Route --</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= $route['route_id']; ?>"><?= $route['route_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger">Remove Route</button>
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

    <!-- JavaScript to populate route details when a route is selected for editing -->
    <script>
        function populateRouteDetails(routeId) {
            const routes = <?= json_encode($routes); ?>;
            const selectedRoute = routes.find(route => route.route_id == routeId);
            if (selectedRoute) {
                document.getElementById('route_name_edit').value = selectedRoute.route_name;
                document.getElementById('start_location_edit').value = selectedRoute.start_location;
                document.getElementById('end_location_edit').value = selectedRoute.end_location;
                document.getElementById('is_return_edit').checked = selectedRoute.is_return == 1;
            }
        }
    </script>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <br><br><br>
</body>
</html>
