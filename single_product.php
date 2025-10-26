<?php
// Direct access to single product view
if (!isset($_GET['action'])) {
    $_GET['action'] = 'view_single_product';
}
require_once 'actions/product_actions.php';
?>
