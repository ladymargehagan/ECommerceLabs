/**
 * Single Product Page JavaScript
 * Features: Product details, related products, add to cart
 */

$(document).ready(function() {
    // Initialize the page
    initializePage();
    
    // Product interactions
    initializeProductInteractions();
});

/**
 * Initialize the page
 */
function initializePage() {
    if (typeof PRODUCT_ID !== 'undefined' && PRODUCT_ID > 0) {
        loadProductDetails(PRODUCT_ID);
        loadRelatedProducts(PRODUCT_ID);
    } else {
        showError('Invalid product ID');
    }
}

/**
 * Initialize product interactions
 */
function initializeProductInteractions() {
    // Add to cart button
    $(document).on('click', '.btn-add-to-cart', function() {
        const productId = $(this).data('product-id');
        showAddToCartModal(productId);
    });
    
    // Confirm add to cart
    $('#confirmAddToCart').on('click', function() {
        const productId = $(this).data('product-id');
        const quantity = $('#quantity').val();
        addToCart(productId, quantity);
    });
    
    // Related product click
    $(document).on('click', '.related-product-card', function() {
        const productId = $(this).data('product-id');
        window.location.href = `single_product.php?id=${productId}`;
    });
    
    // Product image hover effects
    $(document).on('mouseenter', '.related-product-card', function() {
        $(this).find('.related-product-image').addClass('hover-effect');
    }).on('mouseleave', '.related-product-card', function() {
        $(this).find('.related-product-image').removeClass('hover-effect');
    });
}

/**
 * Load product details
 */
function loadProductDetails(productId) {
    showLoading();
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_product_detail', product_id: productId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayProductDetails(response.data);
            } else {
                showError('Failed to load product details: ' + response.message);
            }
        },
        error: function() {
            hideLoading();
            showError('An error occurred while loading product details');
        }
    });
}

/**
 * Display product details
 */
