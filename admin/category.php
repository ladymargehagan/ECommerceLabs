<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Category Management - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/index.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fa fa-tags me-2"></i>Category Management</h1>
                    <p class="mb-0">Manage product categories for Taste of Africa</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../index.php" class="btn btn-light me-2">
                        <i class="fa fa-home me-1"></i>Home
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-light">
                        <i class="fa fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <div class="row">
            <!-- Add Category Form -->
            <div class="col-lg-4 mb-4">
                <div class="card category-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-plus me-2"></i>Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="addCategoryForm">
                            <div class="mb-3">
                                <label for="addCatName" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="addCatName" name="cat_name" 
                                       placeholder="e.g., Spices & Seasonings" required maxlength="100">
                                <div class="form-text">Enter a unique category name (max 100 characters)</div>
                            </div>
                            <button type="submit" class="btn btn-custom w-100" id="addCategoryBtn">
                                <i class="fa fa-plus me-2"></i>Add Category
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Update Category Form (Hidden by default) -->
                <div class="card category-card" id="updateCategoryForm" style="display: none;">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fa fa-edit me-2"></i>Update Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="updateCategoryForm">
                            <input type="hidden" id="updateCatId" name="cat_id">
                            <div class="mb-3">
                                <label for="updateCatName" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="updateCatName" name="cat_name" 
                                       placeholder="e.g., Spices & Seasonings" required maxlength="100">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning" id="updateCategoryBtn">
                                    <i class="fa fa-save me-2"></i>Update Category
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                                    <i class="fa fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Categories List -->
            <div class="col-lg-8">
                <div class="card category-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa fa-list me-2"></i>Existing Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="categoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Categories -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories for Taste of Africa -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card category-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fa fa-lightbulb me-2"></i>Suggested Categories for Taste of Africa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Spices & Seasonings</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Grains & Cereals</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Sauces & Condiments</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Beverages</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Snacks & Treats</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Cooking Ingredients</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Traditional Foods</span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary me-1 mb-1">Kitchen Tools</span>
                            </div>
                        </div>
                        <small class="text-muted">Click on any suggested category to add it to your system</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/category.js"></script>
    
    <script>

        $(document).ready(function() {
            $('.badge').on('click', function() {
                const categoryName = $(this).text();
                $('#addCatName').val(categoryName);
                $('#addCategoryForm').show();
                $('#updateCategoryForm').hide();
            });
        });
    </script>
</body>
</html>
