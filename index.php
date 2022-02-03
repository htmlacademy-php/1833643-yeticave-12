<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';
require_once('getwinner.php');

$categories = getCategories($con);

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
    compact('categories', 'categories', 'units', 'pageContent', 'title'
    )
);
print($page);
?>
