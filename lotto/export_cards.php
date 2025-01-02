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
    $fileName = "cards.xlsx";

    $data = [
        ['<b>Kartennummer</b>', '<b>Name</b>', '<b>Vorname</b>', '<b>Jahrgang</b>', '<b>Wohnort</b>', '<b>Verkäufer</b>', '<b>Zahl 1</b>', '<b>Zahl 2</b>']
    ];

    $sql = "SELECT * FROM Card where lotto_id = ".$lottoId;
    $cardsOfThisLottoResult = $conn->query($sql);
    if ($cardsOfThisLottoResult->num_rows > 0) {
        while($row = $cardsOfThisLottoResult->fetch_assoc()) {
            $data[] = [$row['card_nr'], $row['name'], $row['firstname'], $row['birthyear'], $row['location'], $row['seller'], $row['number_1'], $row['number_2']];
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
