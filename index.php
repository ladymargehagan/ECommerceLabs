<?php
// Start session to check if user is logged in
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Welcome - Taste of Africa</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
	<link href="css/index.css" rel="stylesheet">
</head>
<body>

	<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 1): // Admin users ?>
		<?php 
		// Redirect admin users directly to dashboard
		header("Location: admin/dashboard.php");
		exit;
		?>
	<?php endif; ?>

	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			<span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
			<?php if ($_SESSION['role'] == 1): // Admin users ?>
				<a href="admin/category.php" class="btn btn-sm btn-outline-primary me-2">
					<i class="fa fa-tags me-1"></i>Category
				</a>
				<a href="admin/brand.php" class="btn btn-sm btn-outline-warning me-2">
					<i class="fa fa-star me-1"></i>Brand
				</a>
				<a href="admin/product.php" class="btn btn-sm btn-outline-success me-2">
					<i class="fa fa-plus me-1"></i>Add Product
				</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">
				<i class="fa fa-sign-out-alt me-1"></i>Logout
			</a>
		<?php else: ?>
			<span class="me-2">Menu:</span>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary me-2">
				<i class="fa fa-user-plus me-1"></i>Register
			</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary me-2">
				<i class="fa fa-sign-in-alt me-1"></i>Login
			</a>
		<?php endif; ?>
		
		<!-- Lab Required Menu Items -->
		<div class="mt-2">
			<a href="all_product.php" class="btn btn-sm btn-outline-info me-2">
				<i class="fa fa-box me-1"></i>All Products
			</a>
		</div>
		
		<!-- Search Box -->
		<div class="mt-2">
			<form class="d-flex" method="GET" action="actions/product_actions.php">
				<input type="hidden" name="action" value="search_products">
				<input class="form-control form-control-sm me-2" type="search" name="query" 
					   placeholder="Search products..." aria-label="Search" style="width: 200px;">
				<button class="btn btn-outline-success btn-sm" type="submit">
					<i class="fa fa-search"></i>
				</button>
			</form>
		</div>
		
		<!-- Category and Brand Filters -->
		<div class="mt-2">
			<div class="d-flex gap-2">
				<select class="form-select form-select-sm" style="width: 150px;" onchange="filterByCategory(this.value)">
					<option value="">All Categories</option>
				</select>
				
				<select class="form-select form-select-sm" style="width: 150px;" onchange="filterByBrand(this.value)">
					<option value="">All Brands</option>
				</select>
			</div>
		</div>
	</div>

	<div class="container" style="padding-top:120px;">
		<?php if (isset($_SESSION['user_id'])): ?>
			<!-- Logged in user content -->
			<div class="welcome-card text-center">
				<h1><i class="fa fa-home me-2"></i>Welcome to Taste of Africa!</h1>
				<p class="lead">You have successfully logged in to your account.</p>
				
				<div class="user-info">
					<h4><i class="fa fa-user me-2"></i>Your Account Information</h4>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
							<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
						</div>
						<div class="col-md-6">
							<p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?></p>
							<p><strong>Role:</strong> 
								<?php 
								if ($_SESSION['role'] == 1) {
									echo '<span class="badge bg-warning">Administrator</span>';
								} else {
									echo '<span class="badge bg-info">Customer</span>';
								}
								?>
							</p>
						</div>
					</div>
				</div>
				
				<div class="mt-4">
					<a href="all_product.php" class="btn btn-custom btn-lg me-3">
						<i class="fa fa-shopping-cart me-2"></i>Start Shopping
					</a>
					<a href="#" class="btn btn-outline-light btn-lg">
						<i class="fa fa-user-edit me-2"></i>Edit Profile
					</a>
				</div>
			</div>
		<?php else: ?>
			<!-- Guest user content -->
			<div class="text-center">
				<h1><i class="fa fa-home me-2"></i>Welcome to Taste of Africa</h1>
				<p class="lead text-muted">Discover the authentic flavors of Africa</p>
				<p class="text-muted">Please login or register to access your account and start shopping.</p>
				
				<div class="mt-4">
					<a href="login/login.php" class="btn btn-custom btn-lg me-3">
						<i class="fa fa-sign-in-alt me-2"></i>Login
					</a>
					<a href="login/register.php" class="btn btn-outline-primary btn-lg">
						<i class="fa fa-user-plus me-2"></i>Register
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<script>
		$(document).ready(function() {
			// Show welcome message if just logged in
			const urlParams = new URLSearchParams(window.location.search);
			if (urlParams.get('login') === 'success') {
				// You can add a success notification here if needed
				console.log('Login successful!');
			}
		});

		// Filter functions
		function filterByCategory(catId) {
			if (catId) {
				window.location.href = 'actions/product_actions.php?action=filter_by_category&cat_id=' + catId;
			} else {
				window.location.href = 'actions/product_actions.php?action=view_all_products';
			}
		}

		function filterByBrand(brandId) {
			if (brandId) {
				window.location.href = 'actions/product_actions.php?action=filter_by_brand&brand_id=' + brandId;
			} else {
				window.location.href = 'actions/product_actions.php?action=view_all_products';
			}
		}
	</script>
</body>
</html>
