<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

include_once('../config/db.php'); // Include your database connection

$lottoId = $_GET['lotto_id'];
if (!isset($lottoId) || $lottoId == "") {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name FROM Lotto where ID = ".$lottoId . " and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $headers = ["Serie", "Preis", "Sponsor", "Sieger Name", "Sieger Geburtsjahr", "Sieger Wohnort", "Sieger Verkäufer", "Sieger Kartennummer", "Sieger Zahl 1", "Sieger Zahl 2"];

    $sql = "SELECT ID FROM Series where lotto_id = ".$lottoId;
    $seriesOfThisLottoResult = $conn->query($sql);
    $seriesIds = [];
    if ($seriesOfThisLottoResult->num_rows > 0) {
        while($row = $seriesOfThisLottoResult->fetch_assoc()) {
            $seriesIds[] = $row['ID'];
        }
    }
    $idsString = implode(',', $seriesIds);

    $sql = "SELECT Series.name as `Seriesname`, Price.name as `Pricename`, Price.sponsor, Price.winner_name, Price.winner_birthyear, Price.winner_location, Price.winner_seller, Price.winner_card_number, Price.winner_number_1, Price.winner_number_2  FROM Price INNER JOIN Series on Series.ID = Price.series_id where series_id in (".$idsString.") and winner_name is not null ORDER BY series_id ASC, sequence ASC";
    $winnersResult = $conn->query($sql);
    $list = [];
    if ($winnersResult->num_rows > 0) {
        while($row = $winnersResult->fetch_assoc()) {
            $list[] = [$row['Seriesname'], $row['Pricename'], $row['sponsor'], $row['winner_name'], $row['winner_birthyear'], $row['winner_location'], $row['winner_seller'], $row['winner_card_number'], $row['winner_number_1'], $row['winner_number_2']];
        }
    }

    $csvName = "winners.csv";
    $fileHandle = fopen($csvName, 'w');

    // Add the headers
    fputcsv($fileHandle, $headers);

    // Add the data
    foreach ($list as $fields) {
        fputcsv($fileHandle, $fields);
    }

    fclose($fileHandle);

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename='.$csvName);
    header('Pragma: no-cache');
    header('Expires: 0');
    readfile($csvName);
    exit();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
