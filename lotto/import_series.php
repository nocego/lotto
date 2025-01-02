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
                        $row[0] == "Seriename" &&
                        $row[1] == "Seriemodus" &&
                        $row[2] == "Preisreihenfolge" &&
                        $row[3] == "Preisname" &&
                        $row[4] == "Preissponsor" &&
                        $row[5] == "Sieger Name" &&
                        $row[6] == "Sieger Geburtsjahr" &&
                        $row[7] == "Sieger Wohnort" &&
                        $row[8] == "Sieger Verkäufer" &&
                        $row[9] == "Sieger Kartennummer" &&
                        $row[10] == "Sieger Zahl 1" &&
                        $row[11] == "Sieger Zahl 2") {
                        continue;
                    } else {
                        echo "Invalid file format. Nothing was imported.";
                        die();
                    }
                } else {
                    $seriesName = $row[0];
                    $seriesMode = $row[1];
                    $priceSequence = $row[2];
                    $priceName = $row[3];
                    $priceSponsor = $row[4];
                    $winnerName = $row[5];
                    if ($winnerName == "") {
                        $winnerName = null;
                    }
                    $winnerBirthYear = $row[6];
                    if ($winnerBirthYear == "") {
                        $winnerBirthYear = null;
                    }
                    $winnerLocation = $row[7];
                    if ($winnerLocation == "") {
                        $winnerLocation = null;
                    }
                    $winnerSeller = $row[8];
                    if ($winnerSeller == "") {
                        $winnerSeller = null;
                    }
                    $winnerCardNumber = $row[9];
                    if ($winnerCardNumber == "") {
                        $winnerCardNumber = null;
                    }
                    $winnerNumber1 = $row[10];
                    if ($winnerNumber1 == "") {
                        $winnerNumber1 = null;
                    }
                    $winnerNumber2 = $row[11];
                    if ($winnerNumber2 == "") {
                        $winnerNumber2 = null;
                    }

                    $sql = "SELECT ID FROM Series WHERE lotto_id = ".$lottoId." AND name = '".$seriesName."' AND mode = ".$seriesMode;
                    $existingSeries = $conn->query($sql);

                    if ($existingSeries->num_rows > 0) {
                        $seriesId = $existingSeries->fetch_assoc()['ID'];
                    } else {
                        try {
                            // Prepare and execute query
                            $stmt = $conn->prepare('INSERT INTO Series (lotto_id, name, mode) VALUES (?, ?, ?)');
                            if ($stmt === false) {
                                die('Prepare failed: ' . htmlspecialchars($conn->error));
                            }

                            $bind = $stmt->bind_param('isi', $lottoId, $seriesName, $seriesMode);
                            if ($bind === false) {
                                die('Bind param failed: ' . htmlspecialchars($stmt->error));
                            }

                            $exec = $stmt->execute();
                            if ($exec === false) {
                                throw new Exception($stmt->error);
                            }

                            $seriesId = $conn->insert_id; // Get the ID of the saved record

                            $stmt->close();
                        } catch (Exception $e) {
                            // Rollback transaction
                            $conn->rollback();
                            die($e->getMessage());
                        }
                    }

                    try {
                        // Prepare and execute query
                        $stmt = $conn->prepare('INSERT INTO Price (series_id, sequence, sponsor, name, winner_name, winner_birthyear, winner_location, winner_seller, winner_card_number, winner_number_1, winner_number_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        if ($stmt === false) {
                            die('Prepare failed: ' . htmlspecialchars($conn->error));
                        }

                        $bind = $stmt->bind_param('iisssissiii', $seriesId, $priceSequence, $priceSponsor, $priceName, $winnerName, $winnerBirthYear, $winnerLocation, $winnerSeller, $winnerCardNumber, $winnerNumber1, $winnerNumber2);
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
                        die($e->getMessage());
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
    echo "down here";
    die();
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
