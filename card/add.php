<?php

$PageTitle="Neue Karte";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the lotto_id from the URL
$id = $_GET['lotto_id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$id." and enabled = 1";
$lottoResult = $conn->query($sql);

if ($lottoResult->num_rows < 1) {
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
            <h1>Neue Karte</h1>
        </div>
    </div>
    <form action="/card/handle_add.php" method="post" class="">
        <div class="row">
            <input type="hidden" name="lotto_id" value="<?=$id?>">
            <div class="mb-3 mt-3 col-12 col-md-5">
                <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-5">
                <input type="text" class="form-control" id="firstname" placeholder="Vorname" name="firstname" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-2">
                <input type="number" class="form-control" id="birthyear" placeholder="Geb" name="birthyear" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <input type="text" class="form-control" id="location" placeholder="Wohnort" name="location" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <input type="text" class="form-control" id="seller" placeholder="Verkäufer" name="seller">
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="card_nr" placeholder="Kartennummer" name="card_nr" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="number_1" placeholder="Zahl 1" name="number_1" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-4">
                <input type="number" class="form-control" id="number_2" placeholder="Zahl 2" name="number_2" required>
            </div>
            <div class="mb-3 mt-3 col-12 col-md-6">
                <button type="submit" class="btn btn-success">Erstellen</button>
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
                url: '/card/suggestion.php?lotto_id=<?=$id?>&field='+field+'&query=%QUERY',
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
</script>

<?php
include_once('../layout/footer.php');
?>
