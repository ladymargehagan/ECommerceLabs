/**
 * All Products Page JavaScript
 * Features: Pagination, filtering, product display, modal details
 */

$(document).ready(function() {
    // Initialize the page
    initializePage();
    
    // Filter functionality
    initializeFilters();
    
    // Pagination functionality
    initializePagination();
    
    // Product interactions
    initializeProductInteractions();
});

/**
 * Initialize the page
 */
function initializePage() {
    loadProducts(1);
    loadFilters();
    
    // Show loading state
    showLoading();
}

/**
 * Initialize filter functionality
 */
function initializeFilters() {
    // Category filter change
    $('#categoryFilter').on('change', function() {
        applyFilters();
    });
    
    // Brand filter change
    $('#brandFilter').on('change', function() {
        applyFilters();
    });
    
    // Sort filter change
    $('#sortFilter').on('change', function() {
        applyFilters();
    });
    
    // Clear filters
    $('#clearFilters').on('click', function() {
        clearAllFilters();
    });
}

/**
 * Initialize pagination functionality
 */
function initializePagination() {
    // Pagination click handler
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadProducts(page);
        }
    });
}

/**
 * Initialize product interactions
 */
function initializeProductInteractions() {
    // Product detail modal
    $(document).on('click', '.btn-view-product', function() {
        const productId = $(this).data('product-id');
        loadProductDetail(productId);
    });
    
    // Add to cart functionality
    $(document).on('click', '#addToCartBtn', function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    // Product image hover effects
    $(document).on('mouseenter', '.product-card-all', function() {
        $(this).find('.product-image-all').addClass('hover-effect');
    }).on('mouseleave', '.product-card-all', function() {
        $(this).find('.product-image-all').removeClass('hover-effect');
    });
}

/**
 * Load products with pagination
 */
function loadProducts(page = 1, filters = {}) {
    showLoading();
    
    const requestData = {
        action: 'get_products_paginated',
        page: page,
        limit: 10, // 10 products per page
        ...filters
    };
    
    console.log('Loading products with data:', requestData);
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        timeout: 10000, // 10 second timeout
        success: function(response) {
            hideLoading();
            console.log('Products response:', response);
            
            if (response.success) {
                displayProducts(response.data.products);
                updatePagination(response.data.pagination);
                updateResultsInfo(response.data.total, response.data.page);
            } else {
                console.error('Products API error:', response.message);
                showError('Failed to load products: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
            showError('An error occurred while loading products. Please check the console for details.');
        }
    });
}

/**
 * Load filters (categories and brands)
 */
function loadFilters() {
    // Load categories
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_categories' },
        dataType: 'json',
        timeout: 5000,
        success: function(response) {
            console.log('Categories response:', response);
            if (response.success) {
                populateCategoryFilter(response.data);
            } else {
                console.error('Categories API error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Categories AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
        }
    });
    
    // Load brands
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_brands' },
        dataType: 'json',
        timeout: 5000,
        success: function(response) {
            console.log('Brands response:', response);
            if (response.success) {
                populateBrandFilter(response.data);
            } else {
                console.error('Brands API error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Brands AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
        }
    });
}

/**
 * Populate category filter dropdown
 */
function populateCategoryFilter(categories) {
    const select = $('#categoryFilter');
    select.empty().append('<option value="all">All Categories</option>');
    
    categories.forEach(function(category) {
        select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
    });
}

/**
 * Populate brand filter dropdown
 */
function populateBrandFilter(brands) {
    const select = $('#brandFilter');
    select.empty().append('<option value="all">All Brands</option>');
    
    brands.forEach(function(brand) {
        select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
    });
}

/**
 * Apply filters
 */
function applyFilters() {
    const filters = {
        category: $('#categoryFilter').val() || 'all',
        brand: $('#brandFilter').val() || 'all',
        sort: $('#sortFilter').val() || 'name_asc'
    };
    
    loadProducts(1, filters);
}

/**
 * Clear all filters
 */
function clearAllFilters() {
    $('#categoryFilter').val('all');
    $('#brandFilter').val('all');
    $('#sortFilter').val('name_asc');
    
    loadProducts(1);
}

/**
 * Display products
 */
function displayProducts(products) {
    const container = $('#productsContainer');
    
    if (!products || products.length === 0) {
        container.html(`
            <div class="col-12">
                <div class="no-results text-center py-5">
                    <i class="fa fa-box fa-3x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">No products match your current filters</p>
                    <button class="btn btn-outline-primary" onclick="clearAllFilters()">
                        <i class="fa fa-refresh me-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '';
    products.forEach(function(product) {
        const imageSrc = getProductImagePath(product.product_image);
        const price = parseFloat(product.product_price).toFixed(2);
        const description = product.product_desc ? 
            product.product_desc.substring(0, 100) + (product.product_desc.length > 100 ? '...' : '') : 
            'No description available';
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card product-card-all h-100" data-product-id="${product.product_id}">
                    <div class="product-image-container">
                        <img src="${imageSrc}" class="product-image-all" alt="${product.product_title}" 
                             onerror="this.src='uploads/placeholder.png'" loading="lazy">
                        <div class="product-overlay">
                            <div class="product-actions">
                                <button class="btn btn-sm btn-outline-light me-1 btn-view-product" 
                                        data-product-id="${product.product_id}" 
                                        data-bs-toggle="tooltip" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-light btn-add-to-cart" 
                                        data-product-id="${product.product_id}"
                                        data-bs-toggle="tooltip" title="Add to Cart">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="product-id mb-1">
                            <small class="text-muted">ID: ${product.product_id}</small>
                        </div>
                        <h5 class="product-title">${product.product_title}</h5>
                        <div class="product-price mb-2">$${price}</div>
                        <div class="product-meta mb-2">
                            <small class="text-muted">
                                <i class="fa fa-tag me-1"></i>${product.cat_name || 'No Category'}
                            </small><br>
                            <small class="text-muted">
                                <i class="fa fa-star me-1"></i>${product.brand_name || 'No Brand'}
                            </small>
                        </div>
                        <p class="product-description text-muted small">${description}</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-view-product" data-product-id="${product.product_id}">
                                <i class="fa fa-eye me-2"></i>View Details
                            </button>
                            <button class="btn btn-outline-success btn-add-to-cart" data-product-id="${product.product_id}">
                                <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
    
    // Reinitialize tooltips for new elements
    $('[data-bs-toggle="tooltip"]').tooltip();
}

/**
 * Load product detail
 */
function loadProductDetail(productId) {
    showLoading();
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_product_detail', product_id: productId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayProductDetail(response.data);
                $('#productDetailModal').modal('show');
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
 * Display product detail in modal
 */
function displayProductDetail(product) {
    const imageSrc = getProductImagePath(product.product_image);
    const price = parseFloat(product.product_price).toFixed(2);
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="product-detail-image-container">
                    <img src="${imageSrc}" class="product-detail-image" 
                         alt="${product.product_title}" onerror="this.src='uploads/placeholder.png'">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-id mb-2">
                    <small class="text-muted">Product ID: ${product.product_id}</small>
                </div>
                <h3 class="mb-3">${product.product_title}</h3>
                <div class="product-price mb-3 fs-4 text-primary fw-bold">$${price}</div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-6">
                            <strong>Category:</strong><br>
                            <span class="text-muted">${product.cat_name || 'No Category'}</span>
                        </div>
                        <div class="col-6">
                            <strong>Brand:</strong><br>
                            <span class="text-muted">${product.brand_name || 'No Brand'}</span>
                        </div>
                    </div>
                </div>
                ${product.product_desc ? `
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="text-muted">${product.product_desc}</p>
                    </div>
                ` : ''}
                ${product.product_keywords ? `
                    <div class="mb-3">
                        <strong>Keywords:</strong>
                        <div class="mt-1">
                            ${product.product_keywords.split(',').map(keyword => 
                                `<span class="badge bg-secondary me-1">${keyword.trim()}</span>`
                            ).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    $('#productDetailContent').html(content);
    $('#addToCartBtn').data('product-id', product.product_id);
}

/**
 * Add to cart functionality
 */
function addToCart(productId) {
    // This would integrate with your cart system
    Swal.fire({
        icon: 'success',
        title: 'Added to Cart!',
        text: 'Product has been added to your cart',
        timer: 2000,
        showConfirmButton: false
    });
}

/**
 * Update pagination
 */
function updatePagination(pagination) {
    const container = $('#paginationContainer');
    
    if (pagination.total_pages > 1) {
        let html = '<nav><ul class="pagination justify-content-center">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="fa fa-chevron-left"></i> Previous
                </a>
            </li>`;
        }
        
        // Page numbers with ellipsis for large page counts
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
        
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            html += `<li class="page-item ${activeClass}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        
        if (endPage < pagination.total_pages) {
            if (endPage < pagination.total_pages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.total_pages}">${pagination.total_pages}</a></li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.total_pages) {
            html += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    Next <i class="fa fa-chevron-right"></i>
                </a>
            </li>`;
        }
        
        html += '</ul></nav>';
        container.html(html).show();
    } else {
        container.hide();
    }
}

/**
 * Update results info
 */
function updateResultsInfo(total, page) {
    $('#resultsCount').text(total);
    $('#currentPage').text(page);
    $('#resultsInfo').show();
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
window.clearAllFilters = clearAllFilters;
window.loadProducts = loadProducts;
