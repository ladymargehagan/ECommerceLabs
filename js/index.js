/**
 * Index Page JavaScript
 * Features: Header search, quick filters, featured products, navigation
 */

$(document).ready(function() {
    // Initialize the page
    initializePage();
    
    // Header search functionality
    initializeHeaderSearch();
    
    // Quick filters functionality
    initializeQuickFilters();
});

/**
 * Initialize the page
 */
function initializePage() {
    // Show welcome message if just logged in
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('login') === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Welcome Back!',
            text: 'You have successfully logged in.',
            timer: 3000,
            showConfirmButton: false
        });
    }
    
    // Load categories and brands for quick filters
    loadQuickFilters();
}

/**
 * Initialize header search functionality
 */
function initializeHeaderSearch() {
    // Header search form submission
    $('#headerSearchForm').on('submit', function(e) {
        e.preventDefault();
        const searchQuery = $('#headerSearchInput').val().trim();
        
        if (searchQuery) {
            // Redirect to search results page
            window.location.href = `product_search_result.php?q=${encodeURIComponent(searchQuery)}`;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Search Required',
                text: 'Please enter a search term',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
    
    // Real-time search suggestions
    let searchTimeout;
    $('#headerSearchInput').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(function() {
                showSearchSuggestions(searchTerm);
            }, 300);
        } else {
            hideSearchSuggestions();
        }
    });
    
    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#headerSearchInput, #headerSearchSuggestions').length) {
            hideSearchSuggestions();
        }
    });
}

/**
 * Initialize quick filters functionality
 */
function initializeQuickFilters() {
    // Apply quick filters
    $('#applyQuickFilters').on('click', function() {
        const category = $('#quickCategoryFilter').val();
        const brand = $('#quickBrandFilter').val();
        
        if (category || brand) {
            // Build URL with filters
            let url = 'all_product.php?';
            const params = [];
            
            if (category) {
                params.push(`category=${category}`);
            }
            if (brand) {
                params.push(`brand=${brand}`);
            }
            
            url += params.join('&');
            window.location.href = url;
        } else {
            // No filters selected, go to all products
            window.location.href = 'all_product.php';
        }
    });
    
    // Category filter change
    $('#quickCategoryFilter').on('change', function() {
        // Optional: Auto-apply filters
        // applyQuickFilters();
    });
    
    // Brand filter change
    $('#quickBrandFilter').on('change', function() {
        // Optional: Auto-apply filters
        // applyQuickFilters();
    });
}

/**
 * Load quick filters (categories and brands)
 */
function loadQuickFilters() {
    // Load categories
    $.ajax({
        url: 'simple_filters.php',
        method: 'GET',
        data: { action: 'get_categories' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateQuickCategoryFilter(response.data);
            }
        },
        error: function() {
            console.error('Failed to load categories for quick filters');
        }
    });
    
    // Load brands
    $.ajax({
        url: 'simple_filters.php',
        method: 'GET',
        data: { action: 'get_brands' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateQuickBrandFilter(response.data);
            }
        },
        error: function() {
            console.error('Failed to load brands for quick filters');
        }
    });
}

/**
 * Populate quick category filter
 */
function populateQuickCategoryFilter(categories) {
    const select = $('#quickCategoryFilter');
    select.empty().append('<option value="">All Categories</option>');
    
    categories.forEach(function(category) {
        select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
    });
}

/**
 * Populate quick brand filter
 */
function populateQuickBrandFilter(brands) {
    const select = $('#quickBrandFilter');
    select.empty().append('<option value="">All Brands</option>');
    
    brands.forEach(function(brand) {
        select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
    });
}


/**
 * Show search suggestions
 */
function showSearchSuggestions(searchTerm) {
    // This would typically make an AJAX call to get suggestions
    // For now, we'll show basic suggestions
    const suggestions = generateBasicSuggestions(searchTerm);
    
    if (suggestions.length > 0) {
        const suggestionsHtml = suggestions.map(suggestion => 
            `<div class="suggestion-item" data-suggestion="${suggestion}">
                <i class="fa fa-search me-2"></i>${suggestion}
            </div>`
        ).join('');
        
        // Create suggestions dropdown if it doesn't exist
        if ($('#headerSearchSuggestions').length === 0) {
            $('#headerSearchForm').after(`
                <div id="headerSearchSuggestions" class="search-suggestions dropdown-menu show" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;">
                    <!-- Suggestions will be populated here -->
                </div>
            `);
        }
        
        $('#headerSearchSuggestions').html(suggestionsHtml).show();
        
        // Handle suggestion clicks
        $('.suggestion-item').on('click', function() {
            const suggestion = $(this).data('suggestion');
            $('#headerSearchInput').val(suggestion);
            $('#headerSearchForm').submit();
            hideSearchSuggestions();
        });
    }
}

/**
 * Hide search suggestions
 */
function hideSearchSuggestions() {
    $('#headerSearchSuggestions').hide();
}

/**
 * Generate basic suggestions
 */
function generateBasicSuggestions(searchTerm) {
    const suggestions = [];
    
    // Get current categories and brands from dropdowns
    const categories = $('#quickCategoryFilter option').map(function() {
        return $(this).text();
    }).get();
    
    const brands = $('#quickBrandFilter option').map(function() {
        return $(this).text();
    }).get();
    
    // Add matching categories
    categories.forEach(category => {
        if (category.toLowerCase().includes(searchTerm.toLowerCase()) && category !== 'All Categories') {
            suggestions.push(category);
        }
    });
    
    // Add matching brands
    brands.forEach(brand => {
        if (brand.toLowerCase().includes(searchTerm.toLowerCase()) && brand !== 'All Brands') {
            suggestions.push(brand);
        }
    });
    
    return suggestions.slice(0, 5); // Limit to 5 suggestions
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
