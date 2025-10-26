$(document).ready(function() {
    // Load brands on page load
    loadBrands();

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

// Display brands function
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
                            <small>Brand ID: ${brand.brand_id}</small>
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
