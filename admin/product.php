<?php
require_once '../settings/core.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SESSION['role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/product.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="admin-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-box me-2"></i>Product Management</h2>
                    <div class="d-flex gap-3">
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Dashboard
                        </a>
                        <button class="btn" onclick="openAddModal()">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Display -->
        <div class="container py-3">
            <div id="productsContainer">
                <!-- Products will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading products...</p>
                </div>
            </div>
        </div>

        <!-- Add/Edit Product Modal -->
        <div id="addProductModal" class="modal" tabindex="-1">
            <div class="modal-dialog" style="max-width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Product</h5>
                        <button type="button" class="btn-close" onclick="closeAddModal()" aria-label="Close">×</button>
                    </div>
                    <form id="addProductForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productTitle">Product Title <span class="text-danger">*</span></label>
                                    <input type="text" id="productTitle" name="productTitle" class="form-control" required maxlength="200">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productPrice">Product Price <span class="text-danger">*</span></label>
                                    <input type="number" id="productPrice" name="productPrice" class="form-control" step="0.01" min="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory">Category <span class="text-danger">*</span></label>
                                    <select id="productCategory" name="productCategory" class="form-control" required>
                                        <option value="">Select a category...</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productBrand">Brand <span class="text-danger">*</span></label>
                                    <select id="productBrand" name="productBrand" class="form-control" required>
                                        <option value="">Select a brand...</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="productDescription">Product Description</label>
                                <textarea id="productDescription" name="productDescription" class="form-control" rows="3" maxlength="500"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="productKeywords">Product Keywords</label>
                                <input type="text" id="productKeywords" name="productKeywords" class="form-control" maxlength="100" placeholder="Separate keywords with commas">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="productImage">Product Image</label>
                                <input type="file" id="productImage" name="productImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="imagePreview" class="mb-3" style="display: none;">
                                <label>Image Preview</label>
                                <div class="mt-2">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editProductModal" class="modal" tabindex="-1">
            <div class="modal-dialog" style="max-width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Product</h5>
                        <button type="button" class="btn-close" onclick="closeEditModal()" aria-label="Close">×</button>
                    </div>
                    <form id="editProductForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="editProductId" name="productId">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="editProductTitle">Product Title <span class="text-danger">*</span></label>
                                    <input type="text" id="editProductTitle" name="productTitle" class="form-control" required maxlength="200">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="editProductPrice">Product Price <span class="text-danger">*</span></label>
                                    <input type="number" id="editProductPrice" name="productPrice" class="form-control" step="0.01" min="0" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="editProductCategory">Category <span class="text-danger">*</span></label>
                                    <select id="editProductCategory" name="productCategory" class="form-control" required>
                                        <option value="">Select a category...</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="editProductBrand">Brand <span class="text-danger">*</span></label>
                                    <select id="editProductBrand" name="productBrand" class="form-control" required>
                                        <option value="">Select a brand...</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductDescription">Product Description</label>
                                <textarea id="editProductDescription" name="productDescription" class="form-control" rows="3" maxlength="500"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductKeywords">Product Keywords</label>
                                <input type="text" id="editProductKeywords" name="productKeywords" class="form-control" maxlength="100" placeholder="Separate keywords with commas">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductImage">Product Image</label>
                                <input type="file" id="editProductImage" name="productImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="editImagePreview" class="mb-3">
                                <label>Current Image</label>
                                <div class="mt-2">
                                    <img id="editPreviewImg" src="" alt="Current Image" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteProductModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Delete Product</h5>
                        <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product?</p>
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                        <input type="hidden" id="deleteProductId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash me-1"></i>Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Processing...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/product.js"></script>
</body>
</html>
