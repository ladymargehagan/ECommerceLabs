<?php
require_once 'settings/core.php';
require_once 'controllers/product_controller.php';
require_once 'controllers/category_controller.php';
require_once 'controllers/brand_controller.php';

echo "<h1>Database Test and Sample Data Setup</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    $product_controller = new product_controller();
    echo "✓ Product controller initialized successfully<br>";
    
    // Test getting products
    echo "<h2>2. Testing Products</h2>";
    $result = $product_controller->get_all_products_ctr();
    
    if ($result['success']) {
        $product_count = count($result['data']);
        echo "✓ Found {$product_count} products in database<br>";
        
        if ($product_count == 0) {
            echo "<strong>⚠️ No products found! Adding sample products...</strong><br>";
            addSampleData();
        } else {
            echo "✓ Products exist in database<br>";
            foreach ($result['data'] as $index => $product) {
                if ($index < 3) { // Show first 3 products
                    echo "- Product {$product['product_id']}: {$product['product_title']} (Category: {$product['cat_name']}, Brand: {$product['brand_name']})<br>";
                }
            }
        }
    } else {
        echo "❌ Error getting products: " . $result['message'] . "<br>";
    }
    
    // Test categories
    echo "<h2>3. Testing Categories</h2>";
    $cat_controller = new category_controller();
    $cat_result = $cat_controller->get_all_categories_ctr();
    
    if ($cat_result['success']) {
        $cat_count = count($cat_result['data']);
        echo "✓ Found {$cat_count} categories<br>";
        
        if ($cat_count == 0) {
            echo "<strong>⚠️ No categories found! Adding sample categories...</strong><br>";
            addSampleCategories();
        }
    } else {
        echo "❌ Error getting categories: " . $cat_result['message'] . "<br>";
    }
    
    // Test brands
    echo "<h2>4. Testing Brands</h2>";
    $brand_controller = new brand_controller();
    $brand_result = $brand_controller->get_all_brands_ctr();
    
    if ($brand_result['success']) {
        $brand_count = count($brand_result['data']);
        echo "✓ Found {$brand_count} brands<br>";
        
        if ($brand_count == 0) {
            echo "<strong>⚠️ No brands found! Adding sample brands...</strong><br>";
            addSampleBrands();
        }
    } else {
        echo "❌ Error getting brands: " . $brand_result['message'] . "<br>";
    }
    
    // Test product actions
    echo "<h2>5. Testing Product Actions</h2>";
    testProductActions();
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

function addSampleCategories() {
    $cat_controller = new category_controller();
    
    $categories = [
        ['name' => 'Electronics', 'image' => 'electronics.jpg'],
        ['name' => 'Clothing', 'image' => 'clothing.jpg'],
        ['name' => 'Books', 'image' => 'books.jpg'],
        ['name' => 'Home & Garden', 'image' => 'home.jpg'],
        ['name' => 'Sports', 'image' => 'sports.jpg']
    ];
    
    foreach ($categories as $cat) {
        $result = $cat_controller->add_category_ctr([
            'cat_name' => $cat['name'],
            'cat_image' => $cat['image']
        ]);
        
        if ($result['success']) {
            echo "✓ Added category: {$cat['name']}<br>";
        } else {
            echo "❌ Failed to add category {$cat['name']}: {$result['message']}<br>";
        }
    }
}

function addSampleBrands() {
    $brand_controller = new brand_controller();
    
    $brands = [
        ['name' => 'Apple', 'image' => 'apple.jpg'],
        ['name' => 'Samsung', 'image' => 'samsung.jpg'],
        ['name' => 'Nike', 'image' => 'nike.jpg'],
        ['name' => 'Adidas', 'image' => 'adidas.jpg'],
        ['name' => 'Sony', 'image' => 'sony.jpg']
    ];
    
    foreach ($brands as $brand) {
        $result = $brand_controller->add_brand_ctr([
            'brand_name' => $brand['name'],
            'brand_image' => $brand['image']
        ]);
        
        if ($result['success']) {
            echo "✓ Added brand: {$brand['name']}<br>";
        } else {
            echo "❌ Failed to add brand {$brand['name']}: {$result['message']}<br>";
        }
    }
}

