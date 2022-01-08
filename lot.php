<?php

$con = mysqli_connect("localhost", "root", "root", "yeticave");
require_once('helpers.php');

$is_auth = rand(0, 1);
$userName = 'IgorGrinev'; // укажите здесь ваше имя

if (!isset($_GET['id'])) {
    header('Location: pages/404.html');
    die();
}

$id = (int)$_GET['id'];
$categories = [];

function checkId(mysqli $con, $id)
{
    $sql = "SELECT id FROM lots WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) == 0) {
        header('Location: pages/404.html');
    }
}

checkId($con, $id);

function getUnit(mysqli $con, $id): array
{
    $sql = "SELECT lots.id,
       lots.name as name,
       lots.image_url,
       lots.completion_date,
       lots.initial_price,
       lots.description,
       categories.name as category
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
        WHERE lots.id = ?";
    $unit = [];
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $unit = $row;
    }
    return $unit;
}

$categories = getCategories($con);

$item = getUnit($con, $id);

$pageContent = include_template('TempLot.php', compact('categories', 'item', 'unit'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title', 'userName', 'isAuth')
);

print($page);
