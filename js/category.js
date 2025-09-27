// Category Management JavaScript Functions
$(document).ready(function() {
    // Load categories on page load
    loadCategories();
    
    // Add category form submission event
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        addCategory();
    });
    
    // Update category form submission event
    $('#updateCategoryForm').on('submit', function(e) {
        e.preventDefault();
        updateCategory();
    });
    
    // Delete category button click event
    $(document).on('click', '.delete-category-btn', function() {
        const catId = $(this).data('cat-id');
        const catName = $(this).data('cat-name');
        deleteCategory(catId, catName);
    });
    
    // Edit category button click event
    $(document).on('click', '.edit-category-btn', function() {
        const catId = $(this).data('cat-id');
        const catName = $(this).data('cat-name');
        editCategory(catId, catName);
    });
    
    // Cancel edit button click event
    $('#cancelEditBtn').on('click', function() {
        hideUpdateForm();
    });
});

// Load all categories from database
function loadCategories() {
    $.ajax({
        url: 'actions/fetch_category_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCategories(response.data);
            } else {
                showAlert('Error', response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error', 'Failed to load categories. Please try again.', 'danger');
        }
    });
}

// Display categories in the table function
function displayCategories(categories) {
    const tbody = $('#categoriesTable tbody');
    tbody.empty();
    
    if (categories && categories.length > 0) {
        categories.forEach(function(category) {
            const row = `
                <tr>
                    <td>${category.cat_id}</td>
                    <td>${escapeHtml(category.cat_name)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-category-btn me-2" 
                                data-cat-id="${category.cat_id}" 
                                data-cat-name="${escapeHtml(category.cat_name)}">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-category-btn" 
                                data-cat-id="${category.cat_id}" 
                                data-cat-name="${escapeHtml(category.cat_name)}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    } else {
        tbody.append('<tr><td colspan="3" class="text-center">No categories found.</td></tr>');
    }
}

// Add new category function
function addCategory() {
    const catName = $('#addCatName').val().trim();
    
    // Validating input
    if (!validateCategoryName(catName)) {
        return;
    }
    
    // Showing loading state
    const submitBtn = $('#addCategoryBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    
    $.ajax({
        url: 'actions/add_category_action.php',
        method: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
            cat_name: catName
        }),
        success: function(response) {
            if (response.success) {
                showAlert('Success', response.message, 'success');
                $('#addCategoryForm')[0].reset();
                loadCategories(); // Reload categories
            } else {
                showAlert('Error', response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error', 'Failed to add category. Please try again.', 'danger');
        },
        complete: function() {
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Update category function
function updateCategory() {
    const catId = $('#updateCatId').val();
    const catName = $('#updateCatName').val().trim();
    
    // Validate input
    if (!validateCategoryName(catName)) {
        return;
    }
    
    // Show loading state after clicking the update button
    const submitBtn = $('#updateCategoryBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    $.ajax({
        url: 'actions/update_category_action.php',
        method: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({
            cat_id: catId,
            cat_name: catName
        }),
        success: function(response) {
            if (response.success) {
                showAlert('Success', response.message, 'success');
                hideUpdateForm();
                loadCategories(); // Reload categories
            } else {
                showAlert('Error', response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error', 'Failed to update category. Please try again.', 'danger');
        },
        complete: function() {
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Delete category function
function deleteCategory(catId, catName) {
    if (confirm(`Are you sure you want to delete the category "${catName}"? This action cannot be undone.`)) {
        $.ajax({
            url: 'actions/delete_category_action.php',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                cat_id: catId
            }),
            success: function(response) {
                if (response.success) {
                    showAlert('Success', response.message, 'success');
                    loadCategories(); // Reload categories
                } else {
                    showAlert('Error', response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showAlert('Error', 'Failed to delete category. Please try again.', 'danger');
            }
        });
    }
}

// Edit category (populate update form) function
function editCategory(catId, catName) {
    $('#updateCatId').val(catId);
    $('#updateCatName').val(catName);
    $('#updateCategoryForm').show();
    $('#addCategoryForm').hide();
    
    // Scroll to update form
    $('html, body').animate({
        scrollTop: $('#updateCategoryForm').offset().top - 100
    }, 500);
}

// Hide update form function
function hideUpdateForm() {
    $('#updateCategoryForm').hide();
    $('#addCategoryForm').show();
    $('#updateCategoryForm')[0].reset();
}

// Validate category name function
function validateCategoryName(catName) {
    if (!catName) {
        showAlert('Validation Error', 'Category name is required.', 'warning');
        return false;
    }
    
    if (catName.length > 100) {
        showAlert('Validation Error', 'Category name must be 100 characters or less.', 'warning');
        return false;
    }
    
    // Checking for valid characters (letters, numbers, spaces, hyphens, underscores)
    const validPattern = /^[a-zA-Z0-9\s\-_]+$/;
    if (!validPattern.test(catName)) {
        showAlert('Validation Error', 'Category name can only contain letters, numbers, spaces, hyphens, and underscores.', 'warning');
        return false;
    }
    
    return true;
}

// Show alert message function
function showAlert(title, message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Removing existing alerts
    $('.alert').remove();
    
    // Add new alert
    $('#alertContainer').html(alertHtml);
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
}

// Escape HTML to prevent XSS
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
