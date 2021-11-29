<?php

$con = mysqli_connect("localhost", "root", "", "yeticave");

require_once('helpers.php');

$isAuth = rand(0, 1);
$userName = 'IgorGrinev'; // укажите здесь ваше имя

$sql = "SELECT name, symbol_code FROM categories";
$result = mysqli_query($con, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT lots.id,
            lots.name            as name,
            lots.image_url            as image,
            lots.initial_price        as price,
            categories.name      as category,
            lots.completion_date      as finTime
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
    WHERE completion_date > now() ";
$result = mysqli_query($con, $sql);
$units = mysqli_fetch_all($result, MYSQLI_ASSOC);


$title = 'Главная';
$pageContent = include_template('main.php', compact('categories', 'units'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title', 'userName', 'isAuth')
);
print($page);
?>
