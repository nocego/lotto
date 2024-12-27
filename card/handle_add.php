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
    $firstname = $_POST['firstname'];
    $birthyear = $_POST['birthyear'];
    $location = $_POST['location'];
    $seller = $_POST['seller'];
    $cardNr = $_POST['card_nr'];
    $number1 = $_POST['number_1'];
    $number2 = $_POST['number_2'];

    // Validate input
    if (empty($lottoId) || empty($name) || empty($firstname) || empty($birthyear) || empty($location) || empty($cardNr) || empty($number1) || empty($number2)) {
        die('Please fill in both fields.');
    }

    if ($seller == "") {
        $seller = null;
    }

    // Prepare and execute query
    $stmt = $conn->prepare('INSERT INTO Card (lotto_id, card_nr, name, firstname, birthyear, location, seller, number_1, number_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('iississii', $lottoId, $cardNr, $name, $firstname, $birthyear, $location, $seller, $number1, $number2);
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
