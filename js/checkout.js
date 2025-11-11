$(document).ready(function() {
    // Simulate Payment Button
    $('#simulatePaymentBtn').on('click', function() {
        // Show payment confirmation modal
        Swal.fire({
            title: 'Simulate Payment',
            html: `
                <div class="text-center">
                    <i class="fa fa-credit-card fa-3x text-primary mb-3"></i>
                    <p class="mb-3">This is a simulated payment process.</p>
                    <p class="text-muted small">Click "Yes, I've paid" to complete the order.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, I\'ve paid',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: 'actions/process_checkout_action.php',
                    method: 'POST',
                    dataType: 'json'
                }).then(function(response) {
                    return response;
                }).catch(function(error) {
                    Swal.showValidationMessage('Request failed: ' + error.statusText);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const response = result.value;
                
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Payment Successful!',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                                <h4>Thank you for your purchase!</h4>
                                <p class="mb-2"><strong>Order Reference:</strong> ${response.order_reference || 'N/A'}</p>
                                <p class="mb-2"><strong>Order ID:</strong> #${response.order_id || 'N/A'}</p>
                                <p class="mb-3"><strong>Total Amount:</strong> $${parseFloat(response.total_amount || 0).toFixed(2)}</p>
                                <p class="text-muted small">You will receive an email confirmation shortly.</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'View Orders',
                        allowOutsideClick: false
                    }).then(() => {
                        // Redirect to customer dashboard or orders page
                        window.location.href = 'customer/dashboard.php';
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        title: 'Payment Failed',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-times-circle fa-4x text-danger mb-3"></i>
                                <p>${response.message || 'An error occurred during checkout.'}</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    
                    // If redirect is needed (e.g., login required)
                    if (response.redirect) {
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 2000);
                    }
                }
            }
        });
    });
});

