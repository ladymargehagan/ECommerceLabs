<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Taste of Africa</title>
    
    
    
    
    
</head>

<body>
    <div>
        <div>
            <div>
                <div>
                    <div>
                        <h4>Register</h4>
                    </div>
                    <div>
                        <form method="POST" action="" id="register-form" enctype="multipart/form-data">
                            <div>
                                <label for="name">Name <i></i></label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div>
                                <label for="email">Email <i></i></label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div>
                                <label for="password">Password <i></i></label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <div>
                                <label for="phone_number">Phone Number <i></i></label>
                                <input type="text" id="phone_number" name="phone_number" required>
                            </div>
                            <!-- Added Country -->
                            <div>
                                <label for="country">Country <i></i></label>
                                <input type="text" id="country" name="country" required>
                            </div>
                            <!-- Added City -->
                            <div>
                                <label for="city">City <i></i></label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <!-- Added Profile Image -->
                            <div>
                                <label for="image">Profile Image (optional) <i></i></label>
                                <input type="file" id="image" name="image">
                            </div>
                            <div>
                                <label>Register As</label>
                                <div>
                                    <div>
                                        <input type="radio" name="role" id="customer" value="2" checked>
                                        <label for="customer">Customer</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="role" id="admin" value="1">
                                        <label for="admin">Admin</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit">Register</button>
                        </form>
                    </div>
                    <div>
                        Already have an account? <a href="login.php">Login here</a>.
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
