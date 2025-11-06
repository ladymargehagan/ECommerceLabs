$(document).ready(function() {
    loadBrands();

    // Image preview for add form
    $('#brandImage').on('change', function() {
        previewImage(this, '#previewBrandImg', '#brandImagePreview');
    });

    // Image preview for edit form
    $('#editBrandImage').on('change', function() {
        previewImage(this, '#editPreviewBrandImg', '#editBrandImagePreview');
    });

    $('#addBrandForm').on('submit', function(e) {
        e.preventDefault();
        addBrand();
    });

    $('#editBrandForm').on('submit', function(e) {
        e.preventDefault();
        updateBrand();
    });

    $('#confirmDelete').on('click', function() {
        deleteBrand();
    });

    $('#addBrandModal').on('hidden.bs.modal', function() {
        $('#addBrandForm')[0].reset();
        clearValidationErrors('#addBrandForm');
        $('#brandImagePreview').hide();
    });

    $('#editBrandModal').on('hidden.bs.modal', function() {
        $('#editBrandForm')[0].reset();
        clearValidationErrors('#editBrandForm');
    });
});

function loadBrands() {
    showLoading();
    
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            console.log('Brands fetch response:', response);
            
            if (response && response.success) {
                console.log('Brands data received:', response.data);
                displayBrands(response.data || []);
            } else {
                console.warn('Brands fetch returned unsuccessful response:', response);
                if (response && response.message) {
                    showAlert('error', 'Error', response.message);
                }
                displayBrands([]);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Brands fetch error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            // Try to parse response if it's JSON
            let errorMessage = 'Failed to load brands. Please try again.';
            if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Not JSON, use raw response or default message
                    if (xhr.status === 404) {
                        errorMessage = 'Brands endpoint not found. Please check the server configuration.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please check the server logs.';
                    }
                }
            }
            
            showAlert('error', 'Error Loading Brands', errorMessage);
            displayBrands([]);
        }
    });
}

function displayBrands(brands) {
    const container = $('#brandsContainer');
    
    // Ensure brands is an array
    if (!Array.isArray(brands)) {
        console.error('displayBrands: brands is not an array:', brands);
        brands = [];
    }
    
    if (brands.length === 0) {
        container.html(`
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
    
    console.log('Displaying', brands.length, 'brands');

    let html = '';
    brands.forEach(function(brand) {
        const imageSrc = brand.brand_image ? `../${brand.brand_image}` : '../uploads/placeholder.png';
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card brand-card h-100">
                    <div class="brand-image-container">
                        <img src="${imageSrc}" class="card-img-top brand-image" alt="${escapeHtml(brand.brand_name)}" onerror="this.src='../uploads/placeholder.png'">
                        <div class="brand-overlay">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}', '${brand.brand_image || ''}')" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa fa-star text-warning me-2"></i>
                            ${escapeHtml(brand.brand_name)}
                        </h5>
                        <p class="card-text text-muted">
                            <small><strong>Brand ID:</strong> ${brand.brand_id}</small>
                        </p>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function addBrand() {
    const form = $('#addBrandForm');
    const formData = new FormData(form[0]);
    
    if (!validateBrandForm(form)) {
        return;
    }

    showLoading();
    
    $.ajax({
        url: '../actions/add_brand_action.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Success', response.message);
                $('#addBrandModal').modal('hide');
                loadBrands();
            } else {
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showAlert('error', 'Error', 'Failed to add brand. Please try again.');
        }
    });
}

function updateBrand() {
    const form = $('#editBrandForm');
    const formData = new FormData(form[0]);
    
    if (!validateBrandForm(form)) {
        return;
    }

    showLoading();
    
    $.ajax({
        url: '../actions/update_brand_action.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Success', response.message);
                $('#editBrandModal').modal('hide');
                loadBrands();
            } else {
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showAlert('error', 'Error', 'Failed to update brand. Please try again.');
        }
    });
}

function deleteBrand() {
    const brandId = $('#deleteBrandId').val();
    
    if (!brandId) {
        showAlert('error', 'Error', 'Brand ID not found.');
        return;
    }

    showLoading();
    
    $.ajax({
        url: '../actions/delete_brand_action.php',
        type: 'POST',
        data: { brandId: brandId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Success', response.message);
                $('#deleteBrandModal').modal('hide');
                loadBrands();
            } else {
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            console.error('Delete brand error:', xhr.responseText);
            showAlert('error', 'Error', 'Failed to delete brand. Please check the console for details.');
        }
    });
}

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

function confirmDelete(brandId, brandName) {
    $('#deleteBrandId').val(brandId);
    $('#deleteBrandModal .modal-body p').html(`Are you sure you want to delete the brand "<strong>${escapeHtml(brandName)}</strong>"?`);
    $('#deleteBrandModal').modal('show');
}

function validateBrandForm(form) {
    let isValid = true;
    const brandName = form.find('input[name="brandName"]');
    
    clearValidationErrors(form);
    
    if (!brandName.val().trim()) {
        showFieldError(brandName, 'Brand name is required');
        isValid = false;
    } else if (brandName.val().trim().length < 2) {
        showFieldError(brandName, 'Brand name must be at least 2 characters');
        isValid = false;
    } else if (brandName.val().trim().length > 100) {
        showFieldError(brandName, 'Brand name must be less than 100 characters');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.addClass('is-invalid');
    field.siblings('.invalid-feedback').text(message);
}

function clearValidationErrors(form) {
    const $form = typeof form === 'string' ? $(form) : form;
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').text('');
}

function showLoading() {
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showAlert(type, title, message) {
    const icon = type === 'success' ? 'success' : 'error';
    const color = type === 'success' ? '#28a745' : '#dc3545';
    
    Swal.fire({
        icon: icon,
        title: title,
        text: message,
        confirmButtonColor: color,
        timer: type === 'success' ? 3000 : null,
        timerProgressBar: type === 'success'
    });
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
