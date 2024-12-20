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

    if (isset($_POST['reverse_stops'])) {
        // Get stops for the selected route
        $query = "SELECT rs.stop_id, rs.location, rs.stop_order, rs.km, rst.arrival_time
                  FROM Route_Stops rs
                  LEFT JOIN Route_Stop_Times rst ON rs.stop_id = rst.stop_id
                  WHERE rs.route_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $route_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $reverse_stops = [];
        $last_km = 0;
    
        // Fetch stops and keep track of the last stop's km
        while ($row = $result->fetch_assoc()) {
            $last_km = $row['km']; // Keep track of the last stop's km
            $reverse_stops[] = [
                'stop_id' => $row['stop_id'], // Keep original stop_id values
                'route_id' => $route_id,
                'location' => $row['location'],
                'stop_order' => $row['stop_order'],
                'km' => $row['km'], // Keep original km values
                'arrival_time' => $row['arrival_time']
            ];
        }
    
        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
    
        // Delete all stops with the route_id incremented by 1
        $route_id_plus_one = $route_id + 1;
        $query_delete = "DELETE FROM Route_Stops WHERE route_id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->bind_param('i', $route_id_plus_one);
        $stmt_delete->execute();
        $stmt_delete->close();
    
        // Insert reverse stops into the Route_Stops table with the same route_id + 1
        $query_insert = "INSERT INTO Route_Stops (route_id, location, stop_order, km) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
    
        // Calculate arrival time based on km (10 minutes per km)
        $current_time = 0;
        foreach (array_reverse($reverse_stops) as $index => $reverse_stop) {
            // Increment route_id by 1 for reverse trip
            $reverse_stop['route_id'] = $route_id_plus_one;
            // Calculate new stop order for reverse trip
            $reverse_stop['stop_order'] = $index + 1;
            // Calculate new km based on total_km - actual km of that stop in reverse order
            $reverse_stop['km'] = $last_km - $reverse_stop['km'];
            // Calculate arrival time assuming 10 minutes per km
            $current_time += $reverse_stop['km'] * 10; // 10 minutes per km
    
            // Insert into Route_Stops
            $stmt_insert->bind_param('isid', $reverse_stop['route_id'], $reverse_stop['location'], $reverse_stop['stop_order'], $reverse_stop['km']);
            $stmt_insert->execute();
    
            // Save the new stop_id for the inserted reverse stop
            $reverse_stop_id = $stmt_insert->insert_id;
            $reverse_stop['stop_id'] = $reverse_stop_id;
        }
    
        $stmt_insert->close();
    
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
        // Log the action with description
        logAdminAction($conn, $admin_id, 'Reverse Stops', "Reversed stops for route ID: $route_id.");
    }
    if (isset($_POST['set_arrival_times']) && $route_id) {
        // Delete existing arrival times for the route
        $delete_query = "DELETE FROM Route_Stop_Times WHERE route_id = ?";
        $stmt_delete = $conn->prepare($delete_query);
        $stmt_delete->bind_param('i', $route_id);
        $stmt_delete->execute();
        $stmt_delete->close();
    
        // Fetch stops for the selected route
        $query = "SELECT stop_id, km FROM Route_Stops WHERE route_id = ? ORDER BY stop_order";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $route_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $insert_times_query = "INSERT INTO Route_Stop_Times (route_id, stop_id, arrival_time) VALUES (?, ?, ?)";
        $stmt_insert_times = $conn->prepare($insert_times_query);
    
        // Insert arrival times for each stop
        while ($row = $result->fetch_assoc()) {
            $stop_id = $row['stop_id'];
            $km = $row['km'];
    
            // Calculate arrival time for the km directly
            $arrival_time = date("H:i", strtotime("00:00 + ".(10 * (float)$km)." minutes"));
    
            $stmt_insert_times->bind_param('iis', $route_id, $stop_id, $arrival_time);
            $stmt_insert_times->execute();
        }
    
        $stmt_insert_times->close();
        $stmt->close();
    
        // Log the action with description
        logAdminAction($conn, $admin_id, 'Set Arrival Times', "Set arrival times for all stops in route ID: $route_id.");
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
                    <form action="manage_stops.php" method="post">
                        <input type="hidden" name="reverse_stops" value="reverse_stops">
                        <input type="hidden" name="route_id" value="<?php echo $route_id; ?>">
                        <button type="submit" class="btn btn-warning mb-3">Reverse Stops</button>
                    </form>
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
 
        <!-- Set Arrival Times -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Set Arrival Times</h2>
                <form action="manage_stops.php" method="post">
                    <div class="form-group"><br><br>
                        <label for="route_id">Select Route</label>
                        <select name="route_id" id="route_id" class="form-control" required>
                            <option value="">Select Route</option>
                            <?php foreach ($result_routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>">
                                    <?php echo $route['route_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="set_arrival_times" class="btn btn-warning mt-3">Set Arrival Times</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
