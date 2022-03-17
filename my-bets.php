<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
require_once 'init.php';
global $con;

$userId = (int)$_SESSION['userId'];
$categories = getCategories($con);
$lotsWithMyBets = getAllLotsWithMyBets($con, $userId);
$title = 'Мои ставки';

$pageContent = include_template('t-my-bets.php', compact('categories', 'lotsWithMyBets'));
$page = include_template('layout.php', compact('categories', 'pageContent', 'title'));
print($page);

