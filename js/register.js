$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        let name = $('#name').val();
        let email = $('#email').val();
        let password = $('#password').val();
        let phone_number = $('#phone_number').val();
        let role = $('input[name="role"]:checked').val();
        let country = $('#country').val();
        let city = $('#city').val();
        let image = $('#image')[0].files[0]; // file input

        // --- VALIDATION --- //
        if (!name || !email || !password || !phone_number || !country || !city) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Field length validation
        if (name.length > 100) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Name must be 100 characters or less!',
            });
            return;
        }

        if (email.length > 50) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Email must be 50 characters or less!',
            });
            return;
        }

        if (country.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Country must be 30 characters or less!',
            });
            return;
        }

        if (city.length > 30) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'City must be 30 characters or less!',
            });
            return;
        }

        // Email regex
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Password check - minimum 6 characters
        if (password.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters!',
            });
            return;
        }

        // Phone number digits only, max 15 digits
        let phoneRegex = /^[0-9]{7,15}$/;
        if (!phoneRegex.test(phone_number)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid phone number (7–15 digits)!',
            });
            return;
        }

        // Optional image validation
        if (image) {
            let allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(image.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Only JPG and PNG images are allowed!',
                });
                return;
            }
            if (image.size > 2 * 1024 * 1024) { // 2MB max
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Image must be less than 2MB!',
                });
                return;
            }
        }

        // --- PREPARE FORM DATA --- //
        let formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('phone_number', phone_number);
        formData.append('role', role);
        formData.append('country', country);
        formData.append('city', city);
        if (image) {
            formData.append('image', image);
        }

        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            data: formData,
            processData: false, // required for file upload
            contentType: false, // required for file upload
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});
