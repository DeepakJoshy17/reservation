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
    .chat-container {
        margin-top: 20px;
        border: 1px solid #3377AA;
        padding: 15px;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .user-group {
        cursor: pointer;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        background-color: #3377AA;
        color: #fff;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .user-group:hover {
        background-color: #0056b3;
    }

    .chat-details {
        display: none;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        background-color: #fff;
    }

    .direct-chat-msg {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
    }

    .direct-chat-msg .user-icon {
        margin-right: 10px;
    }

    .user-icon i {
        font-size: 24px;
        color: #3377AA;
    }

    .direct-chat-text,
    .response-text {
        max-width: 70%;
        padding: 10px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
    }

    .direct-chat-text {
        background-color: #f1f1f1;
        color: #333;
    }

    .response-text {
        background-color: #d1ecf1;
        color: #0c5460;
        align-self: flex-end;
    }

    .chat-timestamp {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    textarea {
        margin-top: 10px;
    }

    .btn {
        margin-top: 5px;
    }
</style>

<div class="content-wrapper">
    <div class="container mt-5">
        <h4 class="mb-4">User Enquiries</h4>

        <!-- Enquiries for users with user_id -->
        <?php if (!empty($grouped_enquiries['users'])): ?>
            <?php foreach ($grouped_enquiries['users'] as $user_name => $user_enquiries): ?>
                <?php
                    $unanswered_count = count(array_filter($user_enquiries, function($enquiry) {
                        return $enquiry['response_status'] == 0;
                    }));
                ?>
                <div class="chat-container">
                    <div class="user-group" onclick="toggleChatDetails('user-<?= htmlspecialchars($user_name) ?>')">
                        <?= htmlspecialchars($user_name) ?>
                        <span class="notification">(<?= $unanswered_count ?> enquiries pending)</span>
                    </div>

                    <div id="user-<?= htmlspecialchars($user_name) ?>" class="chat-details">
                        <?php foreach ($user_enquiries as $enquiry): ?>
                            <div class="direct-chat-msg">
                                <div class="user-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <div class="direct-chat-text">
                                        <?= htmlspecialchars($enquiry['message']) ?>
                                    </div>
                                    <div class="chat-timestamp">
                                        <?= htmlspecialchars($enquiry['created_at']) ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($enquiry['response'])): ?>
                                <div class="direct-chat-msg">
                                    <div class="response-text">
                                        <?= htmlspecialchars($enquiry['response']) ?>
                                    </div><br><br><br>
                                    <div class="chat-timestamp">
                                        <?= htmlspecialchars($enquiry['response_created_at']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($enquiry['response_status'] == 0): ?>
                                <form method="post" action="">
                                    <input type="hidden" name="enquiry_id" value="<?= htmlspecialchars($enquiry['enquiry_id']) ?>">
                                    <textarea class="form-control" name="response" rows="2" required></textarea>
                                    <button type="submit" class="btn btn-primary">Send Response</button>
                                </form>
                            <?php else: ?>
                                <p class="text-success">Response has already been sent.</p>
                            <?php endif; ?>

                            <hr>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($grouped_enquiries['common'])): ?>
            <h2>Common Enquiries</h2>
            <?php foreach ($grouped_enquiries['common'] as $enquiry): ?>
                <div class="chat-container">
                    <div class="direct-chat-msg">
                        <div class="user-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <div class="direct-chat-text">
                                <?= htmlspecialchars($enquiry['message']) ?>
                            </div>
                            <div class="chat-timestamp">
                                <?= htmlspecialchars($enquiry['created_at']) ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($enquiry['response'])): ?>
                        <div class="direct-chat-msg">
                            <div class="response-text">
                                <?= htmlspecialchars($enquiry['response']) ?>
                            </div><br><br><br>
                            <div class="chat-timestamp">
                                <?= htmlspecialchars($enquiry['response_created_at']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($enquiry['response_status'] == 0): ?>
                        <form method="post" action="">
                            <input type="hidden" name="enquiry_id" value="<?= htmlspecialchars($enquiry['enquiry_id']) ?>">
                            <textarea class="form-control" name="response" rows="2" required></textarea>
                            <button type="submit" class="btn btn-primary">Send Response</button>
                        </form>
                    <?php else: ?>
                        <p class="text-success">Response has already been sent.</p>
                    <?php endif; ?>
                </div>
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
        chatDetails.style.display = chatDetails.style.display === "block" ? "none" : "block";
    }
</script>