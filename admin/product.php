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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css?v=2.0" rel="stylesheet">
    <link href="../css/product.css?v=2.0" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h2><i class="fa fa-box me-2"></i>Product Management</h2>
                    <div>
                        <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fa fa-plus me-1"></i>Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Display -->
        <div class="row" id="productsContainer">
            <!-- Products will be loaded here via AJAX -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading products...</p>
            </div>
        </div>

        <!-- Add/Edit Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addProductForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productTitle" class="form-label">Product Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="productTitle" name="productTitle" required maxlength="200">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productPrice" class="form-label">Product Price <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="productPrice" name="productPrice" step="0.01" min="0" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="productCategory" name="productCategory" required>
                                            <option value="">Select a category...</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productBrand" class="form-label">Brand <span class="text-danger">*</span></label>
                                        <select class="form-control" id="productBrand" name="productBrand" required>
                                            <option value="">Select a brand...</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Product Description</label>
                                <textarea class="form-control" id="productDescription" name="productDescription" rows="3" maxlength="500"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="productKeywords" class="form-label">Product Keywords</label>
                                <input type="text" class="form-control" id="productKeywords" name="productKeywords" maxlength="100" placeholder="Separate keywords with commas">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="productImage" name="productImage" accept="image/*">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="imagePreview" class="mb-3" style="display: none;">
                                <label class="form-label">Image Preview</label>
                                <div class="text-center">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editProductForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="editProductId" name="productId">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editProductTitle" class="form-label">Product Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editProductTitle" name="productTitle" required maxlength="200">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editProductPrice" class="form-label">Product Price <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="editProductPrice" name="productPrice" step="0.01" min="0" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editProductCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editProductCategory" name="productCategory" required>
                                            <option value="">Select a category...</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editProductBrand" class="form-label">Brand <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editProductBrand" name="productBrand" required>
                                            <option value="">Select a brand...</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductDescription" class="form-label">Product Description</label>
                                <textarea class="form-control" id="editProductDescription" name="productDescription" rows="3" maxlength="500"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductKeywords" class="form-label">Product Keywords</label>
                                <input type="text" class="form-control" id="editProductKeywords" name="productKeywords" maxlength="100" placeholder="Separate keywords with commas">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editProductImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="editProductImage" name="productImage" accept="image/*">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="editImagePreview" class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div class="text-center">
                                    <img id="editPreviewImg" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteProductModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-trash me-2"></i>Delete Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product?</p>
                        <p class="text-muted">This action cannot be undone.</p>
                        <input type="hidden" id="deleteProductId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fa fa-trash me-1"></i>Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
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
