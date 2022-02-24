<?php
session_start();
require_once 'init.php';
global $con;
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
        $priceBet = $currentPrice + (int)$openLot['bet_step'];
        if ($_POST['cost'] > $priceBet) {
            $priceBet = (int)getPostVal('cost');
        }

        $sql = 'INSERT INTO bets ( amount, users_id, lots_id) VALUES ( ?, ?, ?)';
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'iii', $priceBet, $_SESSION['userId'], $lotId);
        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_error($con);
            exit("Ошибка MySQL: " . $error);
        }

    }
}

if (!isset($errors)) {
    $errors = [];
}
$item = getUnit($con, $lotId);
$title = 'Просмотр лота';

$pageContent = include_template('t-lot.php', compact('categories', 'item', 'openLot', 'openBets', 'errors', 'currentPrice'));
$page = include_template('layout.php', compact('categories', 'pageContent', 'title'));

print($page);
