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
    <link href="https://fonts.googleapis.com/css2?family=Teachers:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/brand.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <div>
                <div>
                    <h2><i></i>Brand Management</h2>
                    <div>
                        <a href="dashboard.php">
                            <i></i>Dashboard
                        </a>
                        <button data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i></i>Add Brand
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Display -->
        <div id="brandsContainer">
            <!-- Brands will be loaded here via AJAX -->
            <div>
                <div role="status">
                    <span>Loading...</span>
                </div>
                <p>Loading brands...</p>
            </div>
        </div>

        <!-- Add Brand Modal -->
        <div id="addBrandModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Add New Brand</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addBrandForm" enctype="multipart/form-data">
                        <div>
                            <div>
                                <label for="brandName">Brand Name <span>*</span></label>
                                <input type="text" id="brandName" name="brandName" required maxlength="100">
                                <div></div>
                            </div>
                            
                            <div>
                                <label for="brandImage">Brand Image</label>
                                <input type="file" id="brandImage" name="brandImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="brandImagePreview">
                                <label>Image Preview</label>
                                <div>
                                    <img id="previewBrandImg" src="" alt="Preview">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Add Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Brand Modal -->
        <div id="editBrandModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Edit Brand</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editBrandForm" enctype="multipart/form-data">
                        <div>
                            <input type="hidden" id="editBrandId" name="brandId">
                            <div>
                                <label for="editBrandName">Brand Name <span>*</span></label>
                                <input type="text" id="editBrandName" name="brandName" required maxlength="100">
                                <div></div>
                            </div>
                            
                            <div>
                                <label for="editBrandImage">Brand Image</label>
                                <input type="file" id="editBrandImage" name="brandImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="editBrandImagePreview">
                                <label>Current Image</label>
                                <div>
                                    <img id="editPreviewBrandImg" src="" alt="Current Image">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Update Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteBrandModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Delete Brand</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <div>
                        <p>Are you sure you want to delete this brand?</p>
                        <p>This action cannot be undone.</p>
                        <input type="hidden" id="deleteBrandId">
                    </div>
                    <div>
                        <button type="button" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDelete">
                            <i></i>Delete Brand
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
    <script src="../js/brand.js?v=<?php echo time(); ?>"></script>
</body>
</html>
