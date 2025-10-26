<?php
// Add test data to database
require_once 'settings/db_cred.php';

$db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_error($db));
}

echo "<h2>Adding Test Data to Database</h2>";

// Clear existing data first
mysqli_query($db, "DELETE FROM products");
mysqli_query($db, "DELETE FROM categories");
mysqli_query($db, "DELETE FROM brands");

// Reset auto increment
mysqli_query($db, "ALTER TABLE products AUTO_INCREMENT = 1");
mysqli_query($db, "ALTER TABLE categories AUTO_INCREMENT = 1");
mysqli_query($db, "ALTER TABLE brands AUTO_INCREMENT = 1");

// Add categories
$categories = [
    ['Food & Beverages', 1],
    ['Spices & Seasonings', 1], 
    ['Traditional Crafts', 1],
    ['Health & Wellness', 1]
];

foreach ($categories as $cat) {
    $sql = "INSERT INTO categories (cat_name, created_by) VALUES ('{$cat[0]}', {$cat[1]})";
    if (mysqli_query($db, $sql)) {
        echo "✓ Added category: {$cat[0]}<br>";
    } else {
        echo "✗ Error adding category {$cat[0]}: " . mysqli_error($db) . "<br>";
    }
}

// Add brands
$brands = ['African Delights', 'Taste of Home', 'Heritage Foods', 'Premium African'];
foreach ($brands as $brand) {
    $sql = "INSERT INTO brands (brand_name) VALUES ('$brand')";
    if (mysqli_query($db, $sql)) {
        echo "✓ Added brand: $brand<br>";
    } else {
        echo "✗ Error adding brand $brand: " . mysqli_error($db) . "<br>";
    }
}

// Add products
$products = [
    [1, 1, 'Premium Coffee Beans', 25.99, 'Rich, aromatic coffee beans from the highlands of Ethiopia. Perfect for morning brew.', 'uploads/placeholder.png', 'coffee, beans, ethiopia, premium, morning'],
    [2, 2, 'African Spice Blend', 15.50, 'Authentic blend of traditional African spices. Adds amazing flavor to any dish.', 'uploads/placeholder.png', 'spices, blend, traditional, african, flavor'],
    [1, 1, 'Hibiscus Tea', 12.99, 'Refreshing hibiscus tea with natural flavors. Great for hot or cold brewing.', 'uploads/placeholder.png', 'tea, hibiscus, natural, refreshing, cold'],
    [1, 3, 'Coconut Oil', 18.75, 'Pure virgin coconut oil from West Africa. Perfect for cooking and skincare.', 'uploads/placeholder.png', 'coconut, oil, virgin, west africa, cooking'],
    [2, 2, 'Berbere Spice', 22.00, 'Traditional Ethiopian spice blend. Essential for authentic Ethiopian cuisine.', 'uploads/placeholder.png', 'berbere, spice, ethiopian, traditional, cuisine'],
    [3, 3, 'Handwoven Basket', 35.00, 'Beautiful handwoven basket from Ghana. Perfect for storage or decoration.', 'uploads/placeholder.png', 'basket, handwoven, ghana, traditional, storage'],
    [1, 4, 'Moringa Powder', 28.50, 'Superfood powder from the moringa tree. High in vitamins and minerals.', 'uploads/placeholder.png', 'moringa, powder, superfood, vitamins, health'],
    [2, 1, 'Cumin Seeds', 14.25, 'Premium cumin seeds from North Africa. Essential spice for many dishes.', 'uploads/placeholder.png', 'cumin, seeds, spice, north africa, cooking'],
    [4, 2, 'Shea Butter', 19.99, 'Pure shea butter from Ghana. Excellent for skin and hair care.', 'uploads/placeholder.png', 'shea butter, ghana, skincare, hair, natural'],
    [1, 3, 'Rooibos Tea', 16.75, 'South African rooibos tea. Naturally caffeine-free and antioxidant-rich.', 'uploads/placeholder.png', 'rooibos, tea, south africa, caffeine-free, antioxidant']
];

foreach ($products as $product) {
    $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES ({$product[0]}, {$product[1]}, '{$product[2]}', {$product[3]}, '{$product[4]}', '{$product[5]}', '{$product[6]}')";
    if (mysqli_query($db, $sql)) {
        echo "✓ Added product: {$product[2]}<br>";
    } else {
        echo "✗ Error adding product {$product[2]}: " . mysqli_error($db) . "<br>";
    }
}

// Check final counts
$result = mysqli_query($db, "SELECT COUNT(*) as count FROM products");
$row = mysqli_fetch_assoc($result);
echo "<h3>✓ Total products: " . $row['count'] . "</h3>";

$result = mysqli_query($db, "SELECT COUNT(*) as count FROM categories");
$row = mysqli_fetch_assoc($result);
echo "<h3>✓ Total categories: " . $row['count'] . "</h3>";

$result = mysqli_query($db, "SELECT COUNT(*) as count FROM brands");
$row = mysqli_fetch_assoc($result);
echo "<h3>✓ Total brands: " . $row['count'] . "</h3>";

mysqli_close($db);
echo "<h3>🎉 Test data added successfully!</h3>";
echo "<a href='index.php' class='btn btn-primary'>Go to Home</a> | <a href='all_product.php' class='btn btn-success'>View All Products</a>";
?>
