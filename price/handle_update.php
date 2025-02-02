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

$priceId = $_POST['id'];

$sql = "SELECT series_id FROM Price where ID = ".$priceId;
$result = $conn->query($sql);
if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}
$priceRow = $result->fetch_assoc();
$seriesId = $priceRow['series_id'];

$sql = "SELECT lotto_id FROM Series where ID = ".$seriesId;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $seriesRow = $result->fetch_assoc();
    $lottoId = $seriesRow["lotto_id"];
} else {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name FROM Lotto where ID = ".$lottoId . " and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
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
    if (empty($name) || empty($id)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('UPDATE Price SET name = ?, sponsor = ?, sequence = ?, winner_number = ?, winner_name = ?, winner_birthyear = ?, winner_location = ?, winner_seller = ?, winner_card_number = ?, winner_number_1 = ?, winner_number_2 = ?, winner_company = ? WHERE ID = ?');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('ssiisissiiisi', $name, $sponsor, $sequence, $winnerNumber, $winner_name, $winner_birthyear, $winner_location, $winner_seller, $winner_card_number, $winner_number_1, $winner_number_2, $winner_company, $id);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    } else {
        // Redirect to a protected page
        header('Location: /price/view.php?series_id='.$seriesId);
    }

    $stmt->close();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
