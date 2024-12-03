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

// Function to fetch boat names and IDs
function get_boats() {
    global $conn;
    $query = "SELECT boat_id, boat_name FROM Boats ORDER BY boat_name";
    $result = $conn->query($query);
    $boats = [];
    while ($row = $result->fetch_assoc()) {
        $boats[] = $row;
    }
    return $boats;
}

// Function to fetch route names and IDs
function get_routes() {
    global $conn;
    $query = "SELECT route_id, route_name FROM Routes ORDER BY route_name";
    $result = $conn->query($query);
    $routes = [];
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
    return $routes;
}

// Function to fetch schedules based on selected boat and route
function get_schedules($boat_id, $route_id) {
    global $conn;
    $query = "SELECT schedule_id, departure_time, arrival_time FROM Schedules WHERE boat_id = ? AND route_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $boat_id, $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    $stmt->close();
    return $schedules;
}

$boats = get_boats();
$routes = get_routes();
$schedules = [];
if (isset($_POST['boat_id_edit']) && isset($_POST['route_id_edit'])) {
    $schedules = get_schedules($_POST['boat_id_edit'], $_POST['route_id_edit']);
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
    <!-- Custom CSS -->
    <!-- Add your custom CSS here if needed -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
    // Handle add schedule
    $('#addScheduleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: $(this).serialize() + '&action=add',
            success: function(response) {
                $('#message').html(response);
                $('#addScheduleForm')[0].reset();
            }
        });
    });

    // Handle edit schedule
    $('#editScheduleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: $(this).serialize() + '&action=edit',
            success: function(response) {
                $('#message').html(response);
                $('#editScheduleForm')[0].reset();
            }
        });
    });

    // Handle remove schedule
    $('#removeScheduleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: $(this).serialize() + '&action=remove',
            success: function(response) {
                $('#message').html(response);
                $('#removeScheduleForm')[0].reset();
            }
        });
    });

    // Update schedules dropdown based on selected boat and route
    $('#boat_id_edit, #route_id_edit').on('change', function() {
        var boat_id = $('#boat_id_edit').val();
        var route_id = $('#route_id_edit').val();
        if (boat_id && route_id) {
            $.ajax({
                type: 'POST',
                url: 'manage_schedules_action.php',
                data: { boat_id: boat_id, route_id: route_id, action: 'fetch_schedules' },
                success: function(response) {
                    $('#schedule_id_edit').html(response);
                }
            });
        }
    });

    $('#boat_id_remove, #route_id_remove').on('change', function() {
        var boat_id = $('#boat_id_remove').val();
        var route_id = $('#route_id_remove').val();
        if (boat_id && route_id) {
            $.ajax({
                type: 'POST',
                url: 'manage_schedules_action.php',
                data: { boat_id: boat_id, route_id: route_id, action: 'fetch_schedules' },
                success: function(response) {
                    $('#schedule_id_remove').html(response);
                }
            });
        }
    });
});
</script>
</head>

<body>
    <?php include 'includes/headeradmin.php'; ?>
<br><br><br>
    <div class="container mt-5">
        <h1 class="mb-4">Manage Schedules</h1>
        <div id="message"></div>

        <!-- Add Schedule -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Schedule</h2>
                <form id="addScheduleForm">
                    <div class="mb-3">
                        <label for="boat_id" class="form-label">Boat:</label>
                        <select id="boat_id" name="boat_id" class="form-select" required>
                            <option value="">Select Boat</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?= $boat['boat_id'] ?>"><?= $boat['boat_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Route:</label>
                        <select id="route_id" name="route_id" class="form-select" required>
                            <option value="">Select Route</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= $route['route_id'] ?>"><?= $route['route_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="departure_time" class="form-label">Departure Time:</label>
                        <input type="datetime-local" id="departure_time" name="departure_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="arrival_time" class="form-label">Arrival Time:</label>
                        <input type="datetime-local" id="arrival_time" name="arrival_time" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </form>
            </div>
        </div>

       <!-- Edit Schedule -->
<div class="card mb-4">
    <div class="card-body">
        <h2 class="card-title">Edit Schedule</h2>
        <form id="editScheduleForm">
            <div class="mb-3">
                <label for="route_id_edit" class="form-label">Route:</label>
                <select id="route_id_edit" name="route_id_edit" class="form-select" required>
                    <option value="">Select Route</option>
                    <?php foreach ($routes as $route): ?>
                        <option value="<?= $route['route_id'] ?>" <?= isset($_POST['route_id_edit']) && $_POST['route_id_edit'] == $route['route_id'] ? 'selected' : '' ?>><?= $route['route_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="boat_id_edit" class="form-label">Boat:</label>
                <select id="boat_id_edit" name="boat_id_edit" class="form-select" required>
                    <option value="">Select Boat</option>
                    <?php foreach ($boats as $boat): ?>
                        <option value="<?= $boat['boat_id'] ?>" <?= isset($_POST['boat_id_edit']) && $_POST['boat_id_edit'] == $boat['boat_id'] ? 'selected' : '' ?>><?= $boat['boat_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="schedule_id_edit" class="form-label">Schedule:</label>
                <select id="schedule_id_edit" name="schedule_id" class="form-select" required>
                    <option value="">Select Schedule</option>
                    <!-- Options will be populated via AJAX -->
                </select>
            </div>
            <div class="mb-3">
                <label for="new_departure_time" class="form-label">New Departure Time:</label>
                <input type="datetime-local" id="new_departure_time" name="new_departure_time" class="form-control">
            </div>
            <div class="mb-3">
                <label for="new_arrival_time" class="form-label">New Arrival Time:</label>
                <input type="datetime-local" id="new_arrival_time" name="new_arrival_time" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Schedule</button>
        </form>
        <div id="message" class="mt-2"></div>
    </div>
</div>

<!-- Remove Schedule -->
<div class="card mb-4">
    <div class="card-body">
        <h2 class="card-title">Remove Schedule</h2>
        <form id="removeScheduleForm">
            <div class="mb-3">
                <label for="route_id_remove" class="form-label">Route:</label>
                <select id="route_id_remove" name="route_id_remove" class="form-select" required>
                    <option value="">Select Route</option>
                    <?php foreach ($routes as $route): ?>
                        <option value="<?= $route['route_id'] ?>" <?= isset($_POST['route_id_remove']) && $_POST['route_id_remove'] == $route['route_id'] ? 'selected' : '' ?>><?= $route['route_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="boat_id_remove" class="form-label">Boat:</label>
                <select id="boat_id_remove" name="boat_id_remove" class="form-select" required>
                    <option value="">Select Boat</option>
                    <?php foreach ($boats as $boat): ?>
                        <option value="<?= $boat['boat_id'] ?>" <?= isset($_POST['boat_id_remove']) && $_POST['boat_id_remove'] == $boat['boat_id'] ? 'selected' : '' ?>><?= $boat['boat_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="schedule_id_remove" class="form-label">Schedule:</label>
                <select id="schedule_id_remove" name="schedule_id" class="form-select" required>
                    <option value="">Select Schedule</option>
                    <!-- Options will be populated via AJAX -->
                </select>
            </div>
            <button type="submit" class="btn btn-danger">Remove Schedule</button>
        </form>
        <div id="message" class="mt-2"></div>
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
