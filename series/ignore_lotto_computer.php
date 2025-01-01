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
    $_SESSION['potential_winner_card_id'] = "";
    header('Location: /series/play.php?id=' . $seriesId);
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