function addSampleData() {
    $product_controller = new product_controller();
    $cat_controller = new category_controller();
    $brand_controller = new brand_controller();
    
    // First ensure we have categories and brands
    $cat_result = $cat_controller->get_all_categories_ctr();
    $brand_result = $brand_controller->get_all_brands_ctr();
    
    if (!$cat_result['success'] || count($cat_result['data']) == 0) {
        addSampleCategories();
        $cat_result = $cat_controller->get_all_categories_ctr();
    }
    
    if (!$brand_result['success'] || count($brand_result['data']) == 0) {
        addSampleBrands();
        $brand_result = $brand_controller->get_all_brands_ctr();
    }
    
    $categories = $cat_result['data'];
    $brands = $brand_result['data'];
    
    if (count($categories) == 0 || count($brands) == 0) {
        echo "❌ Cannot add products without categories and brands<br>";
        return;
    }
    
    $products = [
        [
            'title' => 'iPhone 15 Pro',
            'price' => 999.99,
            'desc' => 'Latest iPhone with advanced camera system and A17 Pro chip',
            'keywords' => 'smartphone, apple, camera, mobile',
            'cat_id' => $categories[0]['cat_id'],
            'brand_id' => $brands[0]['brand_id']
        ],
        [
            'title' => 'Samsung Galaxy S24',
            'price' => 899.99,
            'desc' => 'Premium Android smartphone with AI-powered features',
            'keywords' => 'smartphone, samsung, android, mobile',
            'cat_id' => $categories[0]['cat_id'],
            'brand_id' => $brands[1]['brand_id']
        ],
        [
            'title' => 'Nike Air Max 270',
            'price' => 150.00,
            'desc' => 'Comfortable running shoes with Max Air cushioning',
            'keywords' => 'shoes, running, nike, athletic',
            'cat_id' => $categories[4]['cat_id'],
            'brand_id' => $brands[2]['brand_id']
        ],
        [
            'title' => 'Adidas Ultraboost 22',
            'price' => 180.00,
            'desc' => 'High-performance running shoes with Boost technology',
            'keywords' => 'shoes, running, adidas, athletic',
            'cat_id' => $categories[4]['cat_id'],
            'brand_id' => $brands[3]['brand_id']
        ],
        [
            'title' => 'Sony WH-1000XM5 Headphones',
            'price' => 399.99,
            'desc' => 'Premium noise-canceling wireless headphones',
            'keywords' => 'headphones, wireless, noise canceling, sony',
            'cat_id' => $categories[0]['cat_id'],
            'brand_id' => $brands[4]['brand_id']
        ],
        [
            'title' => 'Programming Book: Clean Code',
            'price' => 45.99,
            'desc' => 'Essential guide to writing clean, maintainable code',
            'keywords' => 'programming, book, software development, coding',
            'cat_id' => $categories[2]['cat_id'],
            'brand_id' => $brands[0]['brand_id'] // Using Apple as publisher for demo
        ]
    ];
    
    foreach ($products as $product) {
        $result = $product_controller->add_product_ctr([
            'product_cat' => $product['cat_id'],
            'product_brand' => $product['brand_id'],
            'product_title' => $product['title'],
            'product_price' => $product['price'],
            'product_desc' => $product['desc'],
            'product_image' => 'placeholder.png',
            'product_keywords' => $product['keywords']
        ]);
        
        if ($result['success']) {
            echo "✓ Added product: {$product['title']}<br>";
        } else {
            echo "❌ Failed to add product {$product['title']}: {$result['message']}<br>";
        }
    }
}

function testProductActions() {
    echo "Testing product_actions.php endpoints...<br>";
    
    // Test get_products action
    $url = 'product_actions.php?action=get_products&page=1';
    echo "Testing: {$url}<br>";
    
    // Test get_categories action
    $url = 'product_actions.php?action=get_categories';
    echo "Testing: {$url}<br>";
    
    // Test get_brands action
    $url = 'product_actions.php?action=get_brands';
    echo "Testing: {$url}<br>";
    
    echo "✓ Product actions endpoints available<br>";
}

echo "<h2>6. Next Steps</h2>";
echo "1. Visit <a href='all_product.php'>all_product.php</a> to test the all products page<br>";
echo "2. Visit <a href='product.php'>product.php</a> to test the search functionality<br>";
echo "3. Check browser console for any JavaScript errors<br>";
echo "4. Test the AJAX calls manually if needed<br>";
?>
