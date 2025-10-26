/**
 * Enhanced Product Customer JavaScript
 * Features: Dynamic dropdowns, async search, advanced filtering, proper image handling
 */

$(document).ready(function() {
    // Initialize the page
    initializePage();
    
    // Search functionality with debouncing
    initializeSearch();
    
    // Dynamic filter dropdowns
    initializeDynamicFilters();
    
    // Advanced filtering
    initializeAdvancedFilters();
    
    // Product interactions
    initializeProductInteractions();
    
    // Price range slider
    initializePriceRange();
});

/**
 * Initialize the page
 */
function initializePage() {
    loadProducts();
    loadFilters();
    loadFeaturedProducts();
    
    // Show loading state
    showLoading();
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
}

/**
 * Initialize search functionality with debouncing
 */
function initializeSearch() {
    let searchTimeout;
    
    // Real-time search with debouncing
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Show search suggestions if there's input
        if (searchTerm.length > 0) {
            showSearchSuggestions(searchTerm);
        } else {
            hideSearchSuggestions();
        }
        
        // Debounce the actual search
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 2) {
                performSearch(searchTerm);
            } else if (searchTerm.length === 0) {
                loadProducts();
                hideSearchSuggestions();
            }
        }, 300); // 300ms delay
    });
    
    // Search button click
    $('#searchBtn').on('click', function() {
        const searchTerm = $('#searchInput').val().trim();
        if (searchTerm) {
            performSearch(searchTerm);
        }
    });
    
    // Enter key search
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const searchTerm = $(this).val().trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        }
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#searchInput, #searchSuggestions').length) {
            hideSearchSuggestions();
        }
    });
}

/**
 * Initialize dynamic filter dropdowns
 */
function initializeDynamicFilters() {
    // Category dropdown with search
    $('#categoryDropdown').on('show.bs.dropdown', function() {
        loadCategoryDropdown();
    });
    
    // Brand dropdown with search
    $('#brandDropdown').on('show.bs.dropdown', function() {
        loadBrandDropdown();
    });
    
    // Category filter change
    $(document).on('change', '#categorySelect', function() {
        const categoryId = $(this).val();
        applyCategoryFilter(categoryId);
    });
    
    // Brand filter change
    $(document).on('change', '#brandSelect', function() {
        const brandId = $(this).val();
        applyBrandFilter(brandId);
    });
    
    // Multi-select functionality
    initializeMultiSelect();
}

/**
 * Initialize advanced filtering
 */
function initializeAdvancedFilters() {
    // Sort functionality
    $('#sortSelect').on('change', function() {
        applyFilters();
    });
    
    // Price range filtering
    $('#priceRangeMin, #priceRangeMax').on('input', function() {
        debounceFilter();
    });
    
    // Clear all filters
    $('#clearFilters').on('click', function() {
        clearAllFilters();
    });
    
    // Filter by availability
    $('#availabilityFilter').on('change', function() {
        applyFilters();
    });
    
    // Filter by rating (if implemented)
    $('.rating-filter').on('click', function() {
        const rating = $(this).data('rating');
        applyRatingFilter(rating);
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
    $(document).on('mouseenter', '.product-card-customer', function() {
        $(this).find('.product-image-customer').addClass('hover-effect');
    }).on('mouseleave', '.product-card-customer', function() {
        $(this).find('.product-image-customer').removeClass('hover-effect');
    });
    
    // Wishlist functionality (if implemented)
    $(document).on('click', '.btn-wishlist', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        toggleWishlist(productId);
    });
}

/**
 * Initialize price range slider
 */
function initializePriceRange() {
    const priceSlider = $('#priceRangeSlider');
    
    if (priceSlider.length) {
        priceSlider.slider({
            range: true,
            min: 0,
            max: 1000,
            values: [0, 1000],
            slide: function(event, ui) {
                $('#priceRangeMin').val(ui.values[0]);
                $('#priceRangeMax').val(ui.values[1]);
                debounceFilter();
            }
        });
    }
}

/**
 * Load products with pagination
 */
function loadProducts(page = 1, filters = {}) {
    showLoading();
    
    const requestData = {
        action: 'get_products',
        page: page,
        ...filters
    };
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayProducts(response.data.products);
                updatePagination(response.data.pagination);
                updateResultsInfo(response.data.total, response.data.page);
                updateFilterCounts(response.data.filter_counts);
            } else {
                showError('Failed to load products: ' + response.message);
            }
        },
        error: function() {
            hideLoading();
            showError('An error occurred while loading products');
        }
    });
}

