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
    $sql = "SELECT * FROM Price where series_id = ".$seriesRow["ID"]." and winner IS NULL ORDER BY sequence ASC";
    $pricesToWinResult = $conn->query($sql);

    $nextPriceToWinRow = $pricesToWinResult->fetch_assoc();

    if ($nextPriceToWinRow == null) {
        echo "todo: no more prices";
        die();
    } else {
        $priceId = $nextPriceToWinRow['ID'];
    }

    $sql = "SELECT * FROM Number where series_id = ".$seriesRow["ID"]." ORDER BY ID DESC LIMIT 1";
    $numberResult = $conn->query($sql);
    $lastDrawnNumberRow = $numberResult->fetch_assoc();
    $lastDrawnNumber = $lastDrawnNumberRow['number'];

    // Prepare and execute query
    $stmt = $conn->prepare('Update Price set winner = ?, winner_number = ? where ID = ?');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $str = "Lotto Saal";
    $bind = $stmt->bind_param('sii', $str, $lastDrawnNumber, $priceId);
    if ($bind === false) {
        die('Bind param failed: ' . htmlspecialchars($stmt->error));
    }

    $exec = $stmt->execute();
    if ($exec === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    } else {
        header('Location: /series/play.php?id=' . $seriesId);
    }

    $stmt->close();


    header('Location: /series/play.php?id=' . $seriesId);
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
