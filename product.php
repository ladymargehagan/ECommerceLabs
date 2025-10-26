<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">
    <link href="css/product-customer.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fa fa-star text-warning me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fa fa-home me-1"></i>Home
                </a>
                <a class="nav-link active" href="product.php">
                    <i class="fa fa-box me-1"></i>Products
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="customer/dashboard.php">
                        <i class="fa fa-user me-1"></i>Dashboard
                    </a>
                    <a class="nav-link" href="login/logout.php">
                        <i class="fa fa-sign-out-alt me-1"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">
                        <i class="fa fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a class="nav-link" href="login/register.php">
                        <i class="fa fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fa fa-box me-3"></i>Discover Our Products
            </h1>
            <p class="lead mb-0">Explore our curated collection of premium products</p>
        </div>
    </div>

    <div class="container">
        <!-- Search Section -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fa fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control search-input border-start-0" id="searchInput" 
                               placeholder="Search for products, brands, or categories...">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-custom" id="searchBtn">
                        <i class="fa fa-search me-2"></i>Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-tags text-primary me-2"></i>Categories
                    </h6>
                    <div id="categoryFilters">
                        <button class="btn filter-btn active" data-category="all">All Categories</button>
                        <!-- Categories will be loaded here -->
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-star text-warning me-2"></i>Brands
                    </h6>
                    <div id="brandFilters">
                        <button class="btn filter-btn active" data-brand="all">All Brands</button>
                        <!-- Brands will be loaded here -->
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-sort text-info me-2"></i>Sort By
                    </h6>
                    <select class="form-select" id="sortSelect">
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low to High)</option>
                        <option value="price_desc">Price (High to Low)</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="results-info results-info-hidden" id="resultsInfo">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fa fa-info-circle text-primary me-2"></i>
                        <span id="resultsCount">0</span> products found
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-outline-secondary" id="clearFilters">
                        <i class="fa fa-times me-2"></i>Clear All Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <div class="row" id="productsContainer">
                <!-- Loading state -->
                <div class="col-12 text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary spinner-large" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading products...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Products pagination" id="paginationContainer" class="pagination-container-hidden">
            <ul class="pagination" id="pagination">
                <!-- Pagination will be generated here -->
            </ul>
        </nav>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-box me-2"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productDetailContent">
                    <!-- Product details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-custom" id="addToCartBtn">
                        <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize the page
            loadProducts();
            loadFilters();
            
            // Search functionality
            $('#searchBtn').on('click', function() {
                performSearch();
            });
            
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    performSearch();
                }
            });
            
            // Filter functionality
            $(document).on('click', '.filter-btn', function() {
                const $this = $(this);
                const filterType = $this.parent().attr('id');
                
                // Remove active class from siblings
                $this.siblings().removeClass('active');
                $this.addClass('active');
                
                // Apply filters
                applyFilters();
            });
            
            // Sort functionality
            $('#sortSelect').on('change', function() {
                applyFilters();
            });
            
            // Clear filters
            $('#clearFilters').on('click', function() {
                $('.filter-btn').removeClass('active');
                $('.filter-btn[data-category="all"], .filter-btn[data-brand="all"]').addClass('active');
                $('#searchInput').val('');
                $('#sortSelect').val('name_asc');
                loadProducts();
                $('#resultsInfo').hide();
            });
            
            // Product detail modal
            $(document).on('click', '.btn-view-product', function() {
                const productId = $(this).data('product-id');
                loadProductDetail(productId);
            });
        });
        
        // Load products
        function loadProducts(page = 1) {
            showLoading();
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_products', page: page },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        displayProducts(response.data.products);
                        updatePagination(response.data.pagination);
                        updateResultsInfo(response.data.total, response.data.page);
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
        
        // Load filters
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
                    }
                }
            });
        }
        
        // Perform search
        function performSearch() {
            const searchTerm = $('#searchInput').val().trim();
            if (searchTerm) {
                showLoading();
                
                $.ajax({
                    url: 'product_actions.php',
                    method: 'GET',
                    data: { action: 'search_products', search: searchTerm },
                    dataType: 'json',
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            displayProducts(response.data.products);
                            updateResultsInfo(response.data.total, 1);
                            $('#resultsInfo').removeClass('results-info-hidden');
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
        }
        
        // Apply filters
        function applyFilters() {
            const selectedCategory = $('.filter-btn.active[data-category]').data('category');
            const selectedBrand = $('.filter-btn.active[data-brand]').data('brand');
            const sortBy = $('#sortSelect').val();
            
            showLoading();
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: {
                    action: 'filter_products',
                    category: selectedCategory,
                    brand: selectedBrand,
                    sort: sortBy
                },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        displayProducts(response.data.products);
                        updateResultsInfo(response.data.total, 1);
                        $('#resultsInfo').removeClass('results-info-hidden');
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
        
        // Display products
        function displayProducts(products) {
            const container = $('#productsContainer');
            
            if (!products || products.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="no-results">
                            <i class="fa fa-search"></i>
                            <h4>No Products Found</h4>
                            <p>Try adjusting your search or filter criteria</p>
                        </div>
                    </div>
                `);
                return;
            }
            
            let html = '';
            products.forEach(function(product) {
                const imageSrc = product.product_image ? product.product_image : 'uploads/placeholder.png';
                const price = parseFloat(product.product_price).toFixed(2);
                const description = product.product_desc ? product.product_desc.substring(0, 120) + '...' : 'No description available';
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card product-card-customer h-100">
                            <img src="${imageSrc}" class="product-image-customer" alt="${product.product_title}" 
                                 onerror="this.src='uploads/placeholder.png'">
                            <div class="product-info">
                                <h5 class="product-title">${product.product_title}</h5>
                                <div class="product-price">$${price}</div>
                                <div class="product-meta">
                                    <small><strong>Category:</strong> ${product.cat_name || 'No Category'}</small><br>
                                    <small><strong>Brand:</strong> ${product.brand_name || 'No Brand'}</small>
                                </div>
                                <p class="product-description">${description}</p>
                                <button class="btn btn-view-product" data-product-id="${product.product_id}">
                                    <i class="fa fa-eye me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
        }
        
        // Populate category filters
        function populateCategoryFilters(categories) {
            const container = $('#categoryFilters');
            categories.forEach(function(category) {
                container.append(`
                    <button class="btn filter-btn" data-category="${category.cat_id}">
                        ${category.cat_name}
                    </button>
                `);
            });
        }
        
        // Populate brand filters
        function populateBrandFilters(brands) {
            const container = $('#brandFilters');
            brands.forEach(function(brand) {
                container.append(`
                    <button class="btn filter-btn" data-brand="${brand.brand_id}">
                        ${brand.brand_name}
                    </button>
                `);
            });
        }
        
        // Load product detail
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
        
        // Display product detail
        function displayProductDetail(product) {
            const imageSrc = product.product_image ? product.product_image : 'uploads/placeholder.png';
            const price = parseFloat(product.product_price).toFixed(2);
            
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="${imageSrc}" class="img-fluid rounded" alt="${product.product_title}" 
                             onerror="this.src='uploads/placeholder.png'">
                    </div>
                    <div class="col-md-6">
                        <h3 class="mb-3">${product.product_title}</h3>
                        <div class="product-price mb-3">$${price}</div>
                        <div class="mb-3">
                            <strong>Category:</strong> ${product.cat_name || 'No Category'}<br>
                            <strong>Brand:</strong> ${product.brand_name || 'No Brand'}
                        </div>
                        ${product.product_desc ? `<p class="mb-3">${product.product_desc}</p>` : ''}
                        ${product.product_keywords ? `<p class="mb-3"><strong>Keywords:</strong> ${product.product_keywords}</p>` : ''}
                    </div>
                </div>
            `;
            
            $('#productDetailContent').html(content);
            $('#addToCartBtn').data('product-id', product.product_id);
        }
        
        // Update pagination
        function updatePagination(pagination) {
            if (pagination.total_pages > 1) {
                let html = '';
                
                // Previous button
                if (pagination.current_page > 1) {
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a></li>`;
                }
                
                // Page numbers
                for (let i = 1; i <= pagination.total_pages; i++) {
                    const activeClass = i === pagination.current_page ? 'active' : '';
                    html += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                
                // Next button
                if (pagination.current_page < pagination.total_pages) {
                    html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a></li>`;
                }
                
                $('#pagination').html(html);
                $('#paginationContainer').removeClass('pagination-container-hidden');
            } else {
                $('#paginationContainer').hide();
            }
        }
        
        // Update results info
        function updateResultsInfo(total, page) {
            $('#resultsCount').text(total);
            $('#resultsInfo').removeClass('results-info-hidden');
        }
        
        // Pagination click handler
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                loadProducts(page);
            }
        });
        
        // Utility functions
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
                text: message
            });
        }
    </script>
</body>
</html>
