<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>
<?php 
//session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';
if (isset($_POST['remove']) && isset($_POST['schedule_id'])) {
    $schedule_id = intval($_POST['schedule_id']);
    $query = "DELETE FROM Schedules WHERE schedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Schedule removed successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to remove schedule. Error: " . $stmt->error . "</div>";
    }
    
    $stmt->close();
}


// Fetch boats and routes for form dropdowns
$boats = get_boats();
$routes = get_routes();

function get_boats() {
    global $conn;
    $query = "SELECT * FROM Boats";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_routes() {
    global $conn;
    $query = "SELECT * FROM Routes";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_schedules($boat_id = null, $route_id = null, $departure_date = null) {
    global $conn;
    $query = "SELECT Schedules.*, Boats.boat_name, Routes.route_name 
              FROM Schedules 
              JOIN Boats ON Schedules.boat_id = Boats.boat_id 
              JOIN Routes ON Schedules.route_id = Routes.route_id";
    $conditions = [];

    if ($boat_id) {
        $conditions[] = "Schedules.boat_id = " . intval($boat_id);
    }
    if ($route_id) {
        $conditions[] = "Schedules.route_id = " . intval($route_id);
    }
    if ($departure_date) {
        $conditions[] = "DATE(Schedules.departure_time) = '" . $conn->real_escape_string($departure_date) . "'";
    }
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}

$schedules = [];

if (isset($_POST['search_schedules'])) {
    $boat_id = !empty($_POST['boat_id']) ? $_POST['boat_id'] : null;
    $route_id = !empty($_POST['route_id']) ? $_POST['route_id'] : null;
    $departure_date = !empty($_POST['departure_date']) ? $_POST['departure_date'] : null;
    $schedules = get_schedules($boat_id, $route_id, $departure_date);
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#addScheduleForm').on('submit', function(e) {
        e.preventDefault();
        const isRecurring = $('#schedule_type').val() === 'recurring';
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: $(this).serialize() + '&action=add' + '&recurring=' + (isRecurring ? '1' : '0'),
            success: function(response) {
                $('#message').html(response).fadeIn(); // Show message
                setTimeout(function() {
                    $('#message').fadeOut(); // Hide message after delay
                    $('#addScheduleForm')[0].reset(); // Reset form
                    $('#searchSchedulesForm').submit(); // Resubmit search
                }, 3000); // Delay for 3000ms (3 seconds)
            }
        });
    });

    // Edit button click handler
    $(document).on('click', '.edit-button', function() {
        const scheduleId = $(this).data('id');

        // Fetch the existing schedule data for editing
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: { action: 'fetch', schedule_id: scheduleId },
            success: function(response) {
                const schedule = JSON.parse(response);
                $('#edit_schedule_id').val(schedule.schedule_id);
                $('#edit_boat_id').val(schedule.boat_id);
                $('#edit_route_id').val(schedule.route_id);
                $('#edit_departure_time').val(schedule.departure_time);
                $('#edit_arrival_time').val(schedule.arrival_time);
                $('#edit_status').val(schedule.status);
                $('#editScheduleModal').modal('show');
            }
        });
    });

    // Edit schedule form submission
    $('#editScheduleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'manage_schedules_action.php',
            data: $(this).serialize() + '&action=edit',
            success: function(response) {
                $('#message').html(response).fadeIn(); // Show message
                setTimeout(function() {
                    $('#message').fadeOut(); // Hide message after delay
                    $('#editScheduleModal').modal('hide'); // Hide modal
                    $('#searchSchedulesForm').submit(); // Resubmit search
                }, 3000); // Delay for 3000ms (3 seconds)
            }
        });
    });
});
$(document).ready(function() {
    $(document).on('click', '.delete-button', function() {
        const scheduleId = $(this).data('id');
        if (confirm("Are you sure you want to delete this schedule?")) {
            $.ajax({
                type: 'POST',
                url: '', // Current file
                data: { remove: true, schedule_id: scheduleId },
                success: function(response) {
                    $('#message').html(response).fadeIn();
                    setTimeout(function() {
                        $('#message').fadeOut();
                        $('#searchSchedulesForm').submit(); // Refresh schedule list
                    }, 3000);
                }
            });
        }
    });
});

