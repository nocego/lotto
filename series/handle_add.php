<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}
include_once('../config/db.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lottoId = $_POST['lotto_id'];
    $name = $_POST['name'];
    $mode = $_POST['mode'];

    // Validate input
    if (empty($name) || empty($lottoId) || empty($mode)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('INSERT INTO Series (name, lotto_id, mode) VALUES (?, ?, ?)');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('ssi', $name, $lottoId, $mode);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    } else {
        header('Location: /lotto/view.php?id=' . $lottoId);
    }

    $stmt->close();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
