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
include_once('../config/db.php');

$seriesId = $_GET['series_id'];

$sql = "SELECT * FROM Series where ID = ".$seriesId;
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows > 0) {
    $seriesRow = $seriesResult->fetch_assoc();
    $lottoId = $seriesRow["lotto_id"];
} else {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name, date FROM Lotto where ID = ".$lottoId." and enabled = 1";
$lottoResult = $conn->query($sql);
if ($lottoResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $winnerCard = $_GET['card_id'];

    $sql = "SELECT * FROM Price where series_id = ".$seriesRow["ID"]." and winner_name IS NULL ORDER BY sequence ASC";
    $pricesToWinResult = $conn->query($sql);

    $sql = "SELECT * FROM Number where series_id = ".$seriesRow["ID"]." ORDER BY ID DESC LIMIT 1";
    $numberResult = $conn->query($sql);
    $lastDrawnNumberRow = $numberResult->fetch_assoc();
    $lastDrawnNumber = $lastDrawnNumberRow['number'];

    $nextPriceToWinRow = $pricesToWinResult->fetch_assoc();

    if ($nextPriceToWinRow == null) {
        echo "todo: no more prices";
        die();
    } else {
        $priceId = $nextPriceToWinRow['ID'];
    }

    // Validate input
    if (empty($winnerCard)) {
        die('Please fill in both fields.');
    }

    $sql = "SELECT * FROM Card where ID = ".$winnerCard;
    $winnerCardResult = $conn->query($sql);
    if ($winnerCardResult->num_rows > 0) {
        $winnerCardRow = $winnerCardResult->fetch_assoc();
        $winner_name = $winnerCardRow['name'] . " " . $winnerCardRow['firstname'];
        $winner_birthyear = $winnerCardRow['birthyear'];
        $winner_location = $winnerCardRow['location'];
        $winner_company = $winnerCardRow['company'];
        $winner_seller = $winnerCardRow['seller'];
        $winner_card_number = $winnerCardRow['card_nr'];
        $winner_number_1 = $winnerCardRow['number_1'];
        $winner_number_2 = $winnerCardRow['number_2'];

        // Prepare and execute query
        $stmt = $conn->prepare('Update Price set winner_name = ?, winner_birthyear = ?, winner_location = ?, winner_seller = ?, winner_card_number = ?, winner_number_1 = ?, winner_number_2 = ?, winner_number = ?, winner_company = ? where ID = ?');
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $bind = $stmt->bind_param('sissiiiisi', $winner_name, $winner_birthyear, $winner_location, $winner_seller, $winner_card_number, $winner_number_1, $winner_number_2, $lastDrawnNumber, $winner_company, $priceId);
        if ($bind === false) {
            die('Bind param failed: ' . htmlspecialchars($stmt->error));
        }

        $exec = $stmt->execute();
        if ($exec === false) {
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        } else {
            $_SESSION['potential_winner_card_id'] = "";
            header('Location: /series/play.php?id=' . $seriesId);
        }

        $stmt->close();
    }
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
