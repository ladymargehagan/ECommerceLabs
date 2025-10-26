<?php
require_once 'settings/core.php';
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
	<link href="css/product-customer.css" rel="stylesheet">
	<link href="css/common.css" rel="stylesheet">
</head>
<body>

	<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 1): // Admin users ?>
		<?php 
		// Redirect admin users directly to dashboard
		header("Location: admin/dashboard.php");
		exit;
		?>
	<?php endif; ?>

	<!-- Enhanced Navigation -->
	<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
		<div class="container">
			<a class="navbar-brand fw-bold" href="index.php">
				<i class="fa fa-star text-warning me-2"></i>Taste of Africa
			</a>
			
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item">
						<a class="nav-link active" href="index.php">
							<i class="fa fa-home me-1"></i>Home
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="all_product.php">
							<i class="fa fa-box me-1"></i>All Products
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="product.php">
							<i class="fa fa-search me-1"></i>Search Products
						</a>
					</li>
				</ul>
				
				<!-- Search Box -->
				<div class="navbar-nav me-3">
					<form class="d-flex" id="headerSearchForm">
						<div class="input-group">
							<input class="form-control" type="search" id="headerSearchInput" 
								   placeholder="Search products..." aria-label="Search">
							<button class="btn btn-outline-primary" type="submit">
								<i class="fa fa-search"></i>
							</button>
						</div>
					</form>
				</div>
				
				<!-- User Menu -->
				<ul class="navbar-nav">
					<?php if (isset($_SESSION['user_id'])): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
								<i class="fa fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['name']); ?>
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="customer/dashboard.php">
									<i class="fa fa-tachometer-alt me-2"></i>Dashboard
								</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="login/logout.php">
									<i class="fa fa-sign-out-alt me-2"></i>Logout
								</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="login/register.php">
								<i class="fa fa-user-plus me-1"></i>Register
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="login/login.php">
								<i class="fa fa-sign-in-alt me-1"></i>Login
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Quick Filters Section -->
	<div class="quick-filters-section bg-light py-3">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-3">
					<h6 class="mb-0 fw-bold">
						<i class="fa fa-filter text-primary me-2"></i>Quick Filters
					</h6>
				</div>
				<div class="col-md-3">
					<select class="form-select form-select-sm" id="quickCategoryFilter">
						<option value="">All Categories</option>
						<!-- Categories will be loaded here -->
					</select>
				</div>
				<div class="col-md-3">
					<select class="form-select form-select-sm" id="quickBrandFilter">
						<option value="">All Brands</option>
						<!-- Brands will be loaded here -->
					</select>
				</div>
				<div class="col-md-3">
					<button class="btn btn-primary btn-sm w-100" id="applyQuickFilters">
						<i class="fa fa-search me-2"></i>Filter Products
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Hero Section -->
	<div class="hero-section">
		<div class="container text-center">
			<h1 class="display-4 fw-bold mb-3">
				<i class="fa fa-star me-3"></i>Welcome to Taste of Africa
			</h1>
			<p class="lead mb-4">Discover the authentic flavors of Africa</p>
			
			<?php if (isset($_SESSION['user_id'])): ?>
				<div class="welcome-message">
					<h3>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h3>
					<p class="text-muted">Ready to explore our amazing products?</p>
				</div>
			<?php else: ?>
				<div class="guest-message">
					<p class="text-muted">Join thousands of satisfied customers who love our products</p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="container">
		<!-- Featured Products Section -->
		<div class="featured-products-section mb-5">
			<h2 class="text-center mb-4">
				<i class="fa fa-heart text-danger me-2"></i>Featured Products
			</h2>
			<div class="row" id="featuredProductsContainer">
				<!-- Featured products will be loaded here -->
				<div class="col-12 text-center py-4">
					<div class="loading-spinner">
						<div class="spinner-border text-primary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
						<p class="mt-3 text-muted">Loading featured products...</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions Section -->
		<div class="quick-actions-section mb-5">
			<div class="row">
				<div class="col-md-4 mb-3">
					<div class="card action-card h-100 text-center">
						<div class="card-body">
							<i class="fa fa-box fa-3x text-primary mb-3"></i>
							<h5 class="card-title">Browse All Products</h5>
							<p class="card-text">Explore our complete collection of premium products</p>
							<a href="all_product.php" class="btn btn-primary">
								<i class="fa fa-box me-2"></i>View All Products
							</a>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<div class="card action-card h-100 text-center">
						<div class="card-body">
							<i class="fa fa-search fa-3x text-success mb-3"></i>
							<h5 class="card-title">Search Products</h5>
							<p class="card-text">Find exactly what you're looking for with our advanced search</p>
							<a href="product.php" class="btn btn-success">
								<i class="fa fa-search me-2"></i>Search Now
							</a>
						</div>
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<div class="card action-card h-100 text-center">
						<div class="card-body">
							<?php if (isset($_SESSION['user_id'])): ?>
								<i class="fa fa-user fa-3x text-info mb-3"></i>
								<h5 class="card-title">Your Dashboard</h5>
								<p class="card-text">Manage your account and view your orders</p>
								<a href="customer/dashboard.php" class="btn btn-info">
									<i class="fa fa-tachometer-alt me-2"></i>Go to Dashboard
								</a>
							<?php else: ?>
								<i class="fa fa-user-plus fa-3x text-warning mb-3"></i>
								<h5 class="card-title">Join Us Today</h5>
								<p class="card-text">Create an account to start shopping and save your preferences</p>
								<a href="login/register.php" class="btn btn-warning">
									<i class="fa fa-user-plus me-2"></i>Register Now
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- User Information Section (for logged in users) -->
		<?php if (isset($_SESSION['user_id'])): ?>
			<div class="user-info-section mb-5">
				<div class="card">
					<div class="card-header">
						<h4 class="mb-0">
							<i class="fa fa-user me-2"></i>Your Account Information
						</h4>
					</div>
					<div class="card-body">
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
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="js/index.js"></script>
</body>
</html>
