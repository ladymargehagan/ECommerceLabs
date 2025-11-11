<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teachers:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/login.css" rel="stylesheet">

    
    
    
    
    
</head>

<body>
    <div>
        <div>
            <div>
                <div>
                    <div>
                        <h4>Login</h4>
                    </div>
                    <div>
                        <!-- Alert Messages (To be handled by backend) -->
                        <!-- Example:
                        <div>Login successful!</div>
                        -->

                        <form method="POST" action="../actions/login_customer_action.php" id="login-form">
                            <div>
                                <label for="email">Email <i></i></label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div>
                                <label for="password">Password <i></i></label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <button type="submit">Login</button>
                        </form>
                    </div>
                    <div>
                        Don't have an account? <a href="register.php">Register here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js"></script>

    
</body>

</html>
