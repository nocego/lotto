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

$seriesId = $_POST['series_id'];

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $drawnNumber = $_POST['drawnnumber'];

    // Validate input
    if (empty($drawnNumber)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('INSERT INTO Number (series_id, number) VALUES (?, ?)');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('ii', $seriesId, $drawnNumber);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    } else {
        // check if a player has this number
        if ($seriesRow["mode"] == 1) {
            $sql = "SELECT ID FROM Card where lotto_id = ".$lottoId." and number_1 = ".$drawnNumber." or lotto_id = ".$lottoId." and number_2 = ".$drawnNumber;
        } else if ($seriesRow["mode"] == 2) {
            $sql = "SELECT ID FROM Card where lotto_id = ".$lottoId." and card_nr = ".$drawnNumber;
        } else {
            header('Location: /series/play.php?id=' . $seriesId);
        }
        $winnerResult = $conn->query($sql);
        if ($winnerResult->num_rows > 0) {
            $_SESSION['potential_winner_card_id'] = $winnerResult->fetch_assoc()['ID'];
        }
        header('Location: /series/play.php?id=' . $seriesId);
    }

    $stmt->close();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
