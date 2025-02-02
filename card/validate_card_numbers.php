<?php

include_once('../config/db.php');

$cardId = $_POST['card_id'];
$cardNr = $_POST['card_nr'];
$numberOne = $_POST['number_1'];
$numberTwo = $_POST['number_2'];
$lottoId = $_POST['lotto_id'];

//echo "cardId: " . $cardId . "<br>";
//echo "cardNr: " . $cardNr . "<br>";
//echo "numberOne: " . $numberOne . "<br>";
//echo "numberTwo: " . $numberTwo . "<br>";
//echo "lottoId: " . $lottoId . "<br>";
//die();

if ($cardId != null) {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE card_nr = ? AND lotto_id = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $cardNr, $lottoId, $cardId);
} else {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE card_nr = ? AND lotto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $cardNr, $lottoId);
}
$stmt->execute();
$stmt->bind_result($cardNumberCount);
$stmt->fetch();
$stmt->close();

if ($cardNumberCount > 0) {
    $conn->close();
    $response = ['valid' => false, 'message' => 'Kartennummer ' . $cardNr . ' existiert bereits.'];
    echo json_encode($response);
    return;
}

if ($cardId != null) {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE (number_1 = ? OR number_2 = ?) AND lotto_id = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $numberOne, $numberOne, $lottoId, $cardId);
} else {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE number_1 = ? OR number_2 = ? AND lotto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $numberOne, $numberOne, $lottoId);
}
$stmt->execute();
$stmt->bind_result($numberOneCount);
$stmt->fetch();
$stmt->close();

if ($numberOneCount > 0) {
    $conn->close();
    $response = ['valid' => false, 'message' => 'Zahl 1 (' . $numberOne . ') existiert bereits.'];
    echo json_encode($response);
    return;
}

if ($cardId != null) {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE (number_1 = ? OR number_2 = ?) AND lotto_id = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $numberTwo, $numberTwo, $lottoId, $cardId);
} else {
    $sql = "SELECT COUNT(*) as count FROM Card WHERE number_1 = ? OR number_2 = ? AND lotto_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $numberTwo, $numberTwo, $lottoId);
}
$stmt->execute();
$stmt->bind_result($numberTwoCount);
$stmt->fetch();
$stmt->close();

if ($numberTwoCount > 0) {
    $conn->close();
    $response = ['valid' => false, 'message' => 'Zahl 2 (' . $numberTwo . ') existiert bereits.'];
    echo json_encode($response);
    return;
}

$conn->close();
$response = ['valid' => true];
echo json_encode($response);
return;

