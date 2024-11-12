<?php
$servername = "localhost";
$username = "root";  // default user in XAMPP
$password = "";      // default password in XAMPP is empty
$dbname = "inventory";     // Name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get all items from inventory
function getInventory() {
    global $conn;
    $sql = "SELECT * FROM inventory";
    $result = $conn->query($sql);
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    return $items;
}

// Function to get dashboard data (Total Items, Low Stock, Pending Orders)
function getDashboardData() {
    global $conn;
    $totalItemsQuery = "SELECT COUNT(*) AS total_items FROM inventory";
    $lowStockQuery = "SELECT COUNT(*) AS low_stock FROM inventory WHERE quantity < 10";
    $pendingOrdersQuery = "SELECT COUNT(*) AS pending_orders FROM orders WHERE status = 'pending'";

    $totalItemsResult = $conn->query($totalItemsQuery);
    $lowStockResult = $conn->query($lowStockQuery);
    $pendingOrdersResult = $conn->query($pendingOrdersQuery);

    $totalItems = $totalItemsResult->fetch_assoc()['total_items'];
    $lowStock = $lowStockResult->fetch_assoc()['low_stock'];
    $pendingOrders = $pendingOrdersResult->fetch_assoc()['pending_orders'];

    return [
        'totalItems' => $totalItems,
        'lowStock' => $lowStock,
        'pendingOrders' => $pendingOrders
    ];
}

// Function to add a new item to inventory
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_item') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    $sql = "INSERT INTO inventory (name, quantity, price) VALUES ('$name', '$quantity', '$price')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Item added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding item']);
    }
    exit();
}

// Handle GET requests to get inventory and dashboard data
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['action']) && $_GET['action'] == 'get_inventory') {
        echo json_encode(getInventory());
        exit();
    }

    if (isset($_GET['action']) && $_GET['action'] == 'get_dashboard') {
        echo json_encode(getDashboardData());
        exit();
    }
}

$conn->close();
?>
