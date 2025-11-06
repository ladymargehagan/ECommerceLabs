$(document).ready(function() {
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
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Brands response:', response);
            if (response && response.success && response.data) {
                console.log('Brands data:', response.data);
                displayBrands(response.data);
            } else {
                console.log('No brands in response');
                $('#brandsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>No Brands Found</h4>
                        <p class="text-muted">Start by adding your first brand.</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading brands:', error, xhr.responseText);
            $('#brandsContainer').html(`
                <div class="col-12 text-center py-5">
                    <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4>Error Loading Brands</h4>
                    <p class="text-muted">Please refresh the page and try again.</p>
                    <p class="text-muted"><small>Error: ${error}</small></p>
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

// Display brands function - organized by categories
function displayBrands(data) {
    console.log('Display brands called with:', data);
    if (!data || !Array.isArray(data) || data.length === 0) {
        console.log('No data to display');
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
        return;
    }

    // Group data by category
    const categoriesMap = {};
    data.forEach(function(item) {
        if (!item) return;
        const catId = item.cat_id !== null && item.cat_id !== undefined ? item.cat_id : 0;
        if (!categoriesMap[catId]) {
            categoriesMap[catId] = {
                cat_id: item.cat_id,
                cat_name: item.cat_name || 'All Brands',
                cat_image: item.cat_image,
                brands: []
            };
        }
        if (item.brand_id) {
            categoriesMap[catId].brands.push({
                brand_id: item.brand_id,
                brand_name: item.brand_name,
                brand_image: item.brand_image
            });
        }
    });

    console.log('Categories map:', categoriesMap);
    // Display organized by categories
    displayBrandsGroupedByCategories(categoriesMap);
}

// Display brands grouped by categories
function displayBrandsGroupedByCategories(categoriesMap) {
    let html = '';
    const categories = Object.values(categoriesMap);
    
    if (categories.length === 0) {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
        return;
    }
    
    categories.forEach(function(category) {
        if (category.brands.length === 0) {
            return; // Skip categories with no brands
        }
        
        html += `
            <div class="col-12 mb-4">
                <div class="category-section">
                    <h4 class="category-header">
                        <i class="fa fa-tags text-primary me-2"></i>
                        ${escapeHtml(category.cat_name)}
                    </h4>
                    <div class="row">
        `;
        
        // Display brands under this category
        category.brands.forEach(function(brand) {
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
    let html = '';
    brands.forEach(function(brand) {
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

    $('#brandsContainer').html(html);
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