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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h2><i class="fa fa-tags me-2"></i>Brand Management</h2>
                    <div>
                        <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i class="fa fa-plus me-1"></i>Add Brand
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Display -->
        <div class="row" id="brandsContainer">
            <!-- Brands will be loaded here -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading brands...</p>
            </div>
        </div>

        <!-- Add Brand Modal -->
        <div class="modal fade" id="addBrandModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Add New Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addBrandForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="brandName" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brandName" name="brand_name" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Add Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Brand Modal -->
        <div class="modal fade" id="editBrandModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editBrandForm">
                        <div class="modal-body">
                            <input type="hidden" id="editBrandId" name="brand_id">
                            <div class="mb-3">
                                <label for="editBrandName" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editBrandName" name="brand_name" required maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-save me-1"></i>Update Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteBrandModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-trash me-2"></i>Delete Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this brand?</p>
                        <p class="text-muted">This action cannot be undone. If the brand is being used by products, it cannot be deleted.</p>
                        <input type="hidden" id="deleteBrandId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">
                            <i class="fa fa-trash me-1"></i>Delete Brand
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            loadBrands();

            $('#addBrandForm').on('submit', function(e) {
                e.preventDefault();
                addBrand();
            });

            $('#editBrandForm').on('submit', function(e) {
                e.preventDefault();
                updateBrand();
            });

            $('#confirmDelete').on('click', function() {
                deleteBrand();
            });

            $('#addBrandModal').on('hidden.bs.modal', function() {
                $('#addBrandForm')[0].reset();
                clearValidationErrors('#addBrandForm');
            });

            $('#editBrandModal').on('hidden.bs.modal', function() {
                $('#editBrandForm')[0].reset();
                clearValidationErrors('#editBrandForm');
            });
        });

        function loadBrands() {
            // Simple brands display for now
            $('#brandsContainer').html(`
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-tags fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Brand Management</h4>
                        <p class="text-muted">Brand management functionality will be implemented here.</p>
                        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i class="fa fa-plus me-1"></i>Add Brand
                        </button>
                    </div>
                </div>
            `);
        }

        function addBrand() {
            const brandName = $('#brandName').val().trim();
            
            if (!brandName) {
                showAlert('error', 'Error', 'Brand name is required.');
                return;
            }

            // Placeholder for brand addition
            showAlert('success', 'Success', 'Brand "' + brandName + '" will be added.');
            $('#addBrandModal').modal('hide');
        }

        function updateBrand() {
            const brandId = $('#editBrandId').val();
            const brandName = $('#editBrandName').val().trim();
            
            if (!brandName) {
                showAlert('error', 'Error', 'Brand name is required.');
                return;
            }

            // Placeholder for brand update
            showAlert('success', 'Success', 'Brand will be updated to "' + brandName + '".');
            $('#editBrandModal').modal('hide');
        }

        function deleteBrand() {
            const brandId = $('#deleteBrandId').val();
            
            if (!brandId) {
                showAlert('error', 'Error', 'Brand ID not found.');
                return;
            }

            // Placeholder for brand deletion
            showAlert('success', 'Success', 'Brand will be deleted.');
            $('#deleteBrandModal').modal('hide');
        }

        function editBrand(brandId, brandName) {
            $('#editBrandId').val(brandId);
            $('#editBrandName').val(brandName);
            $('#editBrandModal').modal('show');
        }

        function confirmDelete(brandId, brandName) {
            $('#deleteBrandId').val(brandId);
            $('#deleteBrandModal .modal-body p').html('Are you sure you want to delete the brand "<strong>' + brandName + '</strong>"?');
            $('#deleteBrandModal').modal('show');
        }

        function clearValidationErrors(form) {
            const $form = typeof form === 'string' ? $(form) : form;
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
        }

        function showAlert(type, title, message) {
            const icon = type === 'success' ? 'success' : 'error';
            const color = type === 'success' ? '#28a745' : '#dc3545';
            
            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonColor: color,
                timer: type === 'success' ? 3000 : null,
                timerProgressBar: type === 'success'
            });
        }
    </script>
</body>
</html>
