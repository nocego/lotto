<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

include_once('../config/db.php');
require_once('../simplexlsxgen/SimpleXLSXGen.php');

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
    $fileName = "series.xlsx";

    $data = [
        ['<b>Seriename</b>', '<b>Seriemodus</b>', '<b>Preisreihenfolge</b>', '<b>Preisname</b>', '<b>Preissponsor</b>', '<b>Sieger Name</b>', '<b>Sieger Geburtsjahr</b>', '<b>Sieger Wohnort</b>', '<b>Sieger Firma</b>', '<b>Sieger Verkäufer</b>', '<b>Sieger Kartennummer</b>', '<b>Sieger Zahl 1</b>', '<b>Sieger Zahl 2</b>']
    ];

    $sql = "SELECT ID FROM Series where lotto_id = ".$lottoId;
    $seriesOfThisLottoResult = $conn->query($sql);
    $seriesIds = [];
    if ($seriesOfThisLottoResult->num_rows > 0) {
        while($row = $seriesOfThisLottoResult->fetch_assoc()) {
            $seriesIds[] = $row['ID'];
        }
    }
    $idsString = implode(',', $seriesIds);

    $sql = "SELECT Series.name as `Seriesname`, Series.mode as `Seriesmode`, Price.sequence, Price.name as `Pricename`, Price.sponsor, Price.winner_name, Price.winner_birthyear, Price.winner_location, Price.winner_company, Price.winner_seller, Price.winner_card_number, Price.winner_number_1, Price.winner_number_2  FROM Price INNER JOIN Series on Series.ID = Price.series_id where series_id in (".$idsString.") ORDER BY series_id ASC, sequence ASC";
    $winnersResult = $conn->query($sql);
    if ($winnersResult->num_rows > 0) {
        while($row = $winnersResult->fetch_assoc()) {
            $data[] = [$row['Seriesname'], $row['Seriesmode'], $row['sequence'], $row['Pricename'], $row['sponsor'], $row['winner_name'], $row['winner_birthyear'], $row['winner_location'], $row['winner_company'], $row['winner_seller'], $row['winner_card_number'], $row['winner_number_1'], $row['winner_number_2']];
        }
    }
    \Shuchkin\SimpleXLSXGen::fromArray($data)->saveAs($fileName);

    // download file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename='.$fileName);
    header('Content-Length: ' . filesize($fileName));
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    readfile($fileName);
    exit();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
