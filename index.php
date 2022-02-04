<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'helpers.php';
require_once 'check_err.php';
require_once 'db.php';
require_once('getwinner.php');

$categories = getCategories($con);
$categoryName = '';
$title = 'Главная';
if (isset($_GET['category'])) {
    $categoryCode = $_GET['category'];
    $categoryName = 'Открытые лоты в категории "' . getCategoryNameByCode($con, $categoryCode) . '"';
    $title = $categoryName;
}

if (isset($categoryCode)) {
    $sql = "SELECT lots.id,
            lots.name            as name,
            lots.image_url            as image,
            lots.initial_price        as price,
            categories.name      as category,
            categories.symbol_code,
            lots.completion_date      as finTime
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
    WHERE completion_date > now() and symbol_code=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $categoryCode);
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($con);
        exit("Ошибка MySQL: " . $error);
    } else {
        $result = mysqli_stmt_get_result($stmt);
        $units = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
} else {
    $sql = "SELECT lots.id,
            lots.name            as name,
            lots.image_url            as image,
            lots.initial_price        as price,
            categories.name      as category,
            categories.symbol_code,
            lots.completion_date      as finTime
        FROM lots
        INNER JOIN categories ON lots.categories_id = categories.id
    WHERE completion_date > now()";
    $result = mysqli_query($con, $sql);
    $units = mysqli_fetch_all($result, MYSQLI_ASSOC);
}


$pageContent = include_template('main.php', compact('categories', 'units', 'categoryName'));
$page = include_template(
    'layout.php',
    compact('categories', 'categories', 'units', 'pageContent', 'title'
    )
);
print($page);
?>
