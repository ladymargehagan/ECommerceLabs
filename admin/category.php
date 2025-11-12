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
    <div>
        <!-- Header -->
        <div>
            <div>
                <div>
                    <h2><i></i>Category Management</h2>
                    <div>
                        <a href="dashboard.php">
                            <i></i>Dashboard
                        </a>
                        <button data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i></i>Add Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Display -->
        <div id="categoriesContainer">
            <!-- Categories will be loaded here via AJAX -->
            <div>
                <div role="status">
                    <span>Loading...</span>
                </div>
                <p>Loading categories...</p>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div id="addCategoryModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Add New Category</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addCategoryForm" enctype="multipart/form-data">
                        <div>
                            <div>
                                <label for="categoryName">Category Name <span>*</span></label>
                                <input type="text" id="categoryName" name="categoryName" required maxlength="100">
                                <div></div>
                            </div>
                            
                            <div>
                                <label for="categoryImage">Category Image</label>
                                <input type="file" id="categoryImage" name="categoryImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="categoryImagePreview">
                                <label>Image Preview</label>
                                <div>
                                    <img id="previewCategoryImg" src="" alt="Preview">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div id="editCategoryModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Edit Category</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editCategoryForm" enctype="multipart/form-data">
                        <div>
                            <input type="hidden" id="editCategoryId" name="categoryId">
                            <div>
                                <label for="editCategoryName">Category Name <span>*</span></label>
                                <input type="text" id="editCategoryName" name="categoryName" required maxlength="100">
                                <div></div>
                            </div>
                            
                            <div>
                                <label for="editCategoryImage">Category Image</label>
                                <input type="file" id="editCategoryImage" name="categoryImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="editCategoryImagePreview">
                                <label>Current Image</label>
                                <div>
                                    <img id="editPreviewCategoryImg" src="" alt="Current Image">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteCategoryModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Delete Category</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <div>
                        <p>Are you sure you want to delete this category?</p>
                        <p>This action cannot be undone.</p>
                        <input type="hidden" id="deleteCategoryId">
                    </div>
                    <div>
                        <button type="button" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDelete">
                            <i></i>Delete Category
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div>
            <div></div>
            <p>Processing...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>
</body>
</html>
