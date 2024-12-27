<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$id = $_GET['lotto_id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$id." and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $date = $row["date"];
} else {
    return null;
}

$field = $_GET['field'];
$searchQuery = $_GET['query'];

$sql = "SELECT DISTINCT ".$field." FROM Card where lotto_id = ".$id." and ".$field." like '".$searchQuery."%'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $flatRows = array_column($rows, $field);
    echo json_encode($flatRows, JSON_UNESCAPED_UNICODE);
    die();
} else {
    echo [];
    die();
}
