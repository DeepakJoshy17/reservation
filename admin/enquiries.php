<?php include 'headeradmin.php'; ?>
<?php include 'sidebar.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'db_connection.php';

// Fetch user complaints from the database
$query = "SELECT e.*, u.name as user_name FROM Enquiries e LEFT JOIN Users u ON e.user_id = u.user_id ORDER BY created_at DESC";
$result = $conn->query($query);
$enquiries = [];
while ($row = $result->fetch_assoc()) {
    $enquiries[] = $row;
}

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $enquiry_id = intval($_POST['enquiry_id']);
    $response = $conn->real_escape_string($_POST['response']);

    $updateQuery = "UPDATE Enquiries SET response = '$response', response_created_at = NOW(), response_status = 1 WHERE enquiry_id = $enquiry_id";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<div class='alert alert-success'>Response sent successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error sending response: " . $conn->error . "</div>";
    }
}

// Group enquiries by user_name
$grouped_enquiries = [];
foreach ($enquiries as $enquiry) {
    if ($enquiry['user_id'] !== null) {
        $grouped_enquiries['users'][$enquiry['user_name']][] = $enquiry; // Group by user name
    } else {
        $grouped_enquiries['common'][] = $enquiry;
    }
}
?>

<style>
    .direct-chat-msg {
        display: flex;
        align-items: flex-start; /* Aligns the user icon and message vertically */
        margin-bottom: 15px; /* Spacing between messages */
    }

    .direct-chat-infos {
        flex: 1; /* Takes up available space */
    }

    .direct-chat-name {
        font-weight: bold; /* Makes the username bold */
    }

    .direct-chat-text {
        background-color: #f8f9fa; /* Light background for the message */
        border-radius: 5px; /* Rounded corners */
        padding: 10px; /* Padding for the message text */
        margin-top: 5px; /* Space between name/timestamp and message */
    }

    .direct-chat-timestamp {
        font-size: 0.9em; /* Slightly smaller font for the timestamp */
        color: #6c757d; /* Muted color for the timestamp */
    }

    .direct-chat-msg i {
        margin-right: 10px; /* Space between icon and name */
        color: #007bff; /* Color for the user icon */
    }

    .response-text {
        background-color: #d1ecf1; /* Light background for the response */
        border-radius: 5px; /* Rounded corners */
        padding: 10px; /* Padding for the response text */
        margin-top: 5px; /* Space above the response */
    }

    .response-timestamp {
        font-size: 0.9em; /* Smaller font for the response timestamp */
        color: #6c757d; /* Muted color for the response timestamp */
    }

    .user-group {
        margin-top: 20px;
        border: 1px solid #007bff;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer; /* Cursor changes to pointer on hover */
    }

    .chat-details {
        display: none; /* Hide chat details by default */
        margin-top: 10px;
    }

    .notification {
        font-weight: bold;
        color: red; /* Highlight the notification count */
    }
</style>

<div class="content-wrapper">
    <div class="container mt-5">
        <h4 class="mb-4">User Enquiries</h4>

        <!-- Enquiries for users with user_id -->
        <?php if (!empty($grouped_enquiries['users'])): ?>
          
            <?php foreach ($grouped_enquiries['users'] as $user_name => $user_enquiries): ?>
                <?php
                    // Count enquiries with response_status = 0
                    $unanswered_count = count(array_filter($user_enquiries, function($enquiry) {
                        return $enquiry['response_status'] == 0;
                    }));
                ?>
                <div class="user-group" onclick="toggleChatDetails('user-<?= htmlspecialchars($user_name) ?>')">
                    <p>* <?= htmlspecialchars($user_name) ?> 
                        <span class="notification">(<?= $unanswered_count ?> enquiries)</span>
                </p>
                </div>
                <div id="user-<?= htmlspecialchars($user_name) ?>" class="chat-details">
                    <?php foreach ($user_enquiries as $enquiry): ?>
                        <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left">
                                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($enquiry['name']) ?>
                                </span>
                                <span class="direct-chat-timestamp float-right"><?= htmlspecialchars($enquiry['created_at']) ?></span>
                            </div>
                            <div class="direct-chat-text">
                                <?= htmlspecialchars($enquiry['message']) ?>
                            </div>
                            <!-- Display response if available -->
                            <?php if (!empty($enquiry['response'])): ?>
                                <div class="response-text">
                                    <?= htmlspecialchars($enquiry['response']) ?>
                                    <div class="response-timestamp float-right"><?= htmlspecialchars($enquiry['response_created_at']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Only show response form if no response has been sent -->
                        <?php if ($enquiry['response_status'] == 0): ?>
                            <form method="post" action="">
                                <input type="hidden" name="enquiry_id" value="<?= htmlspecialchars($enquiry['enquiry_id']) ?>">
                                <div class="form-group">
                                    <label for="response">Response:</label>
                                    <textarea class="form-control" name="response" rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Response</button>
                            </form>
                        <?php else: ?>
                            <p class="text-success">Response has already been sent.</p>
                        <?php endif; ?>
                        <hr>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Common enquiries without user_id -->
        <?php if (!empty($grouped_enquiries['common'])): ?>
            <h2>Common Enquiries</h2>
            <?php foreach ($grouped_enquiries['common'] as $enquiry): ?>
                <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-left">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($enquiry['email']) ?> (Common)
                        </span>
                        <span class="direct-chat-timestamp float-right"><?= htmlspecialchars($enquiry['created_at']) ?></span>
                    </div>
                    <div class="direct-chat-text">
                        <?= htmlspecialchars($enquiry['message']) ?>
                    </div>
                    <!-- Display response if available -->
                    <?php if (!empty($enquiry['response'])): ?>
                        <div class="response-text">
                            <?= htmlspecialchars($enquiry['response']) ?>
                            <div class="response-timestamp float-right"><?= htmlspecialchars($enquiry['response_created_at']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Only show response form if no response has been sent -->
                <?php if ($enquiry['response_status'] == 0): ?>
                    <form method="post" action="">
                        <input type="hidden" name="enquiry_id" value="<?= htmlspecialchars($enquiry['enquiry_id']) ?>">
                        <div class="form-group">
                            <label for="response">Response:</label>
                            <textarea class="form-control" name="response" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Response</button>
                    </form>
                <?php else: ?>
                    <p class="text-success">Response has already been sent.</p>
                <?php endif; ?>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($grouped_enquiries['users']) && empty($grouped_enquiries['common'])): ?>
            <p class="text-center">No enquiries found.</p>
        <?php endif; ?>
    </div>
</div>
<script>
    function toggleChatDetails(userId) {
        var chatDetails = document.getElementById(userId);
        if (chatDetails.style.display === "none" || chatDetails.style.display === "") {
            chatDetails.style.display = "block";
        } else {
            chatDetails.style.display = "none";
        }
    }
</script>

<?php include 'footer.php'; ?>