function displayProductDetails(product) {
    const imageSrc = getProductImagePath(product.product_image);
    const price = parseFloat(product.product_price).toFixed(2);
    
    // Update breadcrumb
    $('#breadcrumbProduct').text(product.product_title);
    
    // Update page title
    document.title = `${product.product_title} - Taste of Africa`;
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="product-main-image-container">
                    <img src="${imageSrc}" class="product-main-image" 
                         alt="${product.product_title}" onerror="this.src='uploads/placeholder.png'">
                    <div class="product-image-overlay">
                        <button class="btn btn-light btn-sm" onclick="zoomImage('${imageSrc}')">
                            <i class="fa fa-search-plus"></i> Zoom
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-info">
                    <div class="product-id mb-2">
                        <small class="text-muted">Product ID: ${product.product_id}</small>
                    </div>
                    <h1 class="product-title mb-3">${product.product_title}</h1>
                    <div class="product-price mb-4">
                        <span class="price-current fs-2 text-primary fw-bold">$${price}</span>
                    </div>
                    
                    <div class="product-meta mb-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="meta-item">
                                    <strong><i class="fa fa-tag text-primary me-2"></i>Category:</strong>
                                    <p class="text-muted mb-0">${product.cat_name || 'No Category'}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="meta-item">
                                    <strong><i class="fa fa-star text-warning me-2"></i>Brand:</strong>
                                    <p class="text-muted mb-0">${product.brand_name || 'No Brand'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${product.product_desc ? `
                        <div class="product-description mb-4">
                            <h5><i class="fa fa-info-circle text-info me-2"></i>Description</h5>
                            <p class="text-muted">${product.product_desc}</p>
                        </div>
                    ` : ''}
                    
                    ${product.product_keywords ? `
                        <div class="product-keywords mb-4">
                            <h5><i class="fa fa-tags text-success me-2"></i>Keywords</h5>
                            <div class="keywords-container">
                                ${product.product_keywords.split(',').map(keyword => 
                                    `<span class="badge bg-secondary me-1 mb-1">${keyword.trim()}</span>`
                                ).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="product-actions">
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-lg w-100 btn-add-to-cart" 
                                        data-product-id="${product.product_id}">
                                    <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-danger btn-lg w-100 btn-wishlist" 
                                        data-product-id="${product.product_id}">
                                    <i class="fa fa-heart me-2"></i>Add to Wishlist
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#productDetailsContainer').html(content);
}

/**
 * Load related products
 */
function loadRelatedProducts(productId) {
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { 
            action: 'get_related_products', 
            product_id: productId,
            category_id: 0, // Will be updated after product details load
            limit: 4
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayRelatedProducts(response.data);
            }
        },
        error: function() {
            console.error('Failed to load related products');
        }
    });
}

/**
 * Display related products
 */
function displayRelatedProducts(products) {
    const container = $('#relatedProductsContainer');
    
    if (!products || products.length === 0) {
        container.html(`
            <div class="col-12 text-center py-4">
                <p class="text-muted">No related products found</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    products.forEach(function(product) {
        const imageSrc = getProductImagePath(product.product_image);
        const price = parseFloat(product.product_price).toFixed(2);
        
        html += `
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card related-product-card h-100" data-product-id="${product.product_id}">
                    <div class="related-product-image-container">
                        <img src="${imageSrc}" class="related-product-image" 
                             alt="${product.product_title}" onerror="this.src='uploads/placeholder.png'">
                        <div class="related-product-overlay">
                            <button class="btn btn-sm btn-outline-light btn-view-related" 
                                    data-product-id="${product.product_id}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">${product.product_title}</h6>
                        <div class="text-primary fw-bold">$${price}</div>
                        <small class="text-muted">${product.cat_name || 'No Category'}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

/**
 * Show add to cart modal
 */
function showAddToCartModal(productId) {
    // Get product details for modal
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_product_detail', product_id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const product = response.data;
                const imageSrc = getProductImagePath(product.product_image);
                const price = parseFloat(product.product_price).toFixed(2);
                
                $('#cartProductImage').attr('src', imageSrc);
                $('#cartProductTitle').text(product.product_title);
                $('#cartProductPrice').text(price);
                $('#quantity').val(1);
                $('#confirmAddToCart').data('product-id', productId);
                
                $('#addToCartModal').modal('show');
            }
        }
    });
}

/**
 * Add to cart functionality
 */
function addToCart(productId, quantity = 1) {
    // This would integrate with your cart system
    const totalPrice = parseFloat($('#cartProductPrice').text()) * quantity;
    
    Swal.fire({
        icon: 'success',
        title: 'Added to Cart!',
        html: `
            <p>Product: ${$('#cartProductTitle').text()}</p>
            <p>Quantity: ${quantity}</p>
            <p>Total: $${totalPrice.toFixed(2)}</p>
        `,
        timer: 3000,
        showConfirmButton: false
    });
    
    $('#addToCartModal').modal('hide');
}

/**
 * Toggle wishlist
 */
function toggleWishlist(productId) {
    const $btn = $(`.btn-wishlist[data-product-id="${productId}"]`);
    const $icon = $btn.find('i');
    
    if ($icon.hasClass('fa-heart-o')) {
        $icon.removeClass('fa-heart-o').addClass('fa-heart');
        $btn.addClass('text-danger');
        Swal.fire({
            icon: 'success',
            title: 'Added to Wishlist!',
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        $icon.removeClass('fa-heart').addClass('fa-heart-o');
        $btn.removeClass('text-danger');
        Swal.fire({
            icon: 'info',
            title: 'Removed from Wishlist',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

/**
 * Zoom image functionality
 */
function zoomImage(imageSrc) {
    // Create a modal for image zoom
    const zoomModal = `
        <div class="modal fade" id="imageZoomModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageSrc}" class="img-fluid" alt="Product Image">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#imageZoomModal').remove();
    
    // Add modal to body
    $('body').append(zoomModal);
    
    // Show modal
    $('#imageZoomModal').modal('show');
    
    // Remove modal when closed
    $('#imageZoomModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Get proper product image path
 */
function getProductImagePath(imagePath) {
    if (!imagePath) {
        return 'uploads/placeholder.png';
    }
    
    // If image path already includes uploads/, use as is
    if (imagePath.includes('uploads/')) {
        return imagePath;
    }
    
    // If it's just a filename, assume it's in the product folder
    if (!imagePath.includes('/')) {
        return `uploads/product/${imagePath}`;
    }
    
    // Otherwise, use the path as provided
    return imagePath;
}

/**
 * Utility functions
 */
function showLoading() {
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonText: 'OK'
    });
}

function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

// Export functions for global access
window.zoomImage = zoomImage;
window.toggleWishlist = toggleWishlist;
