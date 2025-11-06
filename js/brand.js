$(document).ready(function() {
    console.log('Brand page loaded, initializing...');
    
    // Verify jQuery and container exist
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h4>JavaScript Error</h4>
                <p class="text-muted">jQuery is not loaded. Please refresh the page.</p>
            </div>
        `);
        return;
    }
    
    if ($('#brandsContainer').length === 0) {
        console.error('Brands container not found!');
        return;
    }
    
    // Load brands and categories on page load
    loadBrands();
    loadCategories();

    // Image preview for add form
    $('#brandImage').on('change', function() {
        previewImage(this, '#previewBrandImg', '#brandImagePreview');
    });

    // Image preview for edit form
    $('#editBrandImage').on('change', function() {
        previewImage(this, '#editPreviewBrandImg', '#editBrandImagePreview');
    });

    // Add Brand Form Submission
    $('#addBrandForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);

        if (!validateBrandForm(formData)) {
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/add_brand_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
        
        const formData = new FormData(this);

        if (!validateBrandForm(formData)) {
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/update_brand_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Delete brand error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the brand. Please check the console for details.'
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
    console.log('Loading brands...');
    
    // Show loading state
    $('#brandsContainer').html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading brands...</p>
        </div>
    `);
    
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'GET',
        dataType: 'json',
        timeout: 10000, // 10 second timeout
        success: function(response) {
            console.log('Brands response:', response);
            if (response && response.success) {
                // Ensure data is an array
                const brands = Array.isArray(response.data) ? response.data : [];
                console.log('Displaying brands:', brands.length, 'brands found');
                displayBrands(brands);
            } else {
                // Show empty state with button
                console.log('No brands or error in response');
                $('#brandsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-star fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Brands Found</h4>
                        <p class="text-muted">Start by adding your first brand!</p>
                        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i class="fa fa-plus me-1"></i>Add Brand
                        </button>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading brands:', status, error);
            console.error('Response text:', xhr.responseText);
            
            let errorMsg = 'Unknown error';
            if (status === 'timeout') {
                errorMsg = 'Request timed out. Please check your connection.';
            } else if (status === 'parsererror') {
                errorMsg = 'Invalid response from server. Check console for details.';
            } else if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMsg = errorResponse.message || error;
                } catch (e) {
                    errorMsg = xhr.responseText.substring(0, 100);
                }
            } else {
                errorMsg = error || status;
            }
            
            $('#brandsContainer').html(`
                <div class="col-12 text-center py-5">
                    <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4>Error Loading Brands</h4>
                    <p class="text-muted">Please refresh the page and try again.</p>
                    <p class="text-danger small">Error: ${errorMsg}</p>
                    <button class="btn btn-outline-primary mt-3" onclick="loadBrands()">
                        <i class="fa fa-refresh me-1"></i>Retry
                    </button>
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

// Display brands function - always show images like categories do
function displayBrands(brands) {
    console.log('Displaying brands:', brands);
    
    // Ensure brands is an array
    if (!Array.isArray(brands)) {
        brands = [];
    }
    
    if (brands.length === 0) {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand!</p>
                <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="fa fa-plus me-1"></i>Add Brand
                </button>
            </div>
        `);
        return;
    }

    // Always use simple display to ensure images show (like categories)
    try {
        displayBrandsSimple(brands);
    } catch (error) {
        console.error('Error displaying brands:', error);
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h4>Error Displaying Brands</h4>
                <p class="text-muted">${error.message}</p>
            </div>
        `);
    }
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
            const imageSrc = brand.brand_image ? `../${brand.brand_image}` : '../uploads/placeholder.png';
            const escapedBrandName = escapeHtml(brand.brand_name);
            const escapedBrandImage = brand.brand_image ? escapeHtml(brand.brand_image) : '';
            
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card brand-card h-100">
                        <div class="brand-image-container">
                            <img src="${imageSrc}" class="card-img-top brand-image" alt="${escapedBrandName}" onerror="this.src='../uploads/placeholder.png'">
                            <div class="brand-overlay">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editBrand(${brand.brand_id}, '${escapedBrandName}', '${escapedBrandImage}')" title="Edit Brand">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${brand.brand_id})" title="Delete Brand">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fa fa-star text-warning me-2"></i>
                                ${escapedBrandName}
                            </h5>
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

// Simple display without grouping - always show images (exact same pattern as categories)
function displayBrandsSimple(brands) {
    console.log('displayBrandsSimple called with', brands.length, 'brands');
    
    if (!brands || brands.length === 0) {
        console.log('No brands to display, showing empty state');
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand!</p>
                <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="fa fa-plus me-1"></i>Add Brand
                </button>
            </div>
        `);
        return;
    }
    
    let html = '<div class="row">';
    brands.forEach(function(brand) {
        console.log('Processing brand:', brand);
        // Use exact same pattern as categories - simple and works
        const imageSrc = brand.brand_image ? `../${brand.brand_image}` : '../uploads/placeholder.png';
        const escapedBrandName = escapeHtml(brand.brand_name);
        const escapedBrandImage = brand.brand_image ? escapeHtml(brand.brand_image) : '';
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card brand-card h-100">
                    <div class="brand-image-container">
                        <img src="${imageSrc}" class="card-img-top brand-image" alt="${escapedBrandName}" onerror="this.src='../uploads/placeholder.png'">
                        <div class="brand-overlay">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editBrand(${brand.brand_id}, '${escapedBrandName.replace(/'/g, "\\'")}', '${escapedBrandImage.replace(/'/g, "\\'")}')" title="Edit Brand">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${brand.brand_id})" title="Delete Brand">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa fa-star text-warning me-2"></i>
                            ${escapedBrandName}
                        </h5>
                        <p class="card-text text-muted">
                            <small><strong>Brand ID:</strong> ${brand.brand_id}</small>
                        </p>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    console.log('Setting HTML to brandsContainer, length:', html.length);
    const container = $('#brandsContainer');
    if (container.length === 0) {
        console.error('brandsContainer not found!');
        return;
    }
    
    container.html(html);
    console.log('HTML set successfully');
}

// Edit brand function
function editBrand(brandId, brandName, brandImage = '') {
    $('#editBrandId').val(brandId);
    $('#editBrandName').val(brandName);
    
    // Set current image preview
    if (brandImage) {
        $('#editPreviewBrandImg').attr('src', `../${brandImage}`);
        $('#editBrandImagePreview').show();
    } else {
        $('#editPreviewBrandImg').attr('src', '../uploads/placeholder.png');
        $('#editBrandImagePreview').show();
    }
    
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

function clearFieldError(fieldId) {
    const field = $(fieldId);
    field.removeClass('is-invalid');
    field.siblings('.invalid-feedback').remove();
}

function clearFieldErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

// Image preview function
function previewImage(input, previewId, containerId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $(previewId).attr('src', e.target.result);
            $(containerId).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Validate brand form
function validateBrandForm(formData) {
    let isValid = true;
    
    // Check required fields
    const brandName = formData.get('brandName');
    if (!brandName || brandName.trim() === '') {
        showFieldError('#brandName', 'Brand name is required');
        isValid = false;
    } else {
        clearFieldError('#brandName');
    }
    
    // Check edit form required fields
    const editBrandName = formData.get('brandName');
    if ($('#editBrandId').length && (!editBrandName || editBrandName.trim() === '')) {
        showFieldError('#editBrandName', 'Brand name is required');
        isValid = false;
    } else if ($('#editBrandId').length) {
        clearFieldError('#editBrandName');
    }
    
    return isValid;
}

// Escape HTML function
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}
