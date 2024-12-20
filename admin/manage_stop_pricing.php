<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>
<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
//session_start(); // Uncommented session_start()

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';

// Function to log admin actions
function log_admin_action($admin_id, $action, $description) {
    global $conn;
    $query = "INSERT INTO Admin_Logs (admin_id, action, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $admin_id, $action, $description);
    $stmt->execute();
    $stmt->close();
}

function calculate_and_insert_prices($route_id, $price_per_km) {
    global $conn;
    
    // Delete existing prices for the route
    $delete_query = "DELETE FROM Stop_Pricing WHERE start_stop_id IN (SELECT stop_id FROM Route_Stops WHERE route_id = ?)";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $stmt->close();

    // Calculate and insert new prices for the same route
    $query = "SELECT rs1.stop_id AS start_stop_id, rs1.km AS start_km, rs2.stop_id AS end_stop_id, rs2.km AS end_km 
              FROM Route_Stops rs1 
              JOIN Route_Stops rs2 ON rs1.stop_id < rs2.stop_id
              WHERE rs1.route_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $km_difference = abs($row['end_km'] - $row['start_km']);
        // Skip if the difference is zero or if the start and end stop are the same
        if ($km_difference == 0 || $row['start_stop_id'] == $row['end_stop_id']) {
            continue;
        }
        $price = $km_difference * $price_per_km; // Use provided price per km
        
        // Ensure start_stop_id and end_stop_id are part of the same route
        $check_route_query = "SELECT COUNT(*) AS count FROM Route_Stops WHERE stop_id IN (?, ?) AND route_id = ?";
        $stmt_check_route = $conn->prepare($check_route_query);
        $stmt_check_route->bind_param("iii", $row['start_stop_id'], $row['end_stop_id'], $route_id);
        $stmt_check_route->execute();
        $check_result = $stmt_check_route->get_result();
        $check_row = $check_result->fetch_assoc();

        if ($check_row['count'] == 2) {
            // Insert pricing only if both stops are part of the same route
            $insert_query = "INSERT INTO Stop_Pricing (start_stop_id, end_stop_id, price) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_query);
            $stmt_insert->bind_param("iid", $row['start_stop_id'], $row['end_stop_id'], $price);
            $stmt_insert->execute();
            $stmt_insert->close();
        }

        $stmt_check_route->close();
    }
    $stmt->close();
}


// Handle form submission for updating prices for a selected route
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['route_id']) && isset($_POST['price_per_km'])) {
    $route_id = $_POST['route_id'];
    $price_per_km = (float)$_POST['price_per_km']; // Cast price to float
    calculate_and_insert_prices($route_id, $price_per_km);
    log_admin_action($_SESSION['admin_id'], 'Update Stop Pricing', "Updated prices for route ID: $route_id");
    $message = "<div class='alert alert-success'>Prices updated successfully for route ID: $route_id.</div>";
}

// Fetch all routes for dropdown options
$routes_query = "SELECT route_id, route_name FROM Routes ORDER BY route_name";
$routes_result = $conn->query($routes_query);
$routes = [];
while ($row = $routes_result->fetch_assoc()) {
    $routes[] = $row;
}

