$(document).ready(function() {
    // Load brands and categories on page load
    loadBrands();
    loadCategories();

    // Add Brand Form Submission
    $('#addBrandForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            brandName: $('#brandName').val().trim()
        };

        if (!formData.brandName) {
            showFieldError('#brandName', 'Brand name is required');
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/add_brand_action.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#addBrandModal').modal('hide');
                    $('#addBrandForm')[0].reset();
                    clearFieldErrors();
                    loadBrands();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function() {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while adding the brand'
                });
            }
        });
    });

    // Edit Brand Form Submission
    $('#editBrandForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            brandId: $('#editBrandId').val(),
            brandName: $('#editBrandName').val().trim()
        };

        if (!formData.brandName) {
            showFieldError('#editBrandName', 'Brand name is required');
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/update_brand_action.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#editBrandModal').modal('hide');
                    clearFieldErrors();
                    loadBrands();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function() {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating the brand'
                });
            }
        });
    });

    // Delete Brand Confirmation
    $('#confirmDelete').on('click', function() {
        const brandId = $('#deleteBrandId').val();
        
        showLoading();
        
        $.ajax({
            url: '../actions/delete_brand_action.php',
            method: 'POST',
            data: { brandId: brandId },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#deleteBrandModal').modal('hide');
                    loadBrands();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function() {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the brand'
                });
            }
        });
    });

    // Clear form when modal is closed
    $('#addBrandModal').on('hidden.bs.modal', function() {
        $('#addBrandForm')[0].reset();
        clearFieldErrors();
    });

    $('#editBrandModal').on('hidden.bs.modal', function() {
        $('#editBrandForm')[0].reset();
        clearFieldErrors();
    });
});

// Load brands function
function loadBrands() {
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayBrands(response.data);
            } else {
                $('#brandsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>No Brands Found</h4>
                        <p class="text-muted">Start by adding your first brand.</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#brandsContainer').html(`
                <div class="col-12 text-center py-5">
                    <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4>Error Loading Brands</h4>
                    <p class="text-muted">Please refresh the page and try again.</p>
                </div>
            `);
        }
    });
}

// Load categories function
function loadCategories() {
    $.ajax({
        url: '../actions/fetch_category_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateCategorySelects(response.data);
            }
        },
        error: function() {
            console.error('Error loading categories');
        }
    });
}

// Populate category dropdowns
function populateCategorySelects(categories) {
    const addSelect = $('#categoryId');
    const editSelect = $('#editCategoryId');
    
    // Clear existing options except the first one
    addSelect.find('option:not(:first)').remove();
    editSelect.find('option:not(:first)').remove();
    
    categories.forEach(function(category) {
        addSelect.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
        editSelect.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
    });
}

// Display brands function with visual grouping by categories
function displayBrands(brands) {
    if (!brands || brands.length === 0) {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
        return;
    }

    // Get categories for grouping
    $.ajax({
        url: '../actions/fetch_category_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayBrandsGroupedByCategories(brands, response.data);
            } else {
                displayBrandsSimple(brands);
            }
        },
        error: function() {
            displayBrandsSimple(brands);
        }
    });
}

// Display brands grouped by categories
function displayBrandsGroupedByCategories(brands, categories) {
    let html = '';
    
    // Group brands by categories (visual grouping only)
    categories.forEach(function(category) {
        html += `
            <div class="col-12 mb-4">
                <div class="category-section">
                    <h4 class="category-header">
                        <i class="fa fa-tags text-primary me-2"></i>
                        ${category.cat_name}
                    </h4>
                    <div class="row">
        `;
        
        // Display all brands under each category (since brands can produce across categories)
        brands.forEach(function(brand) {
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card brand-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fa fa-star text-warning me-2"></i>
                                    ${brand.brand_name}
                                </h5>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editBrand(${brand.brand_id}, '${brand.brand_name}')" title="Edit Brand">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${brand.brand_id})" title="Delete Brand">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="card-text text-muted">
                                <small><strong>Brand ID:</strong> ${brand.brand_id}</small>
                            </p>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                    </div>
                </div>
            </div>
        `;
    });

    $('#brandsContainer').html(html);
}

// Simple display without grouping
function displayBrandsSimple(brands) {
    let html = '';
    brands.forEach(function(brand) {
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card brand-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fa fa-star text-warning me-2"></i>
                                ${brand.brand_name}
                            </h5>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editBrand(${brand.brand_id}, '${brand.brand_name}')" title="Edit Brand">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${brand.brand_id})" title="Delete Brand">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="card-text text-muted">
                            <small><strong>Brand ID:</strong> ${brand.brand_id}</small>
                        </p>
                    </div>
                </div>
            </div>
        `;
    });

    $('#brandsContainer').html(html);
}

// Edit brand function
function editBrand(brandId, brandName) {
    $('#editBrandId').val(brandId);
    $('#editBrandName').val(brandName);
    $('#editBrandModal').modal('show');
}

// Delete brand function
function deleteBrand(brandId) {
    $('#deleteBrandId').val(brandId);
    $('#deleteBrandModal').modal('show');
}

// Utility functions
function showLoading() {
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showFieldError(fieldId, message) {
    $(fieldId).addClass('is-invalid');
    $(fieldId).siblings('.invalid-feedback').text(message);
}

function clearFieldErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}
