$(document).ready(function() {
    // Quantity increase button
    $(document).on('click', '.quantity-increase', function() {
        const input = $(this).siblings('.quantity-input');
        const currentVal = parseInt(input.val()) || 0;
        input.val(currentVal + 1);
        updateQuantity(input);
    });

    // Quantity decrease button
    $(document).on('click', '.quantity-decrease', function() {
        const input = $(this).siblings('.quantity-input');
        const currentVal = parseInt(input.val()) || 0;
        if (currentVal > 1) {
            input.val(currentVal - 1);
            updateQuantity(input);
        }
    });

    // Quantity input change
    $(document).on('change', '.quantity-input', function() {
        const val = parseInt($(this).val()) || 1;
        if (val < 1) {
            $(this).val(1);
        }
        updateQuantity($(this));
    });

    // Update quantity function
    function updateQuantity(input) {
        const productId = input.data('product-id');
        const quantity = parseInt(input.val()) || 1;

        if (quantity < 1) {
            Swal.fire({
                title: 'Invalid Quantity',
                text: 'Quantity must be at least 1.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            input.val(1);
            return;
        }

        // Show loading
        const cartItem = input.closest('.cart-item');
        cartItem.css('opacity', '0.6');
        cartItem.css('pointer-events', 'none');

        $.ajax({
            url: 'actions/update_quantity_action.php',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                cartItem.css('opacity', '1');
                cartItem.css('pointer-events', 'auto');

                if (response.success) {
                    // Reload page to update totals
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to update quantity.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    // Reload to get correct values
                    location.reload();
                }
            },
            error: function() {
                cartItem.css('opacity', '1');
                cartItem.css('pointer-events', 'auto');
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while updating quantity.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                location.reload();
            }
        });
    }

    // Remove item from cart
    $(document).on('click', '.remove-item', function() {
        const productId = $(this).data('product-id');
        const cartItem = $(this).closest('.cart-item');
        const productTitle = cartItem.find('h5').text();

        Swal.fire({
            title: 'Remove Item',
            text: `Are you sure you want to remove "${productTitle}" from your cart?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                cartItem.css('opacity', '0.6');
                cartItem.css('pointer-events', 'none');

                $.ajax({
                    url: 'actions/remove_from_cart_action.php',
                    method: 'POST',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Removed',
                                text: response.message || 'Item removed from cart.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            cartItem.css('opacity', '1');
                            cartItem.css('pointer-events', 'auto');
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to remove item.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        cartItem.css('opacity', '1');
                        cartItem.css('pointer-events', 'auto');
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while removing the item.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Empty cart
    $('#emptyCartBtn').on('click', function() {
        Swal.fire({
            title: 'Empty Cart',
            text: 'Are you sure you want to remove all items from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, empty cart',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'actions/empty_cart_action.php',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Cart Emptied',
                                text: response.message || 'All items removed from cart.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to empty cart.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while emptying the cart.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
});

