$(document).ready(function() {
    loadCategories();

    // Image preview for add form
    $('#categoryImage').on('change', function() {
        previewImage(this, '#previewCategoryImg', '#categoryImagePreview');
    });

    // Image preview for edit form
    $('#editCategoryImage').on('change', function() {
        previewImage(this, '#editPreviewCategoryImg', '#editCategoryImagePreview');
    });

    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        addCategory();
    });

    $('#editCategoryForm').on('submit', function(e) {
        e.preventDefault();
        updateCategory();
    });

    $('#confirmDelete').on('click', function() {
        deleteCategory();
    });

    $('#addCategoryModal').on('hidden.bs.modal', function() {
        $('#addCategoryForm')[0].reset();
        clearValidationErrors('#addCategoryForm');
        $('#categoryImagePreview').hide();
    });

    $('#editCategoryModal').on('hidden.bs.modal', function() {
        $('#editCategoryForm')[0].reset();
        clearValidationErrors('#editCategoryForm');
    });
});

function loadCategories() {
    showLoading();
    
    $.ajax({
        url: '../actions/fetch_category_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                displayCategories(response.data);
            } else {
                displayCategories([]);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            displayCategories([]);
        }
    });
}

function displayCategories(categories) {
    const container = $('#categoriesContainer');
    
    if (categories.length === 0) {
        container.html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-tags fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Categories Found</h4>
                <p class="text-muted">Start by adding your first category!</p>
                <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fa fa-plus me-1"></i>Add Category
                </button>
            </div>
        `);
        return;
    }

    let html = '';
    categories.forEach(function(category) {
        const imageSrc = category.cat_image ? `../${category.cat_image}` : '../uploads/placeholder.png';
        
        html += `
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card category-card h-100">
                    <div class="category-image-container">
                        <img src="${imageSrc}" class="card-img-top category-image" alt="${escapeHtml(category.cat_name)}" onerror="this.src='../uploads/placeholder.png'">
                        <div class="category-overlay">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}', '${category.cat_image || ''}')" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${category.cat_id}, '${escapeHtml(category.cat_name)}')" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <i class="fa fa-tag me-2 text-primary"></i>
                            ${escapeHtml(category.cat_name)}
                        </h5>
                        <div class="mt-auto">
                            <small class="text-muted">
                                <i class="fa fa-calendar me-1"></i>
                                Created: ${formatDate(category.created_at || new Date())}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function addCategory() {
    const form = $('#addCategoryForm');
    const formData = new FormData(form[0]);
    const imageFile = formData.get('categoryImage');
    
    if (!validateCategoryForm(form)) {
        return;
    }

    showLoading();
    
    // Remove image from form data for category creation
    formData.delete('categoryImage');
    
    $.ajax({
        url: '../actions/add_category_action.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // If category created successfully and image was selected, upload image
                if (imageFile && imageFile.size > 0) {
                    uploadCategoryImage(response.category_id, imageFile);
                } else {
                    hideLoading();
                    showAlert('success', 'Success', response.message);
                    $('#addCategoryModal').modal('hide');
                    $('#addCategoryForm')[0].reset();
                    clearValidationErrors('#addCategoryForm');
                    $('#categoryImagePreview').hide();
                    loadCategories();
                }
            } else {
                hideLoading();
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showAlert('error', 'Error', 'Failed to add category. Please try again.');
        }
    });
}

function updateCategory() {
    const form = $('#editCategoryForm');
    const formData = new FormData(form[0]);
    const imageFile = formData.get('categoryImage');
    const categoryId = formData.get('categoryId');
    
    if (!validateCategoryForm(form)) {
        return;
    }

    showLoading();
    
    // Remove image from form data for category update
    formData.delete('categoryImage');
    
    $.ajax({
        url: '../actions/update_category_action.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // If category updated successfully and new image was selected, upload image
                if (imageFile && imageFile.size > 0) {
                    uploadCategoryImage(categoryId, imageFile);
                } else {
                    hideLoading();
                    showAlert('success', 'Success', response.message);
                    $('#editCategoryModal').modal('hide');
                    clearValidationErrors('#editCategoryForm');
                    loadCategories();
                }
            } else {
                hideLoading();
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showAlert('error', 'Error', 'Failed to update category. Please try again.');
        }
    });
}

function deleteCategory() {
    const categoryId = $('#deleteCategoryId').val();
    
    if (!categoryId) {
        showAlert('error', 'Error', 'Category ID not found.');
        return;
    }

    showLoading();
    
    $.ajax({
        url: '../actions/delete_category_action.php',
        type: 'POST',
        data: { categoryId: categoryId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Success', response.message);
                $('#deleteCategoryModal').modal('hide');
                loadCategories();
            } else {
                showAlert('error', 'Error', response.message);
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            showAlert('error', 'Error', 'Failed to delete category. Please try again.');
        }
    });
}

function editCategory(categoryId, categoryName, categoryImage = '') {
    $('#editCategoryId').val(categoryId);
    $('#editCategoryName').val(categoryName);
    
    // Set current image preview
    if (categoryImage) {
        $('#editPreviewCategoryImg').attr('src', `../${categoryImage}`);
        $('#editCategoryImagePreview').show();
    } else {
        $('#editPreviewCategoryImg').attr('src', '../uploads/placeholder.png');
        $('#editCategoryImagePreview').show();
    }
    
    $('#editCategoryModal').modal('show');
}

function confirmDelete(categoryId, categoryName) {
    $('#deleteCategoryId').val(categoryId);
    $('#deleteCategoryModal .modal-body p').html(`Are you sure you want to delete the category "<strong>${escapeHtml(categoryName)}</strong>"?`);
    $('#deleteCategoryModal').modal('show');
}

function validateCategoryForm(form) {
    let isValid = true;
    const categoryName = form.find('input[name="categoryName"]');
    
    clearValidationErrors(form);
    
    if (!categoryName.val().trim()) {
        showFieldError(categoryName, 'Category name is required');
        isValid = false;
    } else if (categoryName.val().trim().length < 2) {
        showFieldError(categoryName, 'Category name must be at least 2 characters');
        isValid = false;
    } else if (categoryName.val().trim().length > 100) {
        showFieldError(categoryName, 'Category name must be less than 100 characters');
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
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
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

function uploadCategoryImage(categoryId, imageFile) {
    const formData = new FormData();
    formData.append('categoryImage', imageFile);
    formData.append('category_id', categoryId);
    
    $.ajax({
        url: '../actions/upload_category_image_action.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                showAlert('success', 'Success', 'Category and image uploaded successfully!');
                $('#addCategoryModal').modal('hide');
                $('#editCategoryModal').modal('hide');
                $('#addCategoryForm')[0].reset();
                $('#editCategoryForm')[0].reset();
                clearValidationErrors('#addCategoryForm');
                clearValidationErrors('#editCategoryForm');
                $('#categoryImagePreview').hide();
                $('#editCategoryImagePreview').hide();
                loadCategories();
            } else {
                showAlert('error', 'Error', response.message);
            }
        },
        error: function() {
            hideLoading();
            showAlert('error', 'Error', 'An error occurred while uploading the image');
        }
    });
}
