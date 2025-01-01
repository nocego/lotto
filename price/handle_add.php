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
    $name = $_POST['name'];
    $sponsor = $_POST['sponsor'];
    $winner = $_POST['winner'];
    $sequence = $_POST['sequence'];
    $winnerNumber = $_POST['winner_number'];
    if (empty($winner)) {
        $winner = null;
    }
    if (empty($sequence)) {
        $sequence = 0;
    }
    if (empty($winnerNumber)) {
        $winnerNumber = null;
    }

    // Validate input
    if (empty($name) || empty($sponsor)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('INSERT INTO Price (series_id, name, sponsor, winner, sequence, winner_number) VALUES (?, ?, ?, ?, ?, ?)');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('isssii', $_POST['series_id'], $name, $sponsor, $winner, $sequence, $winnerNumber);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    } else {
        // Redirect to a protected page
        header('Location: /price/view.php?series_id='.$_POST['series_id']);
    }

    $stmt->close();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
