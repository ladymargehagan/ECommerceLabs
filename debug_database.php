<?php
require_once 'settings/core.php';

echo "<h2>Database Debug Information</h2>";

try {
    // Check categories
    $category_controller = new category_controller();
    $categories = $category_controller->get_categories_ctr(1);
    echo "<h3>Categories:</h3>";
    echo "<pre>" . print_r($categories, true) . "</pre>";
    
    // Check brands
    $brand_controller = new brand_controller();
    $brands = $brand_controller->get_all_brands_ctr();
    echo "<h3>Brands:</h3>";
    echo "<pre>" . print_r($brands, true) . "</pre>";
    
    // Check products
    $product_controller = new product_controller();
    $products = $product_controller->get_all_products_ctr();
    echo "<h3>Products:</h3>";
    echo "<pre>" . print_r($products, true) . "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
