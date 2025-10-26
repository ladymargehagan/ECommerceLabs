/**
 * Enhanced Product Search and Filtering JavaScript
 * Provides dynamic search, filtering, and AJAX functionality
 */

class ProductSearch {
    constructor() {
        this.searchTimeout = null;
        this.currentPage = 1;
        this.currentFilters = {
            search: '',
            category: '',
            brand: ''
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialData();
    }

    bindEvents() {
        // Search input with debounce
        $(document).on('input', '#searchQuery, #search', function() {
            const query = $(this).val().trim();
            productSearch.handleSearchInput(query);
        });

        // Filter dropdowns
        $(document).on('change', '#categoryFilter, #category', function() {
            const categoryId = $(this).val();
            productSearch.handleFilterChange('category', categoryId);
        });

        $(document).on('change', '#brandFilter, #brand', function() {
            const brandId = $(this).val();
            productSearch.handleFilterChange('brand', brandId);
        });

        // Search form submission
        $(document).on('submit', '#searchForm, #filterForm', function(e) {
            e.preventDefault();
            productSearch.performSearch();
        });

        // Pagination links
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = new URL($(this).attr('href'));
            const page = url.searchParams.get('page') || 1;
            productSearch.loadPage(parseInt(page));
        });

        // Add to cart buttons
        $(document).on('click', '.add-to-cart', function() {
            const productId = $(this).data('product-id');
            productSearch.addToCart(productId);
        });

