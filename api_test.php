<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background-color: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1><i class="fa fa-vial me-2"></i>API Test Page</h1>
        <p class="lead">Testing all product-related API endpoints</p>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Test Controls</h3>
                <button class="btn btn-primary mb-2" onclick="testAllEndpoints()">
                    <i class="fa fa-play me-2"></i>Test All Endpoints
                </button>
                <button class="btn btn-success mb-2" onclick="testProducts()">
                    <i class="fa fa-box me-2"></i>Test Products
                </button>
                <button class="btn btn-info mb-2" onclick="testCategories()">
                    <i class="fa fa-tags me-2"></i>Test Categories
                </button>
                <button class="btn btn-warning mb-2" onclick="testBrands()">
                    <i class="fa fa-star me-2"></i>Test Brands
                </button>
                <button class="btn btn-secondary mb-2" onclick="testSearch()">
                    <i class="fa fa-search me-2"></i>Test Search
                </button>
                <button class="btn btn-danger mb-2" onclick="clearResults()">
                    <i class="fa fa-trash me-2"></i>Clear Results
                </button>
            </div>
            <div class="col-md-6">
                <h3>Quick Links</h3>
                <a href="test_database.php" class="btn btn-outline-primary mb-2 d-block">
                    <i class="fa fa-database me-2"></i>Database Test & Sample Data
                </a>
                <a href="all_product.php" class="btn btn-outline-success mb-2 d-block">
                    <i class="fa fa-box me-2"></i>All Products Page
                </a>
                <a href="product.php" class="btn btn-outline-info mb-2 d-block">
                    <i class="fa fa-search me-2"></i>Product Search Page
                </a>
                <a href="index.php" class="btn btn-outline-secondary mb-2 d-block">
                    <i class="fa fa-home me-2"></i>Home Page
                </a>
            </div>
        </div>
        
        <div id="testResults" class="mt-4">
            <h3>Test Results</h3>
            <div id="resultsContainer">
                <div class="test-info">
                    <i class="fa fa-info-circle me-2"></i>Click "Test All Endpoints" to begin testing
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addResult(message, type = 'info', data = null) {
            const container = $('#resultsContainer');
            const timestamp = new Date().toLocaleTimeString();
            
            let html = `<div class="test-result test-${type}">
                <strong>[${timestamp}]</strong> ${message}`;
            
            if (data) {
                html += `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            }
            
            html += '</div>';
            container.append(html);
        }
        
        function testAllEndpoints() {
            clearResults();
            addResult('Starting comprehensive API test...', 'info');
            
            testProducts();
            setTimeout(() => testCategories(), 500);
            setTimeout(() => testBrands(), 1000);
            setTimeout(() => testSearch(), 1500);
            setTimeout(() => testPagination(), 2000);
        }
        
        function testProducts() {
            addResult('Testing get_products_paginated endpoint...', 'info');
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_products_paginated', page: 1, limit: 5 },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        addResult(`✓ Products endpoint working! Found ${response.data.total} products`, 'success', {
                            total: response.data.total,
                            products_count: response.data.products.length,
                            pagination: response.data.pagination
                        });
                    } else {
                        addResult(`✗ Products endpoint failed: ${response.message}`, 'error', response);
                    }
                },
                error: function(xhr, status, error) {
                    addResult(`✗ Products endpoint error: ${status} - ${error}`, 'error', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }
        
        function testCategories() {
            addResult('Testing get_categories endpoint...', 'info');
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_categories' },
                dataType: 'json',
                timeout: 5000,
                success: function(response) {
                    if (response.success) {
                        addResult(`✓ Categories endpoint working! Found ${response.data.length} categories`, 'success', response.data);
                    } else {
                        addResult(`✗ Categories endpoint failed: ${response.message}`, 'error', response);
                    }
                },
                error: function(xhr, status, error) {
                    addResult(`✗ Categories endpoint error: ${status} - ${error}`, 'error', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }
        
        function testBrands() {
            addResult('Testing get_brands endpoint...', 'info');
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_brands' },
                dataType: 'json',
                timeout: 5000,
                success: function(response) {
                    if (response.success) {
                        addResult(`✓ Brands endpoint working! Found ${response.data.length} brands`, 'success', response.data);
                    } else {
                        addResult(`✗ Brands endpoint failed: ${response.message}`, 'error', response);
                    }
                },
                error: function(xhr, status, error) {
                    addResult(`✗ Brands endpoint error: ${status} - ${error}`, 'error', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }
        
        function testSearch() {
            addResult('Testing search_products endpoint...', 'info');
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'search_products', search: 'phone', page: 1, limit: 5 },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        addResult(`✓ Search endpoint working! Found ${response.data.total} results for "phone"`, 'success', {
                            search_term: response.data.search_term,
                            total: response.data.total,
                            results_count: response.data.products.length
                        });
                    } else {
                        addResult(`✗ Search endpoint failed: ${response.message}`, 'error', response);
                    }
                },
                error: function(xhr, status, error) {
                    addResult(`✗ Search endpoint error: ${status} - ${error}`, 'error', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }
        
        function testPagination() {
            addResult('Testing pagination functionality...', 'info');
            
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_products_paginated', page: 2, limit: 3 },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        addResult(`✓ Pagination working! Page 2 with ${response.data.products.length} products`, 'success', {
                            current_page: response.data.pagination.current_page,
                            total_pages: response.data.pagination.total_pages,
                            total_items: response.data.pagination.total_items
                        });
                    } else {
                        addResult(`✗ Pagination failed: ${response.message}`, 'error', response);
                    }
                },
                error: function(xhr, status, error) {
                    addResult(`✗ Pagination error: ${status} - ${error}`, 'error', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }
        
        function clearResults() {
            $('#resultsContainer').empty();
        }
        
        // Auto-test on page load
        $(document).ready(function() {
            addResult('API Test Page loaded successfully', 'success');
            addResult('Ready to test endpoints. Click "Test All Endpoints" to begin.', 'info');
        });
    </script>
</body>
</html>
