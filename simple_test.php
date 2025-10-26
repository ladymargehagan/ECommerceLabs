<?php
require_once 'settings/core.php';

// SUPER SIMPLE TEST - Just echo what's in the database
echo "<h1>SUPER SIMPLE DATABASE TEST</h1>";

try {
    $product_controller = new product_controller();
    $result = $product_controller->get_all_products_ctr();
    
    echo "<h2>Raw Result:</h2>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    if ($result['success'] && is_array($result['data'])) {
        echo "<h2>Product Count: " . count($result['data']) . "</h2>";
        
        if (count($result['data']) > 0) {
            echo "<h2>First Product:</h2>";
            echo "<pre>" . print_r($result['data'][0], true) . "</pre>";
        } else {
            echo "<h2 style='color: red;'>NO PRODUCTS IN DATABASE!</h2>";
            echo "<p>You need to add products first. Go to admin/product.php and add some products.</p>";
        }
    } else {
        echo "<h2 style='color: red;'>ERROR:</h2>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>EXCEPTION:</h2>";
    echo $e->getMessage();
}
?>