        // Clear filters
        $(document).on('click', '.clear-filters', function() {
            productSearch.clearFilters();
        });
    }

    handleSearchInput(query) {
        clearTimeout(this.searchTimeout);
        this.currentFilters.search = query;
        
        if (query.length >= 2) {
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 500);
        } else if (query.length === 0) {
            this.performSearch();
        }
    }

    handleFilterChange(filterType, value) {
        this.currentFilters[filterType] = value;
        this.performSearch();
    }

    performSearch() {
        const params = new URLSearchParams();
        
        if (this.currentFilters.search) {
            params.set('q', this.currentFilters.search);
        }
        if (this.currentFilters.category) {
            params.set('category', this.currentFilters.category);
        }
        if (this.currentFilters.brand) {
            params.set('brand', this.currentFilters.brand);
        }
        if (this.currentPage > 1) {
            params.set('page', this.currentPage);
        }

        // Determine the correct URL based on current page
        let targetUrl = 'product_search_result.php';
        if (window.location.pathname.includes('all_product.php')) {
            targetUrl = 'all_product.php';
        }

        const url = `${targetUrl}?${params.toString()}`;
        window.location.href = url;
    }

    loadPage(page) {
        this.currentPage = page;
        this.performSearch();
    }

    clearFilters() {
        this.currentFilters = {
            search: '',
            category: '',
            brand: ''
        };
        this.currentPage = 1;
        
        // Reset form elements
        $('#searchQuery, #search').val('');
        $('#categoryFilter, #category').val('');
        $('#brandFilter, #brand').val('');
        
        // Redirect to all products page
        window.location.href = 'all_product.php';
    }

    addToCart(productId) {
        // Placeholder for add to cart functionality
        Swal.fire({
            title: 'Add to Cart',
            text: `Product ID: ${productId} - This feature will be implemented soon!`,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }

    loadInitialData() {
        // Load current filter values from URL
        const urlParams = new URLSearchParams(window.location.search);
        this.currentFilters.search = urlParams.get('q') || '';
        this.currentFilters.category = urlParams.get('category') || '';
        this.currentFilters.brand = urlParams.get('brand') || '';
        this.currentPage = parseInt(urlParams.get('page')) || 1;

        // Update form elements
        $('#searchQuery, #search').val(this.currentFilters.search);
        $('#categoryFilter, #category').val(this.currentFilters.category);
        $('#brandFilter, #brand').val(this.currentFilters.brand);
    }

    // AJAX method for dynamic loading (for future enhancement)
    loadProductsAjax(filters = {}) {
        return $.ajax({
            url: 'product_actions.php',
            method: 'GET',
            data: {
                action: 'get_products_with_filters',
                ...filters
            },
            dataType: 'json'
        });
    }

    // Method to update product count dynamically
    updateProductCount(filters = {}) {
        return $.ajax({
            url: 'product_actions.php',
            method: 'GET',
            data: {
                action: 'get_product_count',
                ...filters
            },
            dataType: 'json'
        });
    }
}

// Live search suggestions (for future enhancement)
class SearchSuggestions {
    constructor() {
        this.suggestionsContainer = null;
        this.currentSuggestions = [];
        this.init();
    }

    init() {
        this.createSuggestionsContainer();
        this.bindEvents();
    }

    createSuggestionsContainer() {
        if (!$('#searchSuggestions').length) {
            $('body').append(`
                <div id="searchSuggestions" class="search-suggestions" style="display: none;">
                    <div class="suggestions-list"></div>
                </div>
            `);
        }
        this.suggestionsContainer = $('#searchSuggestions');
    }

    bindEvents() {
        $(document).on('input', '#searchQuery, #search', (e) => {
            const query = $(e.target).val().trim();
            if (query.length >= 2) {
                this.loadSuggestions(query);
            } else {
                this.hideSuggestions();
            }
        });

        $(document).on('click', (e) => {
            if (!$(e.target).closest('#searchQuery, #search, #searchSuggestions').length) {
                this.hideSuggestions();
            }
        });
    }

    loadSuggestions(query) {
        // Placeholder for AJAX call to get search suggestions
        // This would call product_actions.php with action='get_search_suggestions'
        console.log('Loading suggestions for:', query);
        
        // Mock suggestions for demonstration
        const mockSuggestions = [
            'African spices',
            'Traditional clothing',
            'Handmade crafts',
            'Natural oils',
            'Cultural artifacts'
        ].filter(item => item.toLowerCase().includes(query.toLowerCase()));

        this.showSuggestions(mockSuggestions);
    }

    showSuggestions(suggestions) {
        if (suggestions.length === 0) {
            this.hideSuggestions();
            return;
        }

        const suggestionsHtml = suggestions.map(suggestion => 
            `<div class="suggestion-item" data-suggestion="${suggestion}">${suggestion}</div>`
        ).join('');

        this.suggestionsContainer.find('.suggestions-list').html(suggestionsHtml);
        this.suggestionsContainer.show();

        // Bind click events for suggestions
        this.suggestionsContainer.find('.suggestion-item').click((e) => {
            const suggestion = $(e.target).data('suggestion');
            $('#searchQuery, #search').val(suggestion);
            this.hideSuggestions();
            productSearch.performSearch();
        });
    }

    hideSuggestions() {
        this.suggestionsContainer.hide();
    }
}

// Filter enhancement for dynamic dropdowns
class FilterEnhancement {
    constructor() {
        this.init();
    }

    init() {
        this.loadCategories();
        this.loadBrands();
    }

    loadCategories() {
        $.ajax({
            url: 'product_actions.php',
            method: 'GET',
            data: { action: 'get_categories' },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.updateCategoryDropdown(response.data);
                }
            }
        });
    }

    loadBrands() {
        $.ajax({
            url: 'product_actions.php',
            method: 'GET',
            data: { action: 'get_brands' },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.updateBrandDropdown(response.data);
                }
            }
        });
    }

    updateCategoryDropdown(categories) {
        const categorySelects = $('#categoryFilter, #category');
        categorySelects.each(function() {
            const currentValue = $(this).val();
            let options = '<option value="">All Categories</option>';
            
            categories.forEach(category => {
                const selected = category.cat_id == currentValue ? 'selected' : '';
                options += `<option value="${category.cat_id}" ${selected}>${category.cat_name}</option>`;
            });
            
            $(this).html(options);
        });
    }

    updateBrandDropdown(brands) {
        const brandSelects = $('#brandFilter, #brand');
        brandSelects.each(function() {
            const currentValue = $(this).val();
            let options = '<option value="">All Brands</option>';
            
            brands.forEach(brand => {
                const selected = brand.brand_id == currentValue ? 'selected' : '';
                options += `<option value="${brand.brand_id}" ${selected}>${brand.brand_name}</option>`;
            });
            
            $(this).html(options);
        });
    }
}

// Initialize when document is ready
$(document).ready(function() {
    // Initialize main search functionality
    window.productSearch = new ProductSearch();
    
    // Initialize search suggestions (optional)
    // window.searchSuggestions = new SearchSuggestions();
    
    // Initialize filter enhancement
    window.filterEnhancement = new FilterEnhancement();

    // Additional utility functions
    window.productUtils = {
        formatPrice: (price) => {
            return `$${parseFloat(price).toFixed(2)}`;
        },
        
        highlightSearchTerm: (text, searchTerm) => {
            if (!searchTerm) return text;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        },
        
        truncateText: (text, maxLength) => {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }
    };
});