// Function to manage stop pricing
function manage_stop_pricing($action) {
    global $conn;
    $admin_id = $_SESSION['admin_id']; // Get logged in admin ID
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $start_stop_id = isset($_POST['start_stop_id']) ? $_POST['start_stop_id'] : null;
        $end_stop_id = isset($_POST['end_stop_id']) ? $_POST['end_stop_id'] : null;
        $price = isset($_POST['price']) ? $_POST['price'] : null;

        if ($action == 'add') {
            if ($start_stop_id !== null && $end_stop_id !== null && $price !== null) {
                $query = "INSERT INTO Stop_Pricing (start_stop_id, end_stop_id, price) VALUES (?, ?, ?) 
                          ON DUPLICATE KEY UPDATE price = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iids", $start_stop_id, $end_stop_id, $price, $price);
                if ($stmt->execute()) {
                    $msg = "<div class='alert alert-success'>Price added/updated successfully.</div>";
                    log_admin_action($admin_id, 'Add/Update Stop Price', "Added price for start stop ID: $start_stop_id and end stop ID: $end_stop_id");
                } else {
                    $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                return "<div class='alert alert-danger'>Please provide all fields.</div>";
            }
        } elseif ($action == 'edit') {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            if ($price !== null && $id !== null) {
                $query = "UPDATE Stop_Pricing SET price = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("di", $price, $id);
                if ($stmt->execute()) {
                    $msg = "<div class='alert alert-success'>Price updated successfully.</div>";
                    log_admin_action($admin_id, 'Edit Stop Price', "Updated price for ID: $id");
                } else {
                    $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                return "<div class='alert alert-danger'>ID and price are required for editing.</div>";
            }
        } elseif ($action == 'remove') {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            if ($id !== null) {
                $query = "DELETE FROM Stop_Pricing WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $msg = "<div class='alert alert-success'>Price removed successfully.</div>";
                    log_admin_action($admin_id, 'Remove Stop Price', "Removed price for ID: $id");
                } else {
                    $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                return "<div class='alert alert-danger'>ID is required for removal.</div>";
            }
        }
        return isset($msg) ? $msg : '';
    }
}

// Handle form submissions
$action = isset($_POST['action']) ? $_POST['action'] : '';
$message = manage_stop_pricing($action);

// Fetch all stop pricing records with search and filter functionality
$search_query = "SELECT sp.id, rs1.location AS start_location, rs2.location AS end_location, sp.price 
                 FROM Stop_Pricing sp
                 JOIN Route_Stops rs1 ON sp.start_stop_id = rs1.stop_id
                 JOIN Route_Stops rs2 ON sp.end_stop_id = rs2.stop_id";

if (isset($_GET['start_stop']) || isset($_GET['end_stop']) || isset($_GET['price'])) {
    $start_stop = isset($_GET['start_stop']) ? '%' . $_GET['start_stop'] . '%' : '%';
    $end_stop = isset($_GET['end_stop']) ? '%' . $_GET['end_stop'] . '%' : '%';
    $price = isset($_GET['price']) ? $_GET['price'] : '';

    $search_query .= " WHERE rs1.location LIKE ? AND rs2.location LIKE ?";
    if ($price) {
        $search_query .= " AND sp.price = ?";
    }

    $stmt = $conn->prepare($search_query);
    if ($price) {
        $stmt->bind_param("sss", $start_stop, $end_stop, $price);
    } else {
        $stmt->bind_param("ss", $start_stop, $end_stop);
    }
    $stmt->execute();
    $search_result = $stmt->get_result();
} else {
    $search_result = $conn->query($search_query);
}

// Fetch all stops for dropdown options
$stops_query = "SELECT stop_id, location FROM Route_Stops ORDER BY location";
$stops_result = $conn->query($stops_query);
$stops = [];
while ($row = $stops_result->fetch_assoc()) {
    $stops[] = $row;
}

?>

<div class="content-wrapper">
    <div class=container mt-5">
    <h2 class="mb-4">Manage Stop Pricing</h2>
    <?php if ($message) echo $message; ?>
    
    
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Update Prices for Route</h2>
                    <form action="manage_stop_pricing.php" method="post">
                        <div class="mb-3">
                            <label for="route_id" class="form-label"><br><br>Select Route:</label>
                            <select id="route_id" name="route_id" class="form-select" required>
                                <option value="">Select a route</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price_per_km" class="form-label">Price per Km (Default: 5 rupees):</label>
                            <input type="number" step="0.01" name="price_per_km" value="5" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Prices</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Price List</h2><br><br>
                    <form action="" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col">
                                <input type="text" name="start_stop" placeholder="Start Stop" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="end_stop" placeholder="End Stop" class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" name="price" placeholder="Price" class="form-control">
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Start Stop</th>
                                <th>End Stop</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $search_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['start_location']; ?></td>
                                    <td><?php echo $row['end_location']; ?></td>
                                    <td><?php echo $row['price']; ?></td>
                                    <td>
                                        <form action="manage_stop_pricing.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="number" name="price" step="0.01" value="<?php echo $row['price']; ?>" required>
                                            <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                                        </form>
                                        <form action="manage_stop_pricing.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
