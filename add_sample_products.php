<?php
// Add sample products to test the all_product.php page
session_start();

try {
    require_once 'settings/db_class.php';
    require_once 'classes/product_class.php';
    
    $product_class = new product_class();
    
    if ($product_class->db_connect()) {
        echo "Adding sample products...\n";
        
        // Sample products
        $sample_products = [
            [
                'product_cat' => 1,
                'product_brand' => 1,
                'product_title' => 'African Spice Mix',
                'product_price' => 15.99,
                'product_desc' => 'Authentic blend of traditional African spices perfect for cooking.',
                'product_image' => 'product/spice_mix.jpg',
                'product_keywords' => 'spices, cooking, traditional, african'
            ],
            [
                'product_cat' => 1,
                'product_brand' => 1,
                'product_title' => 'Handwoven Basket',
                'product_price' => 45.00,
                'product_desc' => 'Beautiful handwoven basket made by local artisans.',
                'product_image' => 'product/basket.jpg',
                'product_keywords' => 'basket, handwoven, artisan, traditional'
            ],
            [
                'product_cat' => 2,
                'product_brand' => 2,
                'product_title' => 'African Tea Blend',
                'product_price' => 12.50,
                'product_desc' => 'Premium tea blend with authentic African herbs.',
                'product_image' => 'product/tea_blend.jpg',
                'product_keywords' => 'tea, herbs, premium, african'
            ],
            [
                'product_cat' => 2,
                'product_brand' => 2,
                'product_title' => 'Traditional Pottery',
                'product_price' => 35.00,
                'product_desc' => 'Handcrafted pottery with traditional African designs.',
                'product_image' => 'product/pottery.jpg',
                'product_keywords' => 'pottery, handcrafted, traditional, african'
            ],
            [
                'product_cat' => 3,
                'product_brand' => 3,
                'product_title' => 'African Textile',
                'product_price' => 28.99,
                'product_desc' => 'Beautiful African textile with vibrant patterns.',
                'product_image' => 'product/textile.jpg',
                'product_keywords' => 'textile, patterns, vibrant, african'
            ]
        ];
        
        foreach ($sample_products as $product) {
            $result = $product_class->add_product(
                $product['product_cat'],
                $product['product_brand'],
                $product['product_title'],
                $product['product_price'],
                $product['product_desc'],
                $product['product_image'],
                $product['product_keywords']
            );
            
            if ($result) {
                echo "Added: " . $product['product_title'] . " (ID: $result)\n";
            } else {
                echo "Failed to add: " . $product['product_title'] . "\n";
            }
        }
        
        echo "\nSample products added successfully!\n";
        
    } else {
        echo "Database connection failed\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
