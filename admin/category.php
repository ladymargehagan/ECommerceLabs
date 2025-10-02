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
    <title>Category Management - Flavo Spice Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <style>
        .category-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .action-buttons {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .category-card:hover .action-buttons {
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <div class="nav-header">
        <div class="nav-links">
            <span class="brand">flavo</span>
            <a href="../index.php">HOME</a>
            <a href="dashboard.php">DASHBOARD</a>
            <a href="#shop">SHOP</a>
            <a href="#story">OUR STORY</a>
            <a href="#contact">CONTACT US</a>
            <button class="btn btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fa fa-plus me-1"></i>ADD CATEGORY
            </button>
        </div>
    </div>

    <div class="container" style="padding-top: 160px;">
        <div class="main-container">
            <div class="hero-section">
                <h1><i class="fa fa-tags me-3"></i>CATEGORY MANAGEMENT</h1>
                <p class="subtitle">Organize your spice blends and product categories</p>
            </div>

            <!-- Categories Display -->
            <div class="row mt-4" id="categoriesContainer">
                <!-- Categories will be loaded here via AJAX -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border" style="color: var(--primary-green);" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2" style="color: var(--text-light);">Loading spice categories...</p>
                </div>
            </div>
        </div>
    </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addCategoryForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoryName" name="categoryName" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editCategoryForm">
                        <div class="modal-body">
                            <input type="hidden" id="editCategoryId" name="categoryId">
                            <div class="mb-3">
                                <label for="editCategoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editCategoryName" name="categoryName" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-trash me-2"></i>Delete Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category?</p>
                        <p class="text-muted">This action cannot be undone.</p>
                        <input type="hidden" id="deleteCategoryId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fa fa-trash me-1"></i>Delete Category
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
    <script src="../js/category.js"></script>
</body>
</html>
