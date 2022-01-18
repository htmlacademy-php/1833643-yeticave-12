<?php
require_once('helpers.php');
require_once 'db.php';

$is_auth = rand(0, 1);
$userName = 'IgorGrinev'; // укажите здесь ваше имя

if (!isset($_GET['id'])) {
    header('Location: pages/404.html');
    die();
}

$id = (int)$_GET['id'];
$categories = [];

checkId($con, $id);

$categories = getCategories($con);

$item = getUnit($con, $id);

$pageContent = include_template('TempLot.php', compact('categories', 'item', 'unit'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title', 'userName', 'isAuth')
);

print($page);
