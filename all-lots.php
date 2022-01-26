<?php
session_start();
require_once 'helpers.php';
require_once 'db.php';

$CategoryId=$_GET['id'];

$sql = "SELECT
       id,
       name,
       symbol_code
FROM categories
where id=?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $CategoryId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$oneCategory = mysqli_fetch_assoc($result);
print_r($oneCategory);

$title = 'Категория '.$oneCategory['name'];
$categories = getCategories($con);

$pageContent = include_template('t-all-lots.php', compact('categories', 'oneCategory'));
$page = include_template('layout.php', compact('categories', 'pageContent', 'title'));

print($page);
