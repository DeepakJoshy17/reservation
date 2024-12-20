<?php
// userbooking.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Fetch all routes for selection
$routes_result = $conn->query("SELECT route_id, route_name FROM Routes");

// Fetch all stops initially
$stops_result = $conn->query("SELECT stop_id, location FROM Route_Stops");

// Initialize variables for form data
$schedule_date = '';
$start_stop_id = '';
$end_stop_id = '';
?>
<head>
<link rel="stylesheet" href="css/custom.css">
</head>
<br><br>
<div class="container mt-5">
    <h2 class="tm-text-primary mb-4 tm-section-title">Book Your Seats</h2>

    <!-- Search Form -->
    <form id="search-boats-form" method="post" action="search_boats_ajax.php" class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-3">
                <label for="schedule_date" class="form-label">Schedule Date:</label>
                <input type="date" id="schedule_date" name="schedule_date" class="form-control custom" value="<?= htmlspecialchars($schedule_date) ?>" required>
            </div>
            <div class="col-md-3">
                <label for="selected_route" class="form-label">Select Route:</label>
                <select id="selected_route" name="selected_route" class="form-select custom" required onchange="updateStopsDropdown()">
                    <option value="">-- Select Route --</option>
                    <?php while ($route = $routes_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($route['route_id']) ?>">
                            <?= htmlspecialchars($route['route_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_stop" class="form-label">Start Stop:</label>
                <select id="start_stop" name="start_stop" class="form-select custom" required>
                    <option value="">-- Select Start Stop --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="end_stop" class="form-label">End Stop:</label>
                <select id="end_stop" name="end_stop" class="form-select custom" required>
                    <option value="">-- Select End Stop --</option>
                </select>
            </div>
            <div class="col-md-3 d-flex justify-content-center align-items-end mt-3">
                <button type="submit" name="search_boats" class="btn btn-primary custom w-100">Search Boats</button>
            </div>
        </div>
    </form>

    <!-- Results Container -->
    <div id="search-results">
        <!-- AJAX results will be injected here -->
    </div>
</div>

<br><br>
<?php /*include "includes/footer.php";*/ ?>

<script src="js/jquery-1.9.1.min.js"></script>
<script src="slick/slick.min.js"></script>
<script src="magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="js/easing.min.js"></script>
<script src="js/jquery.singlePageNav.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
function updateStopsDropdown() {
    var selectedRouteId = $('#selected_route').val();

    if (selectedRouteId) {
        $.ajax({
            url: 'get_stops_ajax.php',
            type: 'POST',
            data: { route_id: selectedRouteId },
            success: function(response) {
                var data = JSON.parse(response);
                $('#start_stop').html(data.start_stops);
                $('#end_stop').html(data.end_stops);
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', status, error);
                $('#start_stop').html('<option value="">-- Select Start Stop --</option>');
                $('#end_stop').html('<option value="">-- Select End Stop --</option>');
            }
        });
    } else {
        $('#start_stop').html('<option value="">-- Select Start Stop --</option>');
        $('#end_stop').html('<option value="">-- Select End Stop --</option>');
    }
}
</script>

</body>

</html>
