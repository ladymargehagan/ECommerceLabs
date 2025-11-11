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
    <link href="https://fonts.googleapis.com/css2?family=Teachers:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/product.css" rel="stylesheet">
    <link href="../css/category.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div>
        <!-- Header -->
        <div>
            <div>
                <div>
                    <h2><i></i>Product Management</h2>
                    <div>
                        <a href="dashboard.php">
                            <i></i>Dashboard
                        </a>
                        <button data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i></i>Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Display -->
        <div id="productsContainer">
            <!-- Products will be loaded here via AJAX -->
            <div>
                <div role="status">
                    <span>Loading...</span>
                </div>
                <p>Loading products...</p>
            </div>
        </div>

        <!-- Add/Edit Product Modal -->
        <div id="addProductModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Add New Product</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addProductForm" enctype="multipart/form-data">
                        <div>
                            <div>
                                <div>
                                    <div>
                                        <label for="productTitle">Product Title <span>*</span></label>
                                        <input type="text" id="productTitle" name="productTitle" required maxlength="200">
                                        <div></div>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label for="productPrice">Product Price <span>*</span></label>
                                        <input type="number" id="productPrice" name="productPrice" step="0.01" min="0" required>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div>
                                    <div>
                                        <label for="productCategory">Category <span>*</span></label>
                                        <select id="productCategory" name="productCategory" required>
                                            <option value="">Select a category...</option>
                                        </select>
                                        <div></div>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label for="productBrand">Brand <span>*</span></label>
                                        <select id="productBrand" name="productBrand" required>
                                            <option value="">Select a brand...</option>
                                        </select>
                                        <div></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="productDescription">Product Description</label>
                                <textarea id="productDescription" name="productDescription" rows="3" maxlength="500"></textarea>
                                <div></div>
                            </div>

                            <div>
                                <label for="productKeywords">Product Keywords</label>
                                <input type="text" id="productKeywords" name="productKeywords" maxlength="100" placeholder="Separate keywords with commas">
                                <div></div>
                            </div>

                            <div>
                                <label for="productImage">Product Image</label>
                                <input type="file" id="productImage" name="productImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="imagePreview">
                                <label>Image Preview</label>
                                <div>
                                    <img id="previewImg" src="" alt="Preview">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editProductModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Edit Product</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editProductForm" enctype="multipart/form-data">
                        <div>
                            <input type="hidden" id="editProductId" name="productId">
                            
                            <div>
                                <div>
                                    <div>
                                        <label for="editProductTitle">Product Title <span>*</span></label>
                                        <input type="text" id="editProductTitle" name="productTitle" required maxlength="200">
                                        <div></div>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label for="editProductPrice">Product Price <span>*</span></label>
                                        <input type="number" id="editProductPrice" name="productPrice" step="0.01" min="0" required>
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div>
                                    <div>
                                        <label for="editProductCategory">Category <span>*</span></label>
                                        <select id="editProductCategory" name="productCategory" required>
                                            <option value="">Select a category...</option>
                                        </select>
                                        <div></div>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <label for="editProductBrand">Brand <span>*</span></label>
                                        <select id="editProductBrand" name="productBrand" required>
                                            <option value="">Select a brand...</option>
                                        </select>
                                        <div></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="editProductDescription">Product Description</label>
                                <textarea id="editProductDescription" name="productDescription" rows="3" maxlength="500"></textarea>
                                <div></div>
                            </div>

                            <div>
                                <label for="editProductKeywords">Product Keywords</label>
                                <input type="text" id="editProductKeywords" name="productKeywords" maxlength="100" placeholder="Separate keywords with commas">
                                <div></div>
                            </div>

                            <div>
                                <label for="editProductImage">Product Image</label>
                                <input type="file" id="editProductImage" name="productImage" accept="image/*">
                                <div>Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                                <div></div>
                            </div>

                            <div id="editImagePreview">
                                <label>Current Image</label>
                                <div>
                                    <img id="editPreviewImg" src="" alt="Current Image">
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">
                                <i></i>Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteProductModal" tabindex="-1">
            <div>
                <div>
                    <div>
                        <h5><i></i>Delete Product</h5>
                        <button type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <div>
                        <p>Are you sure you want to delete this product?</p>
                        <p>This action cannot be undone.</p>
                        <input type="hidden" id="deleteProductId">
                    </div>
                    <div>
                        <button type="button" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirmDelete">
                            <i></i>Delete Product
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
    <script src="../js/product.js"></script>
</body>
</html>
