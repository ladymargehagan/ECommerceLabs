<?php
// Direct access to product search results view
if (!isset($_GET['action'])) {
    $_GET['action'] = 'search_products';
}
require_once 'actions/product_actions.php';
?>
