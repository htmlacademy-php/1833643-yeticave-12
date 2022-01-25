<?php
session_start();
require_once 'helpers.php';
require_once 'db.php';
require_once 'check_err.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {
    header('Location: pages/404.html');
    die();
}

$lotId = initOpenLot($_GET, $_SESSION);
checkId($con, $lotId);

$categories = getCategories($con);

$openLot = openLot($con, $lotId);
if ($openLot === NULL) {
    http_response_code(404);
    exit("Страница с id =" . $lotId . " не найдена.");
}

$openBets = openBets($con, $lotId);

$currentPrice = checkPriceLot($openBets, (int)$openLot['initial_price']);

if (isset($_POST['submit_bet'])) {  //If there is such a field in the POST, then the form has been sent
    $errors = validateBetsForm($openLot, $currentPrice, $_SESSION, $_POST);

    //If validation errors, return to the page for adding a new bet with errors displayed.
    if (!$errors) {
        //If no errors, add a new bet to the database
        $price_bet = $currentPrice + (int)$openLot['bet_step'];
        if ($_POST['cost'] > $price_bet) {
            $price_bet = (int)getPostVal('cost');
        }

        $sql_insert_bet = 'INSERT INTO bets ( amount, users_id, lots_id) VALUES ( ?, ?, ?)';
        $stmt = mysqli_prepare($con, $sql_insert_bet);
        mysqli_stmt_bind_param($stmt, 'iii', $price_bet, $_SESSION['userId'], $id);
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($con);
            exit("Ошибка MySQL: " . $error);
        }

    }
}

if (!isset($errors)) {
    $errors = [];
}
//$userId = (int)$_SESSION['userId'];
$item = getUnit($con, $lotId);
$title = 'Просмотр лота';

$pageContent = include_template('TempLot.php', compact('categories', 'item', 'openLot', 'openBets', 'errors', 'currentPrice'));
$page = include_template('layout.php', compact('categories', 'pageContent', 'title'));

print($page);
