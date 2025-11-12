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
    <title>Brand Management - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/brand.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="admin-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-tag me-2"></i>Brand Management</h2>
                    <div class="d-flex gap-3">
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Dashboard
                        </a>
                        <button class="btn" onclick="openAddModal()">
                            <i class="fas fa-plus me-1"></i>Add Brand
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Display -->
        <div class="container py-3">
            <div id="brandsContainer">
                <!-- Brands will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading brands...</p>
                </div>
            </div>
        </div>

        <!-- Add Brand Modal -->
        <div id="addBrandModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Brand</h5>
                        <button type="button" class="btn-close" onclick="closeAddModal()" aria-label="Close">×</button>
                    </div>
                    <form id="addBrandForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="brandName">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" id="brandName" name="brandName" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="brandImage">Brand Image</label>
                                <input type="file" id="brandImage" name="brandImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="brandImagePreview" class="mb-3" style="display: none;">
                                <label>Image Preview</label>
                                <div class="mt-2">
                                    <img id="previewBrandImg" src="" alt="Preview" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Add Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Brand Modal -->
        <div id="editBrandModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Brand</h5>
                        <button type="button" class="btn-close" onclick="closeEditModal()" aria-label="Close">×</button>
                    </div>
                    <form id="editBrandForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="editBrandId" name="brandId">
                            <div class="mb-3">
                                <label for="editBrandName">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" id="editBrandName" name="brandName" class="form-control" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editBrandImage">Brand Image</label>
                                <input type="file" id="editBrandImage" name="brandImage" accept="image/*" class="form-control">
                                <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div id="editBrandImagePreview" class="mb-3">
                                <label>Current Image</label>
                                <div class="mt-2">
                                    <img id="editPreviewBrandImg" src="" alt="Current Image" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                            <button type="submit" class="btn">
                                <i class="fas fa-save me-1"></i>Update Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteBrandModal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Delete Brand</h5>
                        <button type="button" class="btn-close" onclick="closeDeleteModal()" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this brand?</p>
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                        <input type="hidden" id="deleteBrandId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fas fa-trash me-1"></i>Delete Brand
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
    <script src="../js/brand.js?v=<?php echo time(); ?>"></script>
</body>
</html>
