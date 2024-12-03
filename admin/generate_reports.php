<?php
include 'headeradmin.php';
include 'sidebar.php';
include 'db_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

function getBoatBookings($startDate, $endDate) {
    global $conn;
    $sql = "SELECT b.boat_name, COUNT(sb.booking_id) AS bookings_count
            FROM Seat_Bookings sb
            INNER JOIN Boats b ON sb.boat_id = b.boat_id
            WHERE sb.booking_date BETWEEN ? AND ?
            GROUP BY sb.boat_id
            ORDER BY bookings_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getScheduleBookings($startDate, $endDate) {
    global $conn;
    $sql = "SELECT CONCAT(b.boat_id, ' - ', r.route_id, ' - ', s.departure_time) AS schedule_name, COUNT(sb.booking_id) AS bookings_count
            FROM Seat_Bookings sb
            INNER JOIN Schedules s ON sb.schedule_id = s.schedule_id
            INNER JOIN Boats b ON s.boat_id = b.boat_id
            INNER JOIN Routes r ON s.route_id = r.route_id
            WHERE sb.booking_date BETWEEN ? AND ?
            GROUP BY s.schedule_id
            ORDER BY bookings_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSeatBookings($startDate, $endDate) {
    global $conn;
    $sql = "SELECT seat_number, COUNT(sb.booking_id) AS bookings_count
            FROM Seat_Bookings sb
            INNER JOIN Seats s ON sb.seat_id = s.seat_id
            WHERE sb.booking_date BETWEEN ? AND ?
            GROUP BY sb.seat_id
            ORDER BY bookings_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPopularStops($startDate, $endDate) {
    global $conn;
    $sql = "SELECT rs.location, COUNT(sb.booking_id) AS stops_count
            FROM Seat_Bookings sb
            INNER JOIN Route_Stops rs ON sb.start_stop_id = rs.stop_id OR sb.end_stop_id = rs.stop_id
            WHERE sb.booking_date BETWEEN ? AND ?
            GROUP BY rs.stop_id
            ORDER BY stops_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRouteUsage($startDate, $endDate) {
    global $conn;
    $sql = "SELECT r.route_name, COUNT(sb.booking_id) AS routes_count
            FROM Seat_Bookings sb
            INNER JOIN Route_Stops rs_start ON sb.start_stop_id = rs_start.stop_id
            INNER JOIN Route_Stops rs_end ON sb.end_stop_id = rs_end.stop_id
            INNER JOIN Routes r ON rs_start.route_id = r.route_id AND rs_end.route_id = r.route_id
            WHERE sb.booking_date BETWEEN ? AND ?
            GROUP BY r.route_id
            ORDER BY routes_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Define date range
$startDate = $_POST['start_date'] ?? '2024-01-01';
$endDate = $_POST['end_date'] ?? '2024-12-31';

$boatBookings = getBoatBookings($startDate, $endDate);
$scheduleBookings = getScheduleBookings($startDate, $endDate);
$seatBookings = getSeatBookings($startDate, $endDate);
$popularStops = getPopularStops($startDate, $endDate);
$routeUsage = getRouteUsage($startDate, $endDate);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .content-wrapper {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .chart-container {
            display: none;
            margin: 20px 0;
        }
        .active {
            display: block;
        }
        form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        form label {
            margin-right: 10px;
        }
        input[type="date"], select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        /* Add specific styles for canvas to control its size */
        canvas {
            max-width: 800px; /* Set max width for medium size */
            height: 100px; /* Set height for medium size */
        }


    </style>
</head>
<body>
<div class="content-wrapper">
<h1 class="mb-4">Booking Reports</h1>
    <div class="card">
        <div class="card-body">
            <form action="" method="POST">
                <div class="form-group ml-3 mr-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control custom" value="<?= htmlspecialchars($startDate) ?>">
                </div>
                <div class="form-group ml-3 mr-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control custom" value="<?= htmlspecialchars($endDate) ?>">
                </div>
                <div class="form-group ml-3 mr-3">
                    <label for="chartSelect">Select Report Type:</label>
                    <select id="chartSelect" class="form-select custom" onchange="handleChartSelection()">
                        <option value="boat">Most Booked Boats</option>
                        <option value="schedule">Most Booked Schedules</option>
                        <option value="seat">Most Booked Seats</option>
                        <option value="stop">Popular Stops</option>
                        <option value="route">Most Used Routes</option>
                    </select>
                </div>
                <button type="submit " class="btn btn-primary custom ml-3 mr-3">Generate Report</button>
            </form>
        </div>
        <div class="card-footer">
            <small>Fill in the dates and select a report type to generate your booking report.</small>

            <!-- Dropdown for Chart Type Selection -->
            <div class="form-group "> <!-- Added margin-top class for spacing -->
                <label for="chartTypeSelect">Select Chart Type:</label>
                <select id="chartTypeSelect" class="form-select custom" onchange="renderCharts()">
                    <option value="bar">Bar Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="line">Line Chart</option>
                </select>

    <!-- Boat Bookings Charts -->
    <div class="chart-container active" id="boatCharts">
        <h2>Most Booked Boats</h2>
        <canvas id="boatChart"></canvas>
    </div>

    <!-- Schedule Bookings Charts -->
    <div class="chart-container" id="scheduleCharts">
        <h2>Most Booked Schedules</h2>
        <canvas id="scheduleChart"></canvas>
    </div>

    <!-- Seat Bookings Charts -->
    <div class="chart-container" id="seatCharts">
        <h2>Most Booked Seats</h2>
        <canvas id="seatChart"></canvas>
    </div>

    <!-- Popular Stops Charts -->
    <div class="chart-container" id="stopCharts">
        <h2>Popular Stops</h2>
        <canvas id="stopChart"></canvas>
    </div>

    <!-- Route Usage Charts -->
    <div class="chart-container" id="routeCharts">
        <h2>Most Used Routes</h2>
        <canvas id="routeChart"></canvas>
    </div>
</div>

<script>
// Function to generate an array of distinct colors
function generateDistinctColors(numColors) {
    const colors = [];
    for (let i = 0; i < numColors; i++) {
        // Generate colors in HSL format
        const hue = (i * 360 / numColors) % 360; // Spread colors evenly around the hue circle
        const lightness = 50; // Mid lightness for visibility
        const saturation = 70; // Set saturation to avoid too pale colors
        colors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
    }
    return colors;
}

// Function to handle chart type selection
function handleChartSelection() {
    const chartType = document.getElementById('chartSelect').value;
    document.querySelectorAll('.chart-container').forEach(container => {
        container.classList.remove('active');
    });
    switch (chartType) {
        case 'boat':
            document.getElementById('boatCharts').classList.add('active');
            break;
        case 'schedule':
            document.getElementById('scheduleCharts').classList.add('active');
            break;
        case 'seat':
            document.getElementById('seatCharts').classList.add('active');
            break;
        case 'stop':
            document.getElementById('stopCharts').classList.add('active');
            break;
        case 'route':
            document.getElementById('routeCharts').classList.add('active');
            break;
    }
    renderCharts();
}

function generateDistinctColors(numColors) {
    const colors = [];
    for (let i = 0; i < numColors; i++) {
        const hue = (i * 360 / numColors) % 360; // Spread colors evenly around the hue circle
        colors.push(`hsl(${hue}, 70%, 50%)`); // Generate colors in HSL format
    }
    return colors;
}

function renderCharts() {
    const chartType = document.getElementById('chartTypeSelect').value;
    const selectedChart = document.getElementById('chartSelect').value;

    // Clear all charts first
    const charts = ['boat', 'schedule', 'seat', 'stop', 'route'];
    charts.forEach(chart => {
        const ctx = document.getElementById(`${chart}Chart`).getContext('2d');
        if (ctx.chart) ctx.chart.destroy();
    });

    // Data for the charts
    const data = {
        boat: {
            labels: <?php echo json_encode(array_column($boatBookings, 'boat_name')); ?>,
            datasets: [{
                label: 'Bookings Count',
                data: <?php echo json_encode(array_column($boatBookings, 'bookings_count')); ?>,
                backgroundColor: generateDistinctColors(<?php echo count($boatBookings); ?>),
                borderColor: generateDistinctColors(<?php echo count($boatBookings); ?>).map(color => color.replace('0.5', '1')), // Change transparency for border color
                borderWidth: 1
            }]
        },
        schedule: {
            labels: <?php echo json_encode(array_column($scheduleBookings, 'schedule_name')); ?>,
            datasets: [{
                label: 'Bookings Count',
                data: <?php echo json_encode(array_column($scheduleBookings, 'bookings_count')); ?>,
                backgroundColor: generateDistinctColors(<?php echo count($scheduleBookings); ?>),
                borderColor: generateDistinctColors(<?php echo count($scheduleBookings); ?>).map(color => color.replace('0.5', '1')),
                borderWidth: 1
            }]
        },
        seat: {
            labels: <?php echo json_encode(array_column($seatBookings, 'seat_number')); ?>,
            datasets: [{
                label: 'Bookings Count',
                data: <?php echo json_encode(array_column($seatBookings, 'bookings_count')); ?>,
                backgroundColor: generateDistinctColors(<?php echo count($seatBookings); ?>),
                borderColor: generateDistinctColors(<?php echo count($seatBookings); ?>).map(color => color.replace('0.5', '1')),
                borderWidth: 1
            }]
        },
        stop: {
            labels: <?php echo json_encode(array_column($popularStops, 'location')); ?>,
            datasets: [{
                label: 'Stops Count',
                data: <?php echo json_encode(array_column($popularStops, 'stops_count')); ?>,
                backgroundColor: generateDistinctColors(<?php echo count($popularStops); ?>),
                borderColor: generateDistinctColors(<?php echo count($popularStops); ?>).map(color => color.replace('0.5', '1')),
                borderWidth: 1
            }]
        },
        route: {
            labels: <?php echo json_encode(array_column($routeUsage, 'route_name')); ?>,
            datasets: [{
                label: 'Routes Count',
                data: <?php echo json_encode(array_column($routeUsage, 'routes_count')); ?>,
                backgroundColor: generateDistinctColors(<?php echo count($routeUsage); ?>),
                borderColor: generateDistinctColors(<?php echo count($routeUsage); ?>).map(color => color.replace('0.5', '1')),
                borderWidth: 1
            }]
        }
    };

    // Render the selected chart
    const ctx = document.getElementById(`${selectedChart}Chart`).getContext('2d');
    const chartConfig = {
        type: chartType,
        data: data[selectedChart],
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    enabled: true,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                },
            }
        }
    };
    ctx.chart = new Chart(ctx, chartConfig);
}


// Initial chart render
renderCharts();
</script>
</body>
</html>


