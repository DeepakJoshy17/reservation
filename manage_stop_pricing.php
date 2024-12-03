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

// Function to manage stop pricing
function manage_stop_pricing($action) {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $start_stop_id = $_POST['start_stop_id'];
        $end_stop_id = $_POST['end_stop_id'];
        $price = $_POST['price'];

        if ($action == 'add') {
            $query = "INSERT INTO Stop_Pricing (start_stop_id, end_stop_id, price) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iid", $start_stop_id, $end_stop_id, $price);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Price added successfully.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'edit') {
            $query = "UPDATE Stop_Pricing SET price = ? WHERE start_stop_id = ? AND end_stop_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("dii", $price, $start_stop_id, $end_stop_id);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Price updated successfully.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif ($action == 'remove') {
            $id = $_POST['id'];
            $query = "DELETE FROM Stop_Pricing WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Price removed successfully.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
        return isset($msg) ? $msg : '';
    }
}

// Handle form submissions
$action = isset($_POST['action']) ? $_POST['action'] : '';
$message = manage_stop_pricing($action);

// Fetch all stop pricing records
$query = "SELECT sp.id, rs1.location AS start_location, rs2.location AS end_location, sp.price 
          FROM Stop_Pricing sp
          JOIN Route_Stops rs1 ON sp.start_stop_id = rs1.stop_id
          JOIN Route_Stops rs2 ON sp.end_stop_id = rs2.stop_id";
$result = $conn->query($query);

// Fetch all stops for dropdown options
$stops_query = "SELECT stop_id, location FROM Route_Stops ORDER BY location";
$stops_result = $conn->query($stops_query);
$stops = [];
while ($row = $stops_result->fetch_assoc()) {
    $stops[] = $row;
}

// Fetch all routes
$routes_query = "SELECT route_id, route_name FROM Routes ORDER BY route_name";
$routes_result = $conn->query($routes_query);
$routes = [];
while ($row = $routes_result->fetch_assoc()) {
    $routes[] = $row;
}
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
    <style>
        .card { margin-bottom: 1rem; }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>
    
    <?php include 'includes/headeradmin.php'; ?>
    <br><br><br>

    <div class="container mt-5">
        <h1 class="mb-4">Manage Stop Pricing</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Price -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Price</h2>
                <form action="manage_stop_pricing.php" method="post" id="addPriceForm">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="route_id_add" class="form-label">Route:</label>
                        <select id="route_id_add" name="route_id" class="form-select" required>
                            <option value="" disabled selected>Select Route</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="start_stop_id_add" class="form-label">Start Stop:</label>
                        <select id="start_stop_id_add" name="start_stop_id" class="form-select" required>
                            <option value="" disabled selected>Select Start Stop</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="end_stop_id_add" class="form-label">End Stop:</label>
                        <select id="end_stop_id_add" name="end_stop_id" class="form-select" required>
                            <option value="" disabled selected>Select End Stop</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price_add" class="form-label">Price:</label>
                        <input type="number" step="0.01" id="price_add" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Price</button>
                </form>
            </div>
        </div>

        <!-- Edit Price -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Edit Price</h2>
                <form action="manage_stop_pricing.php" method="post" id="editPriceForm">
                    <input type="hidden" name="action" value="edit">
                    <div class="mb-3">
                        <label for="route_id_edit" class="form-label">Route:</label>
                        <select id="route_id_edit" name="route_id" class="form-select" required>
                            <option value="" disabled selected>Select Route</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="start_stop_id_edit" class="form-label">Start Stop:</label>
                        <select id="start_stop_id_edit" name="start_stop_id" class="form-select" required>
                            <option value="" disabled selected>Select Start Stop</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="end_stop_id_edit" class="form-label">End Stop:</label>
                        <select id="end_stop_id_edit" name="end_stop_id" class="form-select" required>
                            <option value="" disabled selected>Select End Stop</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price_edit" class="form-label">Price:</label>
                        <input type="number" step="0.01" id="price_edit" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Update Price</button>
                </form>
            </div>
        </div>

        <!-- Remove Price -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Remove Price</h2>
                <form action="manage_stop_pricing.php" method="post" id="removePriceForm">
                    <input type="hidden" name="action" value="remove">
                    <div class="mb-3">
                        <label for="id_remove" class="form-label">Price ID:</label>
                        <input type="number" id="id_remove" name="id" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Remove Price</button>
                </form>
            </div>
        </div>

        <!-- Display all Stop Pricing -->
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">All Stop Pricing</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Start Stop</th>
                            <th>End Stop</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['start_location']; ?></td>
                                <td><?php echo $row['end_location']; ?></td>
                                <td><?php echo number_format($row['price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const routeSelectAdd = document.getElementById('route_id_add');
            const startStopSelectAdd = document.getElementById('start_stop_id_add');
            const endStopSelectAdd = document.getElementById('end_stop_id_add');
            const routeSelectEdit = document.getElementById('route_id_edit');
            const startStopSelectEdit = document.getElementById('start_stop_id_edit');
            const endStopSelectEdit = document.getElementById('end_stop_id_edit');

            function updateStops(routeId, startStopSelect, endStopSelect) {
                fetch('get_stops_by_route.php?route_id=' + routeId)
                    .then(response => response.json())
                    .then(data => {
                        startStopSelect.innerHTML = '<option value="" disabled selected>Select Start Stop</option>';
                        endStopSelect.innerHTML = '<option value="" disabled selected>Select End Stop</option>';
                        data.forEach(stop => {
                            startStopSelect.innerHTML += `<option value="${stop.stop_id}">${stop.location}</option>`;
                            endStopSelect.innerHTML += `<option value="${stop.stop_id}">${stop.location}</option>`;
                        });
                    });
            }

            routeSelectAdd.addEventListener('change', function () {
                updateStops(this.value, startStopSelectAdd, endStopSelectAdd);
            });

            routeSelectEdit.addEventListener('change', function () {
                updateStops(this.value, startStopSelectEdit, endStopSelectEdit);
            });

            // Populate stops for existing routes on page load if necessary
            if (routeSelectAdd.value) {
                updateStops(routeSelectAdd.value, startStopSelectAdd, endStopSelectAdd);
            }
            if (routeSelectEdit.value) {
                updateStops(routeSelectEdit.value, startStopSelectEdit, endStopSelectEdit);
            }
        });
    </script>
</body>
</html>

