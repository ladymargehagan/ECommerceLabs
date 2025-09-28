$(document).ready(function() {
    // Validation patterns using regex
    const validationPatterns = {
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    };
    
    // Validation messages
    const validationMessages = {
        email: {
            required: 'Email address is required.',
            invalid: 'Please enter a valid email address.'
        },
        password: {
            required: 'Password is required.'
        }
    };
    
    // Real-time validation function
    function validateField(fieldName, value, showMessage = true) {
        const field = $(`#${fieldName}`);
        let isValid = false;
        
        if (fieldName === 'email') {
            isValid = validationPatterns[fieldName].test(value);
        } else if (fieldName === 'password') {
            isValid = value.length > 0;
        }
        
        // Remove existing validation classes and messages
        field.removeClass('is-valid is-invalid');
        field.siblings('.invalid-feedback').remove();
        
        if (value === '') {
            if (showMessage) {
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback">${validationMessages[fieldName].required}</div>`);
            }
            return false;
        } else if (!isValid) {
            if (showMessage) {
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback">${validationMessages[fieldName].invalid}</div>`);
            }
            return false;
        } else {
            field.addClass('is-valid');
            return true;
        }
    }
    
    // Comprehensive form validation
    function validateLoginForm() {
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        let isFormValid = true;
        
        // Validate email
        if (!validateField('email', email)) {
            isFormValid = false;
        }
        
        // Validate password
        if (!validateField('password', password)) {
            isFormValid = false;
        }
        
        return isFormValid;
    }
    
    // Handle login form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Clear any existing alerts
        $('.alert').remove();
        
        // Validate form before submission
        if (!validateLoginForm()) {
            showAlert('Please correct the errors below and try again.', 'error');
            return;
        }
        
        // Get form data
        var formData = {
            email: $('#email').val().trim(),
            password: $('#password').val()
        };
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Logging in...').prop('disabled', true);
        
        // Send AJAX request asynchronously
        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            beforeSend: function() {
                // Additional loading state
                $('input').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Login successful
                    showAlert(response.message, 'success');
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '../index.php';
                        }
                    }, 1500);
                } else {
                    // Login failed
                    showAlert(response.message, 'error');
                    resetForm();
                }
            },
            error: function(xhr, status, error) {
                // Handle different types of errors
                let errorMessage = 'An error occurred during login. Please try again.';
                
                if (status === 'timeout') {
                    errorMessage = 'Login request timed out. Please check your connection and try again.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Login service not found. Please contact support.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred. Please try again later.';
                } else if (xhr.status === 0) {
                    errorMessage = 'Network error. Please check your internet connection.';
                }
                
                console.error('Login error:', error, 'Status:', status, 'Response:', xhr.responseText);
                showAlert(errorMessage, 'error');
                resetForm();
            },
            complete: function() {
                // Re-enable form elements
                $('input').prop('disabled', false);
            }
        });
        
        // Function to reset form state
        function resetForm() {
            submitBtn.html(originalText).prop('disabled', false);
            $('input').prop('disabled', false);
        }
    });
    
    // Function to show alert messages
    function showAlert(message, type) {
        // Remove existing alerts
        $('.alert').remove();
        
        // Create alert element
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                       '<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' +
                       message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                       '</div>';
        
        // Insert alert before the form
        $('#login-form').before(alertHtml);
        
        // Auto-hide success messages after 3 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 3000);
        }
    }
    
    // Real-time validation on input events
    $('#email').on('blur keyup', function() {
        validateField('email', $(this).val().trim());
    });
    
    $('#password').on('blur keyup', function() {
        validateField('password', $(this).val());
    });
    
    // Clear validation on focus
    $('input').on('focus', function() {
        $(this).removeClass('is-invalid is-valid');
        $(this).siblings('.invalid-feedback').remove();
    });
    
    // Form reset functionality
    function resetLoginForm() {
        $('#login-form')[0].reset();
        $('input').removeClass('is-valid is-invalid');
        $('.invalid-feedback').remove();
        $('.alert').remove();
    }
});
