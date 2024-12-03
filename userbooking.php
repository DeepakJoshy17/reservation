<?php
// userbooking.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Fetch all stops for selection
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
    <h1 class="mb-4">Book Your Seats</h1>

    <!-- Search Form -->
    <form id="search-boats-form" method="post" action="search_boats_ajax.php" class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-3">
                <label for="schedule_date" class="form-label">Schedule Date:</label>
                <input type="date" id="schedule_date" name="schedule_date" class="form-control custom" value="<?= htmlspecialchars($schedule_date) ?>" required>
            </div>
            <div class="col-md-3">
                <label for="start_stop" class="form-label">Start Stop:</label>
                <select id="start_stop" name="start_stop" class="form-select custom" required>
                    <option value="">-- Select Start Stop --</option>
                    <?php while ($stop = $stops_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($stop['stop_id']) ?>" <?= $stop['stop_id'] == $start_stop_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($stop['location']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="end_stop" class="form-label">End Stop:</label>
                <select id="end_stop" name="end_stop" class="form-select custom" required>
                    <option value="">-- Select End Stop --</option>
                    <?php
                    // Reset pointer for the stops query
                    $stops_result->data_seek(0);
                    while ($stop = $stops_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($stop['stop_id']) ?>" <?= $stop['stop_id'] == $end_stop_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($stop['location']) ?>
                        </option>
                    <?php endwhile; ?>
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

</body>

</html>