/**
 * Load filters dynamically
 */
function loadFilters() {
    // Load categories
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_categories' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateCategoryFilters(response.data);
                populateCategoryDropdown(response.data);
            }
        }
    });
    
    // Load brands
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_brands' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateBrandFilters(response.data);
                populateBrandDropdown(response.data);
            }
        }
    });
}

/**
 * Load featured products
 */
function loadFeaturedProducts() {
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_featured_products', limit: 6 },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayFeaturedProducts(response.data);
            }
        }
    });
}

/**
 * Perform search with enhanced functionality
 */
function performSearch(searchTerm) {
    showLoading();
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { 
            action: 'search_products', 
            search: searchTerm,
            include_suggestions: true
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayProducts(response.data.products);
                updateResultsInfo(response.data.total, 1);
                $('#resultsInfo').removeClass('results-info-hidden');
                
                // Show search suggestions if available
                if (response.data.suggestions) {
                    showSearchSuggestions(searchTerm, response.data.suggestions);
                }
            } else {
                showError('Search failed: ' + response.message);
            }
        },
        error: function() {
            hideLoading();
            showError('An error occurred during search');
        }
    });
}

/**
 * Show search suggestions
 */
function showSearchSuggestions(searchTerm, suggestions = null) {
    if (!suggestions) {
        // Generate basic suggestions from current filters
        suggestions = generateBasicSuggestions(searchTerm);
    }
    
    const suggestionsHtml = suggestions.map(suggestion => 
        `<div class="suggestion-item" data-suggestion="${suggestion}">
            <i class="fa fa-search me-2"></i>${suggestion}
        </div>`
    ).join('');
    
    $('#searchSuggestions').html(suggestionsHtml).show();
    
    // Handle suggestion clicks
    $('.suggestion-item').on('click', function() {
        const suggestion = $(this).data('suggestion');
        $('#searchInput').val(suggestion);
        performSearch(suggestion);
        hideSearchSuggestions();
    });
}

/**
 * Hide search suggestions
 */
function hideSearchSuggestions() {
    $('#searchSuggestions').hide();
}

/**
 * Generate basic suggestions
 */
function generateBasicSuggestions(searchTerm) {
    const suggestions = [];
    const categories = $('.filter-btn[data-category]').map(function() {
        return $(this).text();
    }).get();
    
    const brands = $('.filter-btn[data-brand]').map(function() {
        return $(this).text();
    }).get();
    
    // Add category suggestions
    categories.forEach(category => {
        if (category.toLowerCase().includes(searchTerm.toLowerCase())) {
            suggestions.push(category);
        }
    });
    
    // Add brand suggestions
    brands.forEach(brand => {
        if (brand.toLowerCase().includes(searchTerm.toLowerCase())) {
            suggestions.push(brand);
        }
    });
    
    return suggestions.slice(0, 5); // Limit to 5 suggestions
}

/**
 * Load category dropdown
 */
function loadCategoryDropdown() {
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_categories' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateCategoryDropdown(response.data);
            }
        }
    });
}

/**
 * Load brand dropdown
 */
function loadBrandDropdown() {
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: { action: 'get_brands' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateBrandDropdown(response.data);
            }
        }
    });
}

/**
 * Populate category filters
 */
function populateCategoryFilters(categories) {
    const container = $('#categoryFilters');
    container.empty();
    
    // Add "All Categories" button
    container.append(`
        <button class="btn filter-btn active" data-category="all">
            <i class="fa fa-th-large me-2"></i>All Categories
        </button>
    `);
    
    categories.forEach(function(category) {
        container.append(`
            <button class="btn filter-btn" data-category="${category.cat_id}">
                <i class="fa fa-tag me-2"></i>${category.cat_name}
                <span class="badge bg-secondary ms-2 category-count" data-category="${category.cat_id}">0</span>
            </button>
        `);
    });
    
    // Add click handlers
    $('.filter-btn[data-category]').on('click', function() {
        const categoryId = $(this).data('category');
        applyCategoryFilter(categoryId);
    });
}

