<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>
<?php 
//session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admindashboard.php");
    exit;
}

include 'db_connection.php';

// Fetch boats for selection
$boats = $conn->query("SELECT * FROM Boats");
$selected_boat_id = isset($_GET['boat_id']) ? (int)$_GET['boat_id'] : 0;
$seats = $selected_boat_id ? $conn->query("SELECT * FROM Seats WHERE boat_id = $selected_boat_id") : [];
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <h1 class="mb-4">Admin Seat Management</h1>

        <!-- Boat Selection Form -->
        <form method="get" action="" class="mb-4">
            <div class="mb-3">
                <label for="boat_id" class="form-label">Select Boat:</label>
                <select id="boat_id" name="boat_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Boat --</option>
                    <?php while ($boat = $boats->fetch_assoc()): ?>
                        <option value="<?= $boat['boat_id'] ?>" <?= $boat['boat_id'] == $selected_boat_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($boat['boat_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <?php if ($selected_boat_id): ?>
            <h2 class="mb-4">Seats for Boat ID <?= $selected_boat_id ?></h2>

            <button class="btn btn-success mb-3" onclick="addMaxCapacity(<?= $selected_boat_id ?>)">Add Seats to Max Capacity</button>

            <div class="boat-container">
                <div class="seat-layout">
                    <?php while ($seat = $seats->fetch_assoc()): ?>
                        <div class="seat" data-seat-id="<?= $seat['seat_id'] ?>" onclick="viewSeatDetails(<?= $seat['seat_id'] ?>)">
                            <?= htmlspecialchars($seat['seat_number']) ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Modal for Seat Details -->
            <div id="seatModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeSeatModal()">&times;</span>
                    <h2>Seat Details</h2>
                    <form id="seatForm">
                        <input type="hidden" name="seat_id" id="modal_seat_id">
                        <div class="mb-3">
                            <label for="modal_seat_number" class="form-label">Seat Number:</label>
                            <input type="text" id="modal_seat_number" name="seat_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_type" class="form-label">Seat Type:</label>
                            <input type="text" id="modal_type" name="type" class="form-control" required>
                        </div>
                        <button type="button" onclick="updateSeat()" class="btn btn-warning">Save Changes</button>
                        <button type="button" onclick="deleteSeat()" class="btn btn-danger">Delete Seat</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Styling for the boat container and seats */
    .boat-container {
  position: relative;
  width: 80%;
  max-width: 600px;
  margin: 0 auto;
  background: #f5f5f5;
  padding: 30px;
  padding-top: 120px;
  padding-bottom: 50px;
  text-align: center;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  overflow: hidden;
  border-radius: 50% 50% 0 0;
}

.boat-name {
  background: #15b9d9;
  color: white;
  padding: 5px;
  margin-top: 20px;
  border-radius: 5px;
  font-size: 1.5em;
  font-weight: bold;
  position: absolute;
  top: 30px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}

.seat-layout {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 50px;
}

.seat {
  display: inline-block;
  width: 40px;
  height: 40px;
  margin: 5px;
  cursor: pointer;
  position: relative;
  border: 2px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
  line-height: 40px;
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.seat:hover {
  transform: scale(1.1);
  background-color: #202428;
  color: white;
  font-weight: bold;
}

.seat.selected {
  background-color: #38c4df;
  color: white;
  font-weight: bold;
}

/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: white;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 300px;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  cursor: pointer;
}
</style>

<script>
    function viewSeatDetails(seatId) {
        fetch(`get_seat_details.php?seat_id=${seatId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('modal_seat_id').value = data.seat_id;
                    document.getElementById('modal_seat_number').value = data.seat_number;
                    document.getElementById('modal_type').value = data.type;
                    document.getElementById('seatModal').style.display = "block";
                }
            });
    }

    function updateSeat() {
        const formData = new FormData(document.getElementById('seatForm'));
        fetch('update_seat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            alert(result);
            closeSeatModal();
            location.reload(); // Reload to see the updates
        });
    }

    function deleteSeat() {
        const seatId = document.getElementById('modal_seat_id').value;
        fetch(`delete_seat.php?seat_id=${seatId}`)
            .then(response => response.text())
            .then(result => {
                alert(result);
                closeSeatModal();
                location.reload(); // Reload to see the updates
            });
    }

    function addMaxCapacity(boatId) {
        fetch(`add_max_capacity.php?boat_id=${boatId}`)
            .then(response => response.text())
            .then(result => {
                alert(result);
                location.reload(); // Reload to see the updates
            });
    }

    function closeSeatModal() {
        document.getElementById('seatModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === document.getElementById('seatModal')) {
            closeSeatModal();
        }
    }
</script>

<?php include 'footer.php'; ?>



