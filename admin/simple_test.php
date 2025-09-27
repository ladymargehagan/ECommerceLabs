<?php
session_start();

echo "<h1>Simple Admin Test</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session Check:</h2>";
echo "user_id set: " . (isset($_SESSION['user_id']) ? 'YES' : 'NO') . "<br>";
echo "role set: " . (isset($_SESSION['role']) ? 'YES' : 'NO') . "<br>";

if (isset($_SESSION['role'])) {
    echo "role value: " . $_SESSION['role'] . "<br>";
    echo "role type: " . gettype($_SESSION['role']) . "<br>";
    echo "role == 1: " . ($_SESSION['role'] == 1 ? 'TRUE' : 'FALSE') . "<br>";
    echo "role === 1: " . ($_SESSION['role'] === 1 ? 'TRUE' : 'FALSE') . "<br>";
}

echo "<h2>Redirect Logic Test:</h2>";
if (!isset($_SESSION['user_id'])) {
    echo "Would redirect to login<br>";
} elseif (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo "Would redirect to index<br>";
} else {
    echo "Would show dashboard<br>";
}

echo "<h2>Manual Session Test:</h2>";
echo "<a href='?set_session=1'>Set Test Session</a><br>";
echo "<a href='?clear_session=1'>Clear Session</a><br>";

if (isset($_GET['set_session'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 1;
    $_SESSION['name'] = 'Test Admin';
    echo "<p>Session set! <a href='dashboard.php'>Try Dashboard</a></p>";
}

if (isset($_GET['clear_session'])) {
    session_destroy();
    echo "<p>Session cleared!</p>";
}
?>
