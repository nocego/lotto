<?php

$PageTitle="Bearbeite Karte";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$cardId = $_GET['id'];
include_once('../config/db.php');

$sql = "SELECT * FROM Card where ID = ".$cardId;
$cardResult = $conn->query($sql);
if ($cardResult->num_rows > 0) {
    $cardRow = $cardResult->fetch_assoc();
    $lotto_id = $cardRow["lotto_id"];
} else {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name, date FROM Lotto where ID = ".$lotto_id . " and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

include_once('../layout/header.php');
?>

<style type="text/css">
    .twitter-typeahead {
        width: 100%;
    }
    .tt-menu {
        width: 100%;
        background-color: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .tt-cursor {
        background-color: #86b7fe;
    }
    /*.tt-hint {*/
    /*    display: none;*/
    /*}*/
</style>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Bearbeite Karte</h1>
        </div>
    </div>
    <form id="cardForm" action="/card/handle_update.php" method="post" class="">
        <input type="hidden" name="id" value="<?=$cardId?>">
        <div class="row">
            <input type="hidden" name="lotto_id" value="<?=$lotto_id?>">
            <div class="mb-3 mt-3 col-12 col-md-5">
                <input type="text" class="form-control" id="name" placeholder="Name" name="name" value='<?=$cardRow['name']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-5">
                <input type="text" class="form-control" id="firstname" placeholder="Vorname" name="firstname" value='<?=$cardRow['firstname']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-2">
                <input type="number" class="form-control" id="birthyear" placeholder="Geb" name="birthyear" value='<?=$cardRow['birthyear']?>'>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <input type="text" class="form-control" id="location" placeholder="Wohnort" name="location" value='<?=$cardRow['location']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <input type="text" class="form-control" id="company" placeholder="Firma" name="company" value='<?=$cardRow['company']?>'>
            </div>
            <div class="mb-3 mt-3 col-12">
                <input type="text" class="form-control" id="seller" placeholder="Verkäufer" name="seller" value='<?=$cardRow['seller']?>'>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="card_nr" placeholder="Kartennummer" name="card_nr" value='<?=$cardRow['card_nr']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="number_1" placeholder="Zahl 1" name="number_1" value='<?=$cardRow['number_1']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="number_2" placeholder="Zahl 2" name="number_2" value='<?=$cardRow['number_2']?>' required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <button type="submit" class="btn btn-success">Bearbeiten</button>
            </div>
        </div>
    </form>
</div>

<script>
    function suggestion (field) {
        return new Bloodhound({
            datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                wildcard: '%QUERY',
                url: '/card/suggestion.php?lotto_id=<?=$lotto_id?>&field='+field+'&query=%QUERY',
                transform: response => response
            }
        });
    }

    $('#name').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'name',
            source: suggestion('name'),
        }
    );
    $('#firstname').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'firstname',
            source: suggestion('firstname'),
        }
    );
    $('#birthyear').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'birthyear',
            source: suggestion('birthyear'),
        }
    );
    $('#location').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'location',
            source: suggestion('location'),
        }
    );
    $('#seller').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'seller',
            source: suggestion('seller'),
        }
    );

    $(document).ready(function() {
        $('#cardForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var cardId = $('input[name="id"]').val();
            var cardNr = $('#card_nr').val();
            var numberOne = $('#number_1').val();
            var numberTwo = $('#number_2').val();
            var lottoId = $('input[name="lotto_id"]').val();

            $.ajax({
                url: '/card/validate_card_numbers.php',
                type: 'POST',
                data: {
                    card_id: cardId,
                    card_nr: cardNr,
                    number_1: numberOne,
                    number_2: numberTwo,
                    lotto_id: lottoId
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.valid) {
                        // If valid, submit the form
                        $('#cardForm')[0].submit();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while validating the card number.');
                }
            });
        });
    });
</script>

<?php
include_once('../layout/footer.php');
?>
