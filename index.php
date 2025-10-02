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

	<!-- Navigation Header -->
	<div class="nav-header">
		<div class="nav-links">
			<span class="brand">flavo</span>
			<a href="#shop">SHOP</a>
			<a href="#story">OUR STORY</a>
			<a href="#contact">CONTACT US</a>
			<div class="menu-tray" style="position: static; background: transparent; border: none; box-shadow: none; padding: 0;">
				<?php if (isset($_SESSION['user_id'])): ?>
					<span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
					<a href="login/logout.php" class="btn btn-sm btn-custom">
						<i class="fa fa-sign-out-alt me-1"></i>Logout
					</a>
				<?php else: ?>
					<a href="login/register.php" class="btn btn-sm btn-custom me-2">
						<i class="fa fa-user-plus me-1"></i>Register
					</a>
					<a href="login/login.php" class="btn btn-sm btn-custom">
						<i class="fa fa-sign-in-alt me-1"></i>Login
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="container" style="padding-top:160px;">
		<?php if (isset($_SESSION['user_id'])): ?>
			<!-- Logged in user content -->
			<div class="main-container">
				<div class="hero-section">
					<h1>DISCOVER NEW TASTES</h1>
					<p class="subtitle">Original spice blends suitable for both home and professional cooking</p>
					<a href="#shop" class="btn btn-custom btn-lg">SHOP ALL PRODUCTS</a>
				</div>
				
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

				<!-- Product Showcase Section -->
				<div class="row mt-5">
					<div class="col-md-4 mb-4">
						<div class="product-card">
							<h3 class="product-title">COSINESS</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #D4A574, #B8956A); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn">-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn">+</button>
							</div>
							<button class="btn-add-cart w-100">ADD TO CART - $18</button>
						</div>
					</div>
					<div class="col-md-4 mb-4">
						<div class="product-card">
							<h3 class="product-title">INTENSITY</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #E85A4F, #C44536); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn">-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn">+</button>
							</div>
							<button class="btn-add-cart w-100">ADD TO CART - $14</button>
						</div>
					</div>
					<div class="col-md-4 mb-4">
						<div class="product-card" style="position: relative;">
							<div style="position: absolute; top: 10px; right: 10px; background: var(--accent-pink); color: var(--text-dark); padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">TOP PICK</div>
							<h3 class="product-title">PASSION</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #F4A261, #E76F51); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn">-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn">+</button>
							</div>
							<button class="btn-add-cart w-100">ADD TO CART - $15</button>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<!-- Guest user content -->
			<div class="main-container">
				<div class="hero-section">
					<h1>DISCOVER NEW TASTES</h1>
					<p class="subtitle">Original spice blends suitable for both home and professional cooking</p>
					<p class="text-muted">Please login or register to access your account and start shopping.</p>
					
					<div class="mt-4">
						<a href="login/login.php" class="btn btn-custom btn-lg me-3">
							<i class="fa fa-sign-in-alt me-2"></i>Login
						</a>
						<a href="login/register.php" class="btn btn-custom btn-lg">
							<i class="fa fa-user-plus me-2"></i>Register
						</a>
					</div>
				</div>

				<!-- Product Preview for Guests -->
				<div class="row mt-5">
					<div class="col-md-4 mb-4">
						<div class="product-card">
							<h3 class="product-title">COSINESS</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #D4A574, #B8956A); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn" disabled>-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn" disabled>+</button>
							</div>
							<button class="btn-add-cart w-100" disabled>LOGIN TO PURCHASE - $18</button>
						</div>
					</div>
					<div class="col-md-4 mb-4">
						<div class="product-card">
							<h3 class="product-title">INTENSITY</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #E85A4F, #C44536); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn" disabled>-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn" disabled>+</button>
							</div>
							<button class="btn-add-cart w-100" disabled>LOGIN TO PURCHASE - $14</button>
						</div>
					</div>
					<div class="col-md-4 mb-4">
						<div class="product-card" style="position: relative;">
							<div style="position: absolute; top: 10px; right: 10px; background: var(--accent-pink); color: var(--text-dark); padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">TOP PICK</div>
							<h3 class="product-title">PASSION</h3>
							<div class="product-image" style="background: linear-gradient(45deg, #F4A261, #E76F51); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
								SPICE BLEND
							</div>
							<div class="quantity-selector">
								<button class="quantity-btn" disabled>-</button>
								<span class="quantity-display">1</span>
								<button class="quantity-btn" disabled>+</button>
							</div>
							<button class="btn-add-cart w-100" disabled>LOGIN TO PURCHASE - $15</button>
						</div>
					</div>
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
	</script>
</body>
</html>
