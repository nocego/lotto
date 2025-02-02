<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//?>

<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

include_once('../config/db.php');
require_once('../simplexlsxgen/SimpleXLSXGen.php');
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

    $rowsToExport = [];

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
                        $row[5] == "Firma" &&
                        $row[6] == "Verkäufer" &&
                        $row[7] == "Zahl 1" &&
                        $row[8] == "Zahl 2") {
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
                    if ($birthyear == "") {
                        $birthyear = null;
                    }
                    $location = $row[4];
                    $company = $row[5];
                    if ($company == "") {
                        $company = null;
                    }
                    $seller = $row[6];
                    if ($seller == "") {
                        $seller = null;
                    }
                    $number_1 = $row[7];
                    $number_2 = $row[8];

                    // check if card number already exists
                    $sql = "SELECT COUNT(*) as count FROM Card WHERE card_nr = ? AND lotto_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ii', $card_nr, $lottoId);
                    $stmt->execute();
                    $stmt->bind_result($cardNumberCount);
                    $stmt->fetch();
                    $stmt->close();
                    if ($cardNumberCount > 0) {
                        $rowsToExport[] = $row;
                        continue;
                    }

                    // check if number_1 already exists
                    $sql = "SELECT COUNT(*) as count FROM Card WHERE number_1 = ? OR number_2 = ? AND lotto_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iii', $number_1, $number_1, $lottoId);
                    $stmt->execute();
                    $stmt->bind_result($numberOneCount);
                    $stmt->fetch();
                    $stmt->close();
                    if ($numberOneCount > 0) {
                        $rowsToExport[] = $row;
                        continue;
                    }

                    // check if number_2 already exists
                    $sql = "SELECT COUNT(*) as count FROM Card WHERE number_1 = ? OR number_2 = ? AND lotto_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iii', $number_2, $number_2, $lottoId);
                    $stmt->execute();
                    $stmt->bind_result($numberTwoCount);
                    $stmt->fetch();
                    $stmt->close();
                    if ($numberTwoCount > 0) {
                        $rowsToExport[] = $row;
                        continue;
                    }

                    try {
                        // Prepare and execute query
                        $stmt = $conn->prepare('INSERT INTO Card (lotto_id, card_nr, name, firstname, birthyear, location, seller, number_1, number_2, company) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        if ($stmt === false) {
                            die('Prepare failed: ' . htmlspecialchars($conn->error));
                        }

                        $bind = $stmt->bind_param('iississiis', $lottoId, $card_nr, $name, $firstname, $birthyear, $location, $seller, $number_1, $number_2, $company);
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

            if (count($rowsToExport) > 0) {
                $fileName = "not_imported_cards.xlsx";
                $data = [
                    ['<b>Kartennummer</b>', '<b>Name</b>', '<b>Vorname</b>', '<b>Jahrgang</b>', '<b>Wohnort</b>', '<b>Firma</b>', '<b>Verkäufer</b>', '<b>Zahl 1</b>', '<b>Zahl 2</b>']
                ];
                foreach ($rowsToExport as $row) {
                    $data[] = $row;
                }
                \Shuchkin\SimpleXLSXGen::fromArray($data)->saveAs($fileName);
//                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//                header('Content-Disposition: attachment; filename='.$fileName);
//                header('Content-Length: ' . filesize($fileName));
//                header('Content-Transfer-Encoding: binary');
//                header('Cache-Control: must-revalidate');
//                header('Pragma: public');
//                readfile($fileName);
                header('Location: /lotto/view.php?id=' . $lottoId . '&not_all_cards_imported=true');
                return;
            }

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
