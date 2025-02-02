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
    $sequence = $_POST['sequence'];
    $winnerNumber = $_POST['winner_number'];
    if (empty($sequence)) {
        $sequence = 0;
    }
    if (empty($winnerNumber)) {
        $winnerNumber = null;
    }
    if (empty($sponsor)) {
        $sponsor = null;
    }
    $winner_name = $_POST['winner_name'];
    if (empty($winner_name)) {
        $winner_name = null;
    }
    $winner_birthyear = $_POST['winner_birthyear'];
    if (empty($winner_birthyear)) {
        $winner_birthyear = null;
    }
    $winner_location = $_POST['winner_location'];
    if (empty($winner_location)) {
        $winner_location = null;
    }
    $winner_company = $_POST['winner_company'];
    if (empty($winner_company)) {
        $winner_company = null;
    }
    $winner_seller = $_POST['winner_seller'];
    if (empty($winner_seller)) {
        $winner_seller = null;
    }
    $winner_card_number = $_POST['winner_card_number'];
    if (empty($winner_card_number)) {
        $winner_card_number = null;
    }
    $winner_number_1 = $_POST['winner_number_1'];
    if (empty($winner_number_1)) {
        $winner_number_1 = null;
    }
    $winner_number_2 = $_POST['winner_number_2'];
    if (empty($winner_number_2)) {
        $winner_number_2 = null;
    }

    // Validate input
    if (empty($name)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('INSERT INTO Price (series_id, name, sponsor, sequence, winner_number, winner_name, winner_birthyear, winner_location, winner_seller, winner_card_number, winner_number_1, winner_number_2, winner_company) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('issiisissiiis', $_POST['series_id'], $name, $sponsor, $sequence, $winnerNumber, $winner_name, $winner_birthyear, $winner_location, $winner_seller, $winner_card_number, $winner_number_1, $winner_number_2, $winner_company);
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
