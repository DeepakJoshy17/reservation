<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe-Style Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="background-overlay"></div>
    <div class="login-container">
        <h2>Sign in to your account</h2>
        <form>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" required>
            </div>
            <div class="options">
                <label><input type="checkbox" checked> Remember me on this device</label>
                <a href="#">Forgot your password?</a>
            </div>
            <button type="submit">Sign in</button>
            <p class="additional-links">
                <a href="#">Sign in with passkey</a> | <a href="#">Use single sign-on (SSO)</a>
            </p>
            <p class="create-account">New to the site? <a href="#">Create an account</a></p>
        </form>
    </div>
</body>
</html>





