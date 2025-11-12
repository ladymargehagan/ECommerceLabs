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
                    $('#brandImagePreview').hide();
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
        $('#brandImagePreview').hide();
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
            if (response.success && response.data && Array.isArray(response.data)) {
                if (response.data.length > 0) {
                    displayBrands(response.data);
                } else {
                    $('#brandsContainer').html(`
                        <div class="col-12 text-center py-5">
                            <i class="fa fa-star fa-3x text-muted mb-3"></i>
                            <h4>No Brands Found</h4>
                            <p class="text-muted">Start by adding your first brand.</p>
                        </div>
                    `);
                }
            } else {
                $('#brandsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>No Brands Found</h4>
                        <p class="text-muted">${response.message || 'Start by adding your first brand.'}</p>
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

// Display brands function - SIMPLE DISPLAY ONLY, NO CATEGORY GROUPING
function displayBrands(brands) {
    console.log('displayBrands called with:', brands);
    if (!brands || !Array.isArray(brands) || brands.length === 0) {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
        return;
    }

    // DIRECTLY display brands in simple grid - NO CATEGORY GROUPING
    displayBrandsSimple(brands);
}

// Get proper image path
function getImagePath(brandImage) {
    if (!brandImage || brandImage.trim() === '') {
        return '../uploads/placeholder.png';
    }
    
    // Clean the path
    const cleanPath = brandImage.trim();
    
    // If path already starts with http or /, use as is
    if (cleanPath.startsWith('http') || cleanPath.startsWith('/')) {
        return cleanPath;
    }
    
    // If path starts with 'uploads/', prepend ../
    if (cleanPath.startsWith('uploads/')) {
        return `../${cleanPath}`;
    }
    
    // Otherwise, assume it's a relative path from root
    return `../${cleanPath}`;
}

// Simple display - NO CATEGORY GROUPING - JUST BRANDS IN A GRID
function displayBrandsSimple(brands) {
    console.log('displayBrandsSimple called - NO CATEGORIES');
    if (!brands || !Array.isArray(brands) || brands.length === 0) {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    brands.forEach(function(brand) {
        if (!brand || !brand.brand_id || !brand.brand_name) {
            return; // Skip invalid brand entries
        }
        
        const imageSrc = getImagePath(brand.brand_image || '');
        const brandName = escapeHtml(brand.brand_name || '');
        const brandImage = escapeHtml((brand.brand_image || '').replace(/'/g, "\\'"));
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card brand-card h-100">
                    <div class="brand-image-container">
                        <img src="${imageSrc}" 
                             class="card-img-top brand-image" 
                             alt="${brandName}" 
                             onerror="this.onerror=null; this.src='../uploads/placeholder.png';">
                        <div class="brand-overlay">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick="editBrand(${brand.brand_id}, '${brandName.replace(/'/g, "\\'")}', '${brandImage}')" 
                                        title="Edit Brand">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteBrand(${brand.brand_id})" 
                                        title="Delete Brand">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa fa-star text-warning me-2"></i>
                            ${brandName}
                        </h5>
                        <p class="card-text text-muted">
                            <small><strong>Brand ID:</strong> ${brand.brand_id}</small>
                        </p>
                    </div>
                </div>
            </div>
        `;
    });

    if (html === '') {
        $('#brandsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-star fa-3x text-muted mb-3"></i>
                <h4>No Brands Found</h4>
                <p class="text-muted">Start by adding your first brand.</p>
            </div>
        `);
    } else {
        $('#brandsContainer').html(html);
    }
}

// Edit brand function
function editBrand(brandId, brandName, brandImage = '') {
    $('#editBrandId').val(brandId);
    $('#editBrandName').val(brandName);
    
    // Set current image preview
    const imageSrc = getImagePath(brandImage);
    $('#editPreviewBrandImg').attr('src', imageSrc);
    $('#editBrandImagePreview').show();
    
    $('#editBrandModal').modal('show');
}

// Delete brand function
function deleteBrand(brandId) {
    $('#deleteBrandId').val(brandId);
    $('#deleteBrandModal').modal('show');
}

// Utility functions
function showLoading() {
    $('#loadingOverlay').css('display', 'flex');
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
    field.siblings('.invalid-feedback').text('');
}

function clearFieldErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

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
    
    // Check brand name in add form
    const brandName = formData.get('brandName');
    if (!brandName || brandName.trim() === '') {
        showFieldError('#brandName', 'Brand name is required');
        isValid = false;
    } else {
        clearFieldError('#brandName');
    }
    
    // Check brand name in edit form
    const editBrandName = $('#editBrandName').val();
    if ($('#editBrandModal').hasClass('show') && (!editBrandName || editBrandName.trim() === '')) {
        showFieldError('#editBrandName', 'Brand name is required');
        isValid = false;
    } else if ($('#editBrandModal').hasClass('show')) {
        clearFieldError('#editBrandName');
    }
    
    return isValid;
}