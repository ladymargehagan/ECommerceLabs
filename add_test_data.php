<?php
require_once 'settings/core.php';

// Simple script to add test data
echo "<h2>Adding Test Data to Database</h2>";

try {
    // Add test categories
    $category_controller = new category_controller();
    $cat1 = $category_controller->add_category_ctr([
        'cat_name' => 'Food & Beverages',
        'created_by' => 1,
        'cat_image' => ''
    ]);
    echo "Category 1: " . ($cat1['success'] ? 'Success' : $cat1['message']) . "<br>";
    
    $cat2 = $category_controller->add_category_ctr([
        'cat_name' => 'Spices & Seasonings',
        'created_by' => 1,
        'cat_image' => ''
    ]);
    echo "Category 2: " . ($cat2['success'] ? 'Success' : $cat2['message']) . "<br>";
    
    // Add test brands
    $brand_controller = new brand_controller();
    $brand1 = $brand_controller->add_brand_ctr([
        'brand_name' => 'African Delights',
        'brand_image' => ''
    ]);
    echo "Brand 1: " . ($brand1['success'] ? 'Success' : $brand1['message']) . "<br>";
    
    $brand2 = $brand_controller->add_brand_ctr([
        'brand_name' => 'Taste of Home',
        'brand_image' => ''
    ]);
    echo "Brand 2: " . ($brand2['success'] ? 'Success' : $brand2['message']) . "<br>";
    
    // Add test products
    $product_controller = new product_controller();
    $product1 = $product_controller->add_product_ctr([
        'product_cat' => 1,
        'product_brand' => 1,
        'product_title' => 'Premium Coffee Beans',
        'product_price' => 25.99,
        'product_desc' => 'Rich, aromatic coffee beans from the highlands of Ethiopia',
        'product_image' => 'uploads/product/coffee.jpg',
        'product_keywords' => 'coffee, beans, ethiopia, premium'
    ]);
    echo "Product 1: " . ($product1['success'] ? 'Success' : $product1['message']) . "<br>";
    
    $product2 = $product_controller->add_product_ctr([
        'product_cat' => 2,
        'product_brand' => 2,
        'product_title' => 'African Spice Blend',
        'product_price' => 15.50,
        'product_desc' => 'Authentic blend of traditional African spices',
        'product_image' => 'uploads/product/spices.jpg',
        'product_keywords' => 'spices, blend, traditional, african'
    ]);
    echo "Product 2: " . ($product2['success'] ? 'Success' : $product2['message']) . "<br>";
    
    $product3 = $product_controller->add_product_ctr([
        'product_cat' => 1,
        'product_brand' => 1,
        'product_title' => 'Hibiscus Tea',
        'product_price' => 12.99,
        'product_desc' => 'Refreshing hibiscus tea with natural flavors',
        'product_image' => 'uploads/product/tea.jpg',
        'product_keywords' => 'tea, hibiscus, natural, refreshing'
    ]);
    echo "Product 3: " . ($product3['success'] ? 'Success' : $product3['message']) . "<br>";
    
    echo "<h3>Test data added successfully!</h3>";
    echo "<a href='all_product.php'>View All Products</a><br>";
    echo "<a href='index.php'>Go to Home</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
