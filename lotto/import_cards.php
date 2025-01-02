<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

include_once('../config/db.php');
require_once('../simplexlsx/SimpleXLSX.php');

$lottoId = $_POST['lotto_id'];
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file_to_import']) && $_FILES['file_to_import']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file_to_import']['tmp_name'];

        if ($xlsx = \Shuchkin\SimpleXLSX::parse($fileTmpPath)) {

            // Begin transaction
            $conn->begin_transaction();

            foreach ($xlsx->rows() as $index => $row) {
                if ($index == 0) {
                    if (
                        $row[0] == "Kartennummer" &&
                        $row[1] == "Name" &&
                        $row[2] == "Vorname" &&
                        $row[3] == "Jahrgang" &&
                        $row[4] == "Wohnort" &&
                        $row[5] == "Verkäufer" &&
                        $row[6] == "Zahl 1" &&
                        $row[7] == "Zahl 2") {
                        continue;
                    } else {
                        echo "Invalid file format. Nothing was imported.";
                        die();
                    }
                } else {
                    $card_nr = $row[0];
                    $name = $row[1];
                    $firstname = $row[2];
                    $birthyear = $row[3];
                    $location = $row[4];
                    $seller = $row[5];
                    if ($seller == "") {
                        $seller = null;
                    }
                    $number_1 = $row[6];
                    $number_2 = $row[7];

                    try {
                        // Prepare and execute query
                        $stmt = $conn->prepare('INSERT INTO Card (lotto_id, card_nr, name, firstname, birthyear, location, seller, number_1, number_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        if ($stmt === false) {
                            die('Prepare failed: ' . htmlspecialchars($conn->error));
                        }

                        $bind = $stmt->bind_param('iississii', $lottoId, $card_nr, $name, $firstname, $birthyear, $location, $seller, $number_1, $number_2);
                        if ($bind === false) {
                            die('Bind param failed: ' . htmlspecialchars($stmt->error));
                        }

                        $exec = $stmt->execute();
                        if ($exec === false) {
                            throw new Exception($stmt->error);
                        }

                        $stmt->close();
                    } catch (Exception $e) {
                        // Rollback transaction
                        $conn->rollback();
                        die($e->getMessage() . "Es wurde nichts importiert.");
                    }
                }
            }

            $conn->commit();
            header('Location: /lotto/view.php?id=' . $lottoId);
        } else {
            echo \Shuchkin\SimpleXLSX::parseError();
        }
    } else {
        echo 'File upload error.';
    }
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
