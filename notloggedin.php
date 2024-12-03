<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Logged In</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: white; /* White background for the container */
            color: #333; /* Dark text for contrast */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .alert {
            background-color: #ffeeba; /* Lighter yellow for alert */
            color: #856404; /* Darker text for contrast */
        }
        .btn-success {
            background-color: #28a745; /* Green success color */
            border: none;
        }
        .btn-success:hover {
            background-color: #218838; /* Darker green on hover */
        }
        footer {
            margin-top: 20px;
            color: #666; /* Muted footer text */
        }
    </style>
</head>

<body>
    <div class="container mt-5 text-center">
        <div class="alert alert-warning" role="alert">
            <strong>Access Denied!</strong> You must be logged in to book seats.
        </div>
        <h2 class="mb-4">You need to log in to proceed with payment</h2>
        <h2 class="lead">Please log in to continue with your booking.</h2>
        <a href="loginuserhtml.php" class="btn btn-success btn-lg">Log In</a> <!-- Success button -->
        <p class="mt-4">Don't have an account? <a href="signupuserhtml.php" class="text-primary">Create one here</a>.</p>
        <footer class="mt-5">
            <p>© 2024 Your Waterway. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>