</script>


<div class="content-wrapper">
    <div class="container mt-5">
        <h1>Manage Schedules</h1>
        <div id="message"></div>
    
    <!-- Add Schedule Form -->
    <form id="addScheduleForm">
        <div class="form-group">
            <label for="schedule_type">Schedule Type:</label>
            <select id="schedule_type" name="schedule_type" class="form-select" required>
                <option value="single">Single Day</option>
                <option value="recurring">Recurring (365 Days)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="boat_id">Boat:</label>
            <select id="boat_id" name="boat_id" class="form-select" required>
                <option value="">Select Boat</option>
                <?php foreach ($boats as $boat): ?>
                    <option value="<?php echo $boat['boat_id']; ?>"><?php echo $boat['boat_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="route_id">Route:</label>
            <select id="route_id" name="route_id" class="form-select" required>
                <option value="">Select Route</option>
                <?php foreach ($routes as $route): ?>
                    <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="departure_time">Departure Time:</label>
            <input type="datetime-local" id="departure_time" name="departure_time" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="arrival_time">Arrival Time:</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" class="form-select" required>
                <option value="Scheduled">Scheduled</option>
                <option value="Unscheduled">Unscheduled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Schedule</button>
    </form>

          <!-- Search Schedules Form -->
          <form id="searchSchedulesForm" method="post">
            <div class="form-group">
                <label for="boat_id_search">Boat:</label>
                <select id="boat_id_search" name="boat_id" class="form-select">
                    <option value="">All Boats</option>
                    <?php foreach ($boats as $boat): ?>
                        <option value="<?php echo $boat['boat_id']; ?>"><?php echo $boat['boat_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="route_id_search">Route:</label>
                <select id="route_id_search" name="route_id" class="form-select">
                    <option value="">All Routes</option>
                    <?php foreach ($routes as $route): ?>
                        <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="departure_date_search">Departure Date:</label>
                <input type="date" id="departure_date_search" name="departure_date" class="form-control">
            </div>
            <button type="submit" name="search_schedules" class="btn btn-secondary">Search Schedules</button>
        </form>

        <div id="schedulesTable">
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Boat</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo $schedule['boat_name']; ?></td>
                                <td><?php echo $schedule['route_name']; ?></td>
                                <td><?php echo $schedule['departure_time']; ?></td>
                                <td><?php echo $schedule['arrival_time']; ?></td>
                                <td><?php echo $schedule['status']; ?></td>
                                <td>
                                    <button class="btn btn-info edit-button" data-id="<?php echo $schedule['schedule_id']; ?>">Edit</button>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="schedule_id" value="<?php echo $schedule['schedule_id']; ?>">
                                        <button class="btn btn-danger delete-button" data-id="<?php echo $schedule['schedule_id']; ?>">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No schedules found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editScheduleForm">
                    <input type="hidden" name="schedule_id" id="edit_schedule_id">
                    <div class="form-group">
                        <label for="edit_boat_id">Boat:</label>
                        <select id="edit_boat_id" name="boat_id" class="form-select" required>
                            <option value="">Select Boat</option>
                            <?php foreach ($boats as $boat): ?>
                                <option value="<?php echo $boat['boat_id']; ?>"><?php echo $boat['boat_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_route_id">Route:</label>
                        <select id="edit_route_id" name="route_id" class="form-select" required>
                            <option value="">Select Route</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>"><?php echo $route['route_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_departure_time">Departure Time:</label>
                        <input type="datetime-local" id="edit_departure_time" name="departure_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_arrival_time">Arrival Time:</label>
                        <input type="datetime-local" id="edit_arrival_time" name="arrival_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status:</label>
                        <select id="edit_status" name="status" class="form-select" required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Unscheduled">Unscheduled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Schedule</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>