/**
 * Populate brand filters
 */
function populateBrandFilters(brands) {
    const container = $('#brandFilters');
    container.empty();
    
    // Add "All Brands" button
    container.append(`
        <button class="btn filter-btn active" data-brand="all">
            <i class="fa fa-star me-2"></i>All Brands
        </button>
    `);
    
    brands.forEach(function(brand) {
        container.append(`
            <button class="btn filter-btn" data-brand="${brand.brand_id}">
                <i class="fa fa-star me-2"></i>${brand.brand_name}
                <span class="badge bg-secondary ms-2 brand-count" data-brand="${brand.brand_id}">0</span>
            </button>
        `);
    });
    
    // Add click handlers
    $('.filter-btn[data-brand]').on('click', function() {
        const brandId = $(this).data('brand');
        applyBrandFilter(brandId);
    });
}

/**
 * Populate category dropdown
 */
function populateCategoryDropdown(categories) {
    const select = $('#categorySelect');
    select.empty().append('<option value="all">All Categories</option>');
    
    categories.forEach(function(category) {
        select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
    });
}

/**
 * Populate brand dropdown
 */
function populateBrandDropdown(brands) {
    const select = $('#brandSelect');
    select.empty().append('<option value="all">All Brands</option>');
    
    brands.forEach(function(brand) {
        select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
    });
}

/**
 * Apply category filter
 */
function applyCategoryFilter(categoryId) {
    // Update active state
    $('.filter-btn[data-category]').removeClass('active');
    $(`.filter-btn[data-category="${categoryId}"]`).addClass('active');
    
    // Update dropdown
    $('#categorySelect').val(categoryId);
    
    // Apply filters
    applyFilters();
}

/**
 * Apply brand filter
 */
function applyBrandFilter(brandId) {
    // Update active state
    $('.filter-btn[data-brand]').removeClass('active');
    $(`.filter-btn[data-brand="${brandId}"]`).addClass('active');
    
    // Update dropdown
    $('#brandSelect').val(brandId);
    
    // Apply filters
    applyFilters();
}

/**
 * Apply filters with debouncing
 */
let filterTimeout;
function debounceFilter() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 300);
}

/**
 * Apply all active filters
 */
function applyFilters() {
    const filters = {
        category: $('.filter-btn.active[data-category]').data('category') || 'all',
        brand: $('.filter-btn.active[data-brand]').data('brand') || 'all',
        sort: $('#sortSelect').val() || 'name_asc',
        min_price: $('#priceRangeMin').val() || 0,
        max_price: $('#priceRangeMax').val() || 999999,
        availability: $('#availabilityFilter').val() || 'all'
    };
    
    showLoading();
    
    $.ajax({
        url: 'product_actions.php',
        method: 'GET',
        data: {
            action: 'filter_products',
            ...filters
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayProducts(response.data.products);
                updateResultsInfo(response.data.total, 1);
                $('#resultsInfo').removeClass('results-info-hidden');
                updateFilterCounts(response.data.filter_counts);
            } else {
                showError('Filter failed: ' + response.message);
            }
        },
        error: function() {
            hideLoading();
            showError('An error occurred while applying filters');
        }
    });
}

/**
 * Clear all filters
 */
function clearAllFilters() {
    // Reset filter buttons
    $('.filter-btn').removeClass('active');
    $('.filter-btn[data-category="all"], .filter-btn[data-brand="all"]').addClass('active');
    
    // Reset dropdowns
    $('#categorySelect').val('all');
    $('#brandSelect').val('all');
    $('#sortSelect').val('name_asc');
    $('#availabilityFilter').val('all');
    
    // Reset price range
    $('#priceRangeMin').val(0);
    $('#priceRangeMax').val(999999);
    $('#priceRangeSlider').slider('values', [0, 999999]);
    
    // Clear search
    $('#searchInput').val('');
    hideSearchSuggestions();
    
    // Reload products
    loadProducts();
    $('#resultsInfo').addClass('results-info-hidden');
}

/**
 * Display products with enhanced features
 */
