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
    $winner = $_POST['winner'];
    if (empty($winner)) {
        $winner = null;
    }

    // Validate input
    if (empty($name) || empty($sponsor) || empty($id)) {
        die('Please fill in both fields.');
    }

    // Prepare and execute query
    $stmt = $conn->prepare('UPDATE Price SET name = ?, sponsor = ?, winner = ? WHERE ID = ?');
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $bind = $stmt->bind_param('sssi', $name, $sponsor, $winner, $id);
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
