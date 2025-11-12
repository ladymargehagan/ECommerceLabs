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
    <title>Category Management - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="admin-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-tags me-2"></i>Category Management</h2>
                    <div class="d-flex gap-3">
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Dashboard
                        </a>
                        <button class="btn" onclick="openAddModal()">
                            <i class="fas fa-plus me-1"></i>Add Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Display -->
        <div class="container py-3">
            <div id="categoriesContainer">
                <!-- Categories will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading categories...</p>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div id="addCategoryModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Category</h5>
                        <button type="button" class="btn-close" onclick="closeAddModal()" aria-label="Close">×</button>
                    </div>
                    <form id="addCategoryForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="categoryName">Category Name <span class="text-danger">*</span></label>
                                <input type="text" id="categoryName" name="categoryName" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="categoryImage">Category Image</label>
                                <input type="file" id="categoryImage" name="categoryImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="categoryImagePreview" class="mb-3" style="display: none;">
                                <label>Image Preview</label>
                                <div class="mt-2">
                                    <img id="previewCategoryImg" src="" alt="Preview" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div id="editCategoryModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Category</h5>
                        <button type="button" class="btn-close" onclick="closeEditModal()" aria-label="Close">×</button>
                    </div>
                    <form id="editCategoryForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="editCategoryId" name="categoryId">
                            <div class="mb-3">
                                <label for="editCategoryName">Category Name <span class="text-danger">*</span></label>
                                <input type="text" id="editCategoryName" name="categoryName" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editCategoryImage">Category Image</label>
                                <input type="file" id="editCategoryImage" name="categoryImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="editCategoryImagePreview" class="mb-3">
                                <label>Current Image</label>
                                <div class="mt-2">
                                    <img id="editPreviewCategoryImg" src="" alt="Current Image" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteCategoryModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Delete Category</h5>
                        <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category?</p>
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                        <input type="hidden" id="deleteCategoryId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash me-1"></i>Delete Category
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
    <script src="../js/category.js"></script>
</body>
</html>