function displayProducts(products) {
    const container = $('#productsContainer');
    
    if (!products || products.length === 0) {
        container.html(`
            <div class="col-12">
                <div class="no-results text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
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
        // Proper image path handling - use product/ folder
        const imageSrc = getProductImagePath(product.product_image);
        const price = parseFloat(product.product_price).toFixed(2);
        const description = product.product_desc ? 
            product.product_desc.substring(0, 120) + (product.product_desc.length > 120 ? '...' : '') : 
            'No description available';
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card product-card-customer h-100" data-product-id="${product.product_id}">
                    <div class="product-image-container">
                        <img src="${imageSrc}" class="product-image-customer" alt="${product.product_title}" 
                             onerror="this.src='uploads/placeholder.png'" loading="lazy">
                        <div class="product-overlay">
                            <div class="product-actions">
                                <button class="btn btn-sm btn-outline-light me-1 btn-view-product" 
                                        data-product-id="${product.product_id}" 
                                        data-bs-toggle="tooltip" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-light btn-wishlist" 
                                        data-product-id="${product.product_id}"
                                        data-bs-toggle="tooltip" title="Add to Wishlist">
                                    <i class="fa fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                        <div class="d-grid">
                            <button class="btn btn-primary btn-view-product" data-product-id="${product.product_id}">
                                <i class="fa fa-eye me-2"></i>View Details
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
 * Display featured products
 */
function displayFeaturedProducts(products) {
    const container = $('#featuredProducts');
    
    if (!products || products.length === 0) {
        container.hide();
        return;
    }
    
    let html = '';
    products.forEach(function(product) {
        const imageSrc = getProductImagePath(product.product_image);
        const price = parseFloat(product.product_price).toFixed(2);
        
        html += `
            <div class="col-md-4 col-lg-2 mb-3">
                <div class="card featured-product-card h-100">
                    <img src="${imageSrc}" class="card-img-top featured-product-image" 
                         alt="${product.product_title}" onerror="this.src='uploads/placeholder.png'">
                    <div class="card-body p-2">
                        <h6 class="card-title small">${product.product_title}</h6>
                        <div class="text-primary fw-bold">$${price}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
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
 * Display product detail
 */
function displayProductDetail(product) {
    const imageSrc = getProductImagePath(product.product_image);
    const price = parseFloat(product.product_price).toFixed(2);
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="product-detail-image-container">
                    <img src="${imageSrc}" class="img-fluid rounded product-detail-image" 
                         alt="${product.product_title}" onerror="this.src='uploads/placeholder.png'">
                </div>
            </div>
            <div class="col-md-6">
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
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" id="addToCartBtn" data-product-id="${product.product_id}">
                        <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-outline-secondary" id="addToWishlistBtn" data-product-id="${product.product_id}">
                        <i class="fa fa-heart me-2"></i>Add to Wishlist
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#productDetailContent').html(content);
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
 * Toggle wishlist
 */
function toggleWishlist(productId) {
    // This would integrate with your wishlist system
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
        container.html(html).removeClass('pagination-container-hidden');
    } else {
        container.addClass('pagination-container-hidden');
    }
}

/**
 * Update results info
 */
function updateResultsInfo(total, page) {
    $('#resultsCount').text(total);
    $('#currentPage').text(page);
    $('#resultsInfo').removeClass('results-info-hidden');
}

/**
 * Update filter counts
 */
function updateFilterCounts(counts) {
    if (counts) {
        // Update category counts
        if (counts.categories) {
            Object.keys(counts.categories).forEach(categoryId => {
                $(`.category-count[data-category="${categoryId}"]`).text(counts.categories[categoryId]);
            });
        }
        
        // Update brand counts
        if (counts.brands) {
            Object.keys(counts.brands).forEach(brandId => {
                $(`.brand-count[data-brand="${brandId}"]`).text(counts.brands[brandId]);
            });
        }
    }
}

/**
 * Initialize multi-select functionality
 */
function initializeMultiSelect() {
    // This would implement multi-select dropdowns if needed
    // For now, we'll use single select
}

/**
 * Pagination click handler
 */
$(document).on('click', '.page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        loadProducts(page);
    }
});

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
window.performSearch = performSearch;
