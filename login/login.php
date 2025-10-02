<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Flavo Spice Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/login.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation Header -->
    <div class="nav-header">
        <div class="nav-links">
            <span class="brand">flavo</span>
            <a href="../index.php">HOME</a>
            <a href="#shop">SHOP</a>
            <a href="#story">OUR STORY</a>
            <a href="#contact">CONTACT US</a>
        </div>
    </div>

    <div class="container" style="padding-top: 160px;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="main-container animate__animated animate__fadeInUp">
                    <div class="text-center mb-4">
                        <h1 style="font-size: 2.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dark);">Login</h1>
                        <p style="color: var(--text-light); font-weight: 500;">Welcome back to Flavo Spice Store</p>
                    </div>

                    <form method="POST" action="../actions/login_customer_action.php" class="mt-4" id="login-form">
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address <i class="fa fa-envelope"></i></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-custom w-100 mb-4">LOGIN TO ACCOUNT</button>
                    </form>

                    <div class="text-center">
                        <p style="color: var(--text-light);">
                            Don't have an account? 
                            <a href="register.php" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">
                                Register here
                            </a>
                        </p>
                        <a href="../index.php" style="color: var(--text-light); text-decoration: none; font-weight: 500;">
                            <i class="fa fa-arrow-left me-2"></i>Back to Home
                        </a>
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
