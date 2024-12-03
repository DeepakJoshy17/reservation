<?php
session_start();

// Establish connection to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'], $_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Verify user credentials
        $sql = "SELECT * FROM Users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stored_password = $user['password'];

            // Check if the provided password matches the stored password
            if (password_verify($password, $stored_password)) {
                if ($user['role'] === 'Admin') {
                    $_SESSION['admin_id'] = $user['user_id'];
                    header("Location: admin/index.php");
                } else if ($user['role'] === 'User') {
                    $_SESSION['user_id'] = $user['user_id'];
                    header("Location: userhome.php");
                } else {
                    $_SESSION['login_error'] = "Unknown role.";
                    header("Location: loginuserhtml.php");
                    exit();
                }
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid email or password.";
                header("Location: loginuserhtml.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
            header("Location: loginuserhtml.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "All fields are required.";
        header("Location: loginuserhtml.php");
        exit();
    }
}

// Close MySQL connection
$conn->close();
?>
