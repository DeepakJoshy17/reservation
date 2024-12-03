<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>
<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
}

// Handle actions for adding, editing, and removing routes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $route_id = $_POST['route_id'] ?? null;
    $route_name = $_POST['route_name'] ?? null;
    $start_location = $_POST['start_location'] ?? null;
    $end_location = $_POST['end_location'] ?? null;

    $admin_id = $_SESSION['admin_id']; // Get the logged-in admin ID

    if (isset($_POST['edit_route'])) {
        $query = "UPDATE Routes SET route_name = ?, start_location = ?, end_location = ? WHERE route_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $route_name, $start_location, $end_location, $route_id);
        if ($stmt->execute()) {
            log_admin_action($admin_id, "Edit Route", "Edited Route ID: $route_id");
        }
    } elseif (isset($_POST['delete_route'])) {
        $query = "DELETE FROM Routes WHERE route_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $route_id);
        if ($stmt->execute()) {
            log_admin_action($admin_id, "Delete Route", "Deleted Route ID: $route_id");
        }
    } elseif (isset($_POST['add_route'])) {
        $query = "INSERT INTO Routes (route_name, start_location, end_location) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $route_name, $start_location, $end_location);
        if ($stmt->execute()) {
            log_admin_action($admin_id, "Add Route", "Added Route: $route_name");
        }
    }
}

// Fetch all routes
$routes = [];
$search_query = "";
$search_route_name = $_GET['search_route_name'] ?? '';
$search_start_location = $_GET['search_start_location'] ?? '';
$search_end_location = $_GET['search_end_location'] ?? '';

if ($search_route_name || $search_start_location || $search_end_location) {
    $search_query = "WHERE route_name LIKE ? OR start_location LIKE ? OR end_location LIKE ?";
    $stmt = $conn->prepare("SELECT route_id, route_name, start_location, end_location FROM Routes $search_query");
    $search_term = '%' . $search_route_name . '%';
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT route_id, route_name, start_location, end_location FROM Routes";
    $result = $conn->query($query);
}

while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Manage Routes</h1>

        <!-- Add Route -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Route</h2><br><br>
                <form action="manage_routes.php" method="post">
                    <input type="hidden" name="add_route" value="add_route">
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
                    <button type="submit" class="btn btn-primary">Add Route</button>
                </form>
            </div>
        </div>
        
        <!-- Search and Filter Form -->
        <form method="get" action="" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="search_route_name" class="form-label">Search by Route Name:</label>
                    <input type="text" id="search_route_name" name="search_route_name" class="form-control" value="<?= htmlspecialchars($search_route_name) ?>">
                </div>
                <div class="col-md-4">
                    <label for="search_start_location" class="form-label">Search by Start Location:</label>
                    <input type="text" id="search_start_location" name="search_start_location" class="form-control" value="<?= htmlspecialchars($search_start_location) ?>">
                </div>
                <div class="col-md-4">
                    <label for="search_end_location" class="form-label">Search by End Location:</label>
                    <input type="text" id="search_end_location" name="search_end_location" class="form-control" value="<?= htmlspecialchars($search_end_location) ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="manage_routes.php" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- All Routes (with inline edit/delete) -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">All Routes</h2>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Route Name</th>
                            <th>Start Location</th>
                            <th>End Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $route): ?>
                            <tr>
                                <form method="post" action="">
                                    <td><?= $route['route_id'] ?></td>
                                    <td><input type="text" name="route_name" value="<?= htmlspecialchars($route['route_name']) ?>" class="form-control" required></td>
                                    <td><input type="text" name="start_location" value="<?= htmlspecialchars($route['start_location']) ?>" class="form-control" required></td>
                                    <td><input type="text" name="end_location" value="<?= htmlspecialchars($route['end_location']) ?>" class="form-control" required></td>
                                    <td>
                                        <input type="hidden" name="route_id" value="<?= $route['route_id'] ?>">
                                        <button type="submit" name="edit_route" class="btn btn-warning">Edit</button>
                                        <button type="submit" name="delete_route" class="btn btn-danger">Delete</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>



