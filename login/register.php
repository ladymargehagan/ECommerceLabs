<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Flavo Spice Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/register.css" rel="stylesheet">
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
            <div class="col-md-8">
                <div class="main-container animate__animated animate__fadeInUp">
                    <div class="text-center mb-4">
                        <h1 style="font-size: 2.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dark);">Register</h1>
                        <p style="color: var(--text-light); font-weight: 500;">Join the Flavo Spice Store community</p>
                    </div>

                    <form method="POST" action="" class="mt-4" id="register-form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label">Full Name <i class="fa fa-user"></i></label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label">Email Address <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="phone_number" class="form-label">Phone Number <i class="fa fa-phone"></i></label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required placeholder="Enter phone number">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="country" class="form-label">Country <i class="fa fa-flag"></i></label>
                                <input type="text" class="form-control" id="country" name="country" required placeholder="Enter your country">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="city" class="form-label">City <i class="fa fa-city"></i></label>
                                <input type="text" class="form-control" id="city" name="city" required placeholder="Enter your city">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="image" class="form-label">Profile Image (Optional) <i class="fa fa-image"></i></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Account Type</label>
                            <div class="d-flex justify-content-start gap-4 mt-2">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="role" id="customer" value="2" checked>
                                    <label class="form-check-label" for="customer" style="font-weight: 600; color: var(--text-dark);">Customer</label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="role" id="admin" value="1">
                                    <label class="form-check-label" for="admin" style="font-weight: 600; color: var(--text-dark);">Admin</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-custom w-100 mb-4">CREATE ACCOUNT</button>
                    </form>

                    <div class="text-center">
                        <p style="color: var(--text-light);">
                            Already have an account? 
                            <a href="login.php" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">
                                Login here
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
    <script src="../js/register.js"></script>
</body>

</html>
