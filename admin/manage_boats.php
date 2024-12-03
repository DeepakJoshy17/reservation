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

$admin_id = $_SESSION['admin_id']; // Retrieve the logged-in admin's ID

// Function to log admin actions
function log_admin_action($admin_id, $action, $description) {
    global $conn;
    $query = "INSERT INTO Admin_Logs (admin_id, action, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $admin_id, $action, $description);
    $stmt->execute();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $boat_id = $_POST['boat_id'] ?? null;
    $boat_name = $_POST['boat_name'] ?? null;
    $capacity = $_POST['capacity'] ?? null;
    $status = $_POST['status'] ?? null;

    if (isset($_POST['edit_boat'])) {
        $query = "UPDATE Boats SET boat_name = ?, capacity = ?, status = ? WHERE boat_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisi", $boat_name, $capacity, $status, $boat_id);
        if ($stmt->execute()) {
            // Log the edit action
            $description = "Edited boat: ID = $boat_id, Name changed to: $boat_name, Capacity changed to: $capacity, Status changed to: $status";
            log_admin_action($admin_id, 'Edit Boat', $description);
        }
    } elseif (isset($_POST['delete_boat'])) {
        $query = "DELETE FROM Boats WHERE boat_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $boat_id);
        if ($stmt->execute()) {
            // Log the delete action
            $description = "Deleted boat: ID = $boat_id, Name = $boat_name";
            log_admin_action($admin_id, 'Delete Boat', $description);
        }
    } elseif (isset($_POST['add_boat'])) {
        $query = "INSERT INTO Boats (boat_name, capacity, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sis", $boat_name, $capacity, $status);
        if ($stmt->execute()) {
            // Log the add action
            $description = "Added new boat: Name = $boat_name, Capacity = $capacity, Status = $status";
            log_admin_action($admin_id, 'Add Boat', $description);
        }
    }
}

// Fetch all boats or filter based on search and filters
$search_name = $_GET['search_name'] ?? '';
$filter_capacity = $_GET['filter_capacity'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';

$query = "SELECT boat_id, boat_name, capacity, status FROM Boats WHERE 1=1";

if ($search_name) {
    $query .= " AND boat_name LIKE '%" . $conn->real_escape_string($search_name) . "%'";
}
if ($filter_capacity) {
    $query .= " AND capacity = " . $conn->real_escape_string($filter_capacity);
}
if ($filter_status) {
    $query .= " AND status = '" . $conn->real_escape_string($filter_status) . "'";
}

$result = $conn->query($query);
$boats = [];
while ($row = $result->fetch_assoc()) {
    $boats[] = $row;
}
?>
<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Manage Boats</h1>

        <!-- Add Boat -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Add Boat</h2>
                <form action="manage_boats.php" method="post">
                    <input type="hidden" name="add_boat" value="add_boat">
                    <div class="mb-3">
                        <label for="boat_name" class="form-label"><br><br>Boat Name:</label>
                        <input type="text" id="boat_name" name="boat_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Boat</button>
                </form>
            </div>
        </div>

        <!-- Search and Filter Options -->
        <div class="mb-4">
            <form method="get" action="manage_boats.php">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search_boat_name" class="form-label">Search by Boat Name:</label>
                        <input type="text" name="search_name" class="form-control" placeholder="Search by Boat Name" value="<?= htmlspecialchars($search_name) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_capacity" class="form-label">Search by Boat Capacity:</label>
                        <input type="number" name="filter_capacity" class="form-control" placeholder="Filter by Capacity" value="<?= htmlspecialchars($filter_capacity) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_status" class="form-label">Search by Status:</label>
                        <select name="filter_status" class="form-select">
                            <option value="">Filter by Status</option>
                            <option value="Available" <?= $filter_status == 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Unavailable" <?= $filter_status == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        <!-- All Boats (with inline edit/delete) -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">All Boats</h2>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($boats)): ?>
                            <?php foreach ($boats as $boat): ?>
                                <tr>
                                    <form method="post" action="">
                                        <td><?= $boat['boat_id'] ?></td>
                                        <td><input type="text" name="boat_name" value="<?= htmlspecialchars($boat['boat_name']) ?>" class="form-control" required></td>
                                        <td><input type="number" name="capacity" value="<?= htmlspecialchars($boat['capacity']) ?>" class="form-control" required></td>
                                        <td>
                                            <select name="status" class="form-select" required>
                                                <option value="Available" <?= $boat['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                                                <option value="Unavailable" <?= $boat['status'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" name="boat_id" value="<?= $boat['boat_id'] ?>">
                                            <button type="submit" name="edit_boat" class="btn btn-warning">Edit</button>
                                            <button type="submit" name="delete_boat" class="btn btn-danger">Delete</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No boats found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>



