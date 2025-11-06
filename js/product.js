$(document).ready(function() {
    // Load products and form data on page load
    loadProducts();
    loadFormData();

    // Image preview for add form
    $('#productImage').on('change', function() {
        previewImage(this, '#previewImg', '#imagePreview');
    });

    // Image preview for edit form - show new image preview when file is selected
    $('#editProductImage').on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Show the new selected image preview
                $('#editPreviewImg').attr('src', e.target.result);
                $('#editImagePreview').show();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Add Product Form Submission
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);

        if (!validateProductForm(formData)) {
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/add_product_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    let message = response.message;
                    if (response.image_warning) {
                        message += '\n\n' + response.image_warning;
                    }
                    
                    Swal.fire({
                        icon: response.image_warning ? 'warning' : 'success',
                        title: response.image_warning ? 'Product Created with Warning' : 'Success!',
                        text: message,
                        timer: response.image_warning ? 4000 : 2000,
                        showConfirmButton: !response.image_warning
                    }).then(() => {
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        clearFieldErrors();
                        $('#imagePreview').hide();
                        // Force reload products to show the new image
                        loadProducts();
                    });
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
                    text: 'An error occurred while adding the product'
                });
            }
        });
    });

    // Edit Product Form Submission
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);

        if (!validateProductForm(formData)) {
            return;
        }

        showLoading();
        
        $.ajax({
            url: '../actions/update_product_action.php',
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
                    }).then(() => {
                        $('#editProductModal').modal('hide');
                        clearFieldErrors();
                        // Reset the image input
                        $('#editProductImage').val('');
                        // Force reload products to show updated image
                        loadProducts();
                    });
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
                    text: 'An error occurred while updating the product'
                });
            }
        });
    });

    // Delete Product Confirmation
    $('#confirmDelete').on('click', function() {
        const productId = $('#deleteProductId').val();
        
        showLoading();
        
        $.ajax({
            url: '../actions/delete_product_action.php',
            method: 'POST',
            data: { productId: productId },
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
                    $('#deleteProductModal').modal('hide');
                    loadProducts();
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
                    text: 'An error occurred while deleting the product'
                });
            }
        });
    });

    // Clear form when modal is closed
    $('#addProductModal').on('hidden.bs.modal', function() {
        $('#addProductForm')[0].reset();
        clearFieldErrors();
        $('#imagePreview').hide();
    });

    $('#editProductModal').on('hidden.bs.modal', function() {
        $('#editProductForm')[0].reset();
        clearFieldErrors();
    });
});

