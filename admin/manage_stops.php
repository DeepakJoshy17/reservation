<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_SESSION['admin_id']; // Get the admin ID from session
    $route_id = $_POST['route_id'] ?? null;
    $stop_id = $_POST['stop_id'] ?? null;
    $location = $_POST['location'] ?? null;
    $stop_order = $_POST['stop_order'] ?? null;
    $km = $_POST['km'] ?? null;

    // Calculate arrival time based on km, assuming 10 minutes per km
    $arrival_time = date("H:i", strtotime("+".(10 * (float)$km)." minutes", strtotime("00:00")));

    function logAdminAction($conn, $admin_id, $action, $description) {
        $log_query = "INSERT INTO Admin_Logs (admin_id, action, description, timestamp) VALUES (?, ?, ?, NOW())";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("iss", $admin_id, $action, $description);
        $log_stmt->execute();
        $log_stmt->close();
    }

    if (isset($_POST['edit_stop'])) {
        $arrival_time = $_POST['arrival_time'] ?? null;
    
        // Update Route_Stops and Route_Stop_Times tables
        $query = "UPDATE Route_Stops SET location = ?, stop_order = ?, km = ? WHERE stop_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdii", $location, $stop_order, $km, $stop_id);
        $stmt->execute();
        $stmt->close();
    
        $query_time = "UPDATE Route_Stop_Times SET arrival_time = ? WHERE route_id = ? AND stop_id = ?";
        $stmt_time = $conn->prepare($query_time);
        $stmt_time->bind_param("sii", $arrival_time, $route_id, $stop_id);
        $stmt_time->execute();
        $stmt_time->close();
    
        logAdminAction($conn, $admin_id, 'Edit Stop', "Edited stop ID: $stop_id for route ID: $route_id, including arrival time.");
    }
if (isset($_POST['add_stop'])) {
        // Add stop
        $query = "INSERT INTO Route_Stops (route_id, location, stop_order, km) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isid", $route_id, $location, $stop_order, $km);
        $stmt->execute();
        $stop_id = $stmt->insert_id; // Get the inserted stop ID
        $stmt->close();

        $query_time = "INSERT INTO Route_Stop_Times (route_id, stop_id, arrival_time) VALUES (?, ?, ?)";
        $stmt_time = $conn->prepare($query_time);
        $stmt_time->bind_param("iis", $route_id, $stop_id, $arrival_time);
        $stmt_time->execute();
        $stmt_time->close();

        // Log the action with description
        logAdminAction($conn, $admin_id, 'Add Stop', "Added stop ID: $stop_id for route ID: $route_id with location '$location', stop order $stop_order, km $km, and arrival time '$arrival_time'.");
    }
}

// Fetch all routes for dropdown
$query_routes = "SELECT route_id, route_name FROM Routes";
$result_routes = $conn->query($query_routes);

// Fetch stops for a specific route if route_id is provided
$stops = [];
$route_id = $_GET['route_id'] ?? null;
$search_location = $_GET['search_location'] ?? '';
$filter_stop_order = $_GET['filter_stop_order'] ?? '';
$filter_km = $_GET['filter_km'] ?? '';

if ($route_id) {
    $query = "SELECT s.stop_id, s.location, s.stop_order, s.km, t.arrival_time
              FROM Route_Stops s
              LEFT JOIN Route_Stop_Times t ON s.stop_id = t.stop_id AND s.route_id = t.route_id
              WHERE s.route_id = ?";

    $params = [$route_id];
    $types = 'i';

    if ($search_location) {
        $query .= " AND s.location LIKE ?";
        $params[] = "%$search_location%";
        $types .= 's';
    }
    if ($filter_stop_order) {
        $query .= " AND s.stop_order = ?";
        $params[] = $filter_stop_order;
        $types .= 'i';
    }
    if ($filter_km) {
        $query .= " AND s.km = ?";
        $params[] = $filter_km;
        $types .= 'd';
    }

    $query .= " ORDER BY s.stop_order";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $stops[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Manage Stops</h1>

        <!-- Add Stop -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Stop</h2><br><br>
                <form action="manage_stops.php" method="post">
                    <input type="hidden" name="add_stop" value="add_stop">
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Route Name:</label>
                        <select id="route_id" name="route_id" class="form-select" required>
                            <option value="">Select Route</option>
                            <?php while ($row = $result_routes->fetch_assoc()): ?>
                                <option value="<?php echo $row['route_id']; ?>" <?php echo ($route_id == $row['route_id']) ? 'selected' : ''; ?>>
                                    <?php echo $row['route_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location:</label>
                        <input type="text" id="location" name="location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="stop_order" class="form-label">Stop Order:</label>
                        <input type="number" id="stop_order" name="stop_order" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="km" class="form-label">Distance (km):</label>
                        <input type="number" id="km" name="km" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Stop</button>
                </form>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Search & Filter Stops</h2><br><br>
                <form action="manage_stops.php" method="get">
                    <div class="mb-3">
                        <label for="route_id" class="form-label">Route:</label>
                        <select id="route_id" name="route_id" class="form-select" required>
                            <option value="">Select Route</option>
                            <?php foreach ($result_routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>" <?php echo ($route_id == $route['route_id']) ? 'selected' : ''; ?>>
                                    <?php echo $route['route_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="search_location" class="form-label">Location:</label>
                        <input type="text" id="search_location" name="search_location" class="form-control" value="<?php echo htmlspecialchars($search_location); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="filter_stop_order" class="form-label">Stop Order:</label>
                        <input type="number" id="filter_stop_order" name="filter_stop_order" class="form-control" value="<?php echo htmlspecialchars($filter_stop_order); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="filter_km" class="form-label">Distance (km):</label>
                        <input type="number" id="filter_km" name="filter_km" class="form-control" value="<?php echo htmlspecialchars($filter_km); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>

        <!-- Display Stops -->
        <?php if ($stops): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Stops for Route: <?php echo $route_id; ?></h2>
                    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Location</th>
            <th>Stop Order</th>
            <th>Distance (km)</th>
            <th>Arrival Time</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stops as $stop): ?>
            <tr>
                <form action="manage_stops.php" method="post">
                    <input type="hidden" name="stop_id" value="<?php echo $stop['stop_id']; ?>">
                    <input type="hidden" name="route_id" value="<?php echo $route_id; ?>">
                    <td>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($stop['location']); ?>" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="stop_order" value="<?php echo htmlspecialchars($stop['stop_order']); ?>" class="form-control">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="km" value="<?php echo htmlspecialchars($stop['km']); ?>" class="form-control">
                    </td>
                    <td>
                        <input type="time" name="arrival_time" value="<?php echo htmlspecialchars($stop['arrival_time']); ?>" class="form-control">
                    </td>
                    <td>
                        <button type="submit" name="edit_stop" class="btn btn-primary">Save</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                </div>
            </div>
        <?php else: ?>
            <p>No stops found for the selected route.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