// Load products function
function loadProducts() {
    $.ajax({
        url: '../actions/fetch_product_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                displayProducts(response.data);
            } else {
                $('#productsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-box fa-3x text-muted mb-3"></i>
                        <h4>No Products Found</h4>
                        <p class="text-muted">Start by adding your first product.</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#productsContainer').html(`
                <div class="col-12 text-center py-5">
                    <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h4>Error Loading Products</h4>
                    <p class="text-muted">Please refresh the page and try again.</p>
                </div>
            `);
        }
    });
}

// Load form data (categories and brands)
function loadFormData() {
    // Load categories
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

    // Load brands
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateBrandSelects(response.data);
            }
        },
        error: function() {
            console.error('Error loading brands');
        }
    });
}

// Populate category dropdowns
function populateCategorySelects(categories) {
    const addSelect = $('#productCategory');
    const editSelect = $('#editProductCategory');
    
    // Clear existing options except the first one
    addSelect.find('option:not(:first)').remove();
    editSelect.find('option:not(:first)').remove();
    
    categories.forEach(function(category) {
        addSelect.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
        editSelect.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
    });
}

// Populate brand dropdowns
function populateBrandSelects(brands) {
    const addSelect = $('#productBrand');
    const editSelect = $('#editProductBrand');
    
    // Clear existing options except the first one
    addSelect.find('option:not(:first)').remove();
    editSelect.find('option:not(:first)').remove();
    
    brands.forEach(function(brand) {
        addSelect.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
        editSelect.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
    });
}

// Display products function
function displayProducts(products) {
    if (!products || products.length === 0) {
        $('#productsContainer').html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-box fa-3x text-muted mb-3"></i>
                <h4>No Products Found</h4>
                <p class="text-muted">Start by adding your first product.</p>
            </div>
        `);
        return;
    }

    let html = '';
    products.forEach(function(product) {
        // Construct image path - product_image should be like "uploads/u{user_id}/p{product_id}/filename"
        let imageSrc = '../uploads/placeholder.png';
        if (product.product_image && product.product_image.trim() !== '') {
            // Ensure the path doesn't already start with ../
            if (product.product_image.startsWith('../')) {
                imageSrc = product.product_image;
            } else if (product.product_image.startsWith('uploads/')) {
                imageSrc = '../' + product.product_image;
            } else {
                imageSrc = '../uploads/' + product.product_image;
            }
            // Debug logging
            console.log('Product ID:', product.product_id, 'Image path:', product.product_image, 'Display path:', imageSrc);
        } else {
            console.log('Product ID:', product.product_id, 'No image - using placeholder');
        }
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card product-card h-100">
                    <div class="product-image-container">
                        <img src="${imageSrc}" class="card-img-top product-image" alt="${escapeHtml(product.product_title)}" onerror="console.error('Image failed to load:', '${imageSrc}'); this.src='../uploads/placeholder.png';">
                        <div class="product-overlay">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(${product.product_id})" title="Edit Product">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.product_id})" title="Delete Product">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${product.product_title}</h5>
                        <p class="card-text text-muted">
                            <small><strong>Category:</strong> ${product.cat_name || 'No Category'}</small><br>
                            <small><strong>Brand:</strong> ${product.brand_name || 'No Brand'}</small><br>
                            <small><strong>Price:</strong> $${parseFloat(product.product_price).toFixed(2)}</small>
                        </p>
                        ${product.product_desc ? `<p class="card-text">${product.product_desc.substring(0, 100)}${product.product_desc.length > 100 ? '...' : ''}</p>` : ''}
                    </div>
                </div>
            </div>
        `;
    });

    $('#productsContainer').html(html);
}

// Edit product function
function editProduct(productId) {
    $.ajax({
        url: '../actions/fetch_product_action.php',
        method: 'GET',
        data: { productId: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const product = response.data.find(p => p.product_id == productId);
                if (product) {
                    $('#editProductId').val(product.product_id);
                    $('#editProductTitle').val(product.product_title);
                    $('#editProductPrice').val(product.product_price);
                    $('#editProductCategory').val(product.product_cat);
                    $('#editProductBrand').val(product.product_brand);
                    $('#editProductDescription').val(product.product_desc || '');
                    $('#editProductKeywords').val(product.product_keywords || '');
                    
                    // Set current image
                    let imageSrc = '../uploads/placeholder.png';
                    if (product.product_image && product.product_image.trim() !== '') {
                        // Ensure the path doesn't already start with ../
                        if (product.product_image.startsWith('../')) {
                            imageSrc = product.product_image;
                        } else if (product.product_image.startsWith('uploads/')) {
                            imageSrc = '../' + product.product_image;
                        } else {
                            imageSrc = '../uploads/' + product.product_image;
                        }
                    }
                    $('#editPreviewImg').attr('src', imageSrc);
                    $('#editImagePreview').show();
                    
                    $('#editProductModal').modal('show');
                }
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load product data'
            });
        }
    });
}

// Delete product function
function deleteProduct(productId) {
    $('#deleteProductId').val(productId);
    $('#deleteProductModal').modal('show');
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

// Validate product form
function validateProductForm(formData) {
    let isValid = true;
    
    // Check required fields
    const requiredFields = ['productTitle', 'productPrice', 'productCategory', 'productBrand'];
    
    requiredFields.forEach(function(field) {
        const value = formData.get(field);
        if (!value || value.trim() === '') {
            showFieldError(`#${field}`, `${field.replace('product', 'Product ')} is required`);
            isValid = false;
        } else {
            clearFieldError(`#${field}`);
        }
    });
    
    // Validate price
    const price = parseFloat(formData.get('productPrice'));
    if (isNaN(price) || price <= 0) {
        showFieldError('#productPrice', 'Valid product price is required');
        isValid = false;
    }
    
    return isValid;
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
    $(fieldId).removeClass('is-invalid');
    $(fieldId).siblings('.invalid-feedback').text('');
}

function clearFieldErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}
